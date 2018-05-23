var pageData = {},
	currentPage,
    msInDay =  86400000;
    

function parseJSONToOptions(json, field) {
	var out = [];
	var parts = field.split('.');
	for(var i=0; i<json.data.length; i++) {
		var o = {};		
		var rec = json.data[i];
		o.value = rec['DT_RowId'].replace('row_', '');
		if(parts.length > 1) {
			o.label = rec[parts[0]][parts[1]];
		} else {
			o.label = json.data[i][field];
		}
		out.push(o);
	}
	return out;
}

function getValue(elem){
	var tag=elem.prop('tagName');
	var name=elem.attr('name');
	if(tag=='INPUT'){
		if(elem.attr('type')=='radio'){
			return $("input:radio[name="+name+"]:checked").val();
		}else if(elem.attr('type')=='checkbox'){
			var cb = $("input:checkbox[name="+name+"]");
			if(cb.prop('checked')) return cb.val();
			if(cb.attr('no-val')) { console.log(name + " N" ); return cb.attr('no-val'); }
			return null;
		}else{
			return elem.val();
		}
	}else if(tag=='SELECT'||tag=='TEXTAREA'){
		return elem.val();
	}else if(tag=='SPAN'){
		return elem.text();
	}else{
		if(elem.attr('value')) return elem.attr('value');
		else return null;
	}
}

function setValue(elem,value){
	var tag=elem.prop('tagName');
	var name=elem.attr('name');
	if(tag=='INPUT'){
		if(elem.attr('type')=='radio'){
			$("input:radio[name="+name+"]").val([value]);
			$("input:radio[name="+name+"][value="+value+"]").change();
		}else if(elem.attr('type')=='checkbox'){
			$("input:checkbox[name="+name+"]").val([value]);
		}else{
			elem.val(value);
		}
	}else if(tag=='SELECT'||tag=='TEXTAREA'){
		elem.val(value);
	}else if(tag=='SPAN'){
		elem.text(value);
	} else {
		elem.attr('value',value);
		if(elem.data('setdata'))elem.data('setdata')();
	}
	if(elem.change)	elem.change();
}


function pageLoaded(data){
	initModels();
	$('form').submit(function (evt) {
		evt.preventDefault();
	});
	if(data.callback) data.callback(data || {});
}

function getInputValues(modelName) {
	var d = {};
	$('[model=' + modelName+']').each(function() {
		d[$(this).attr('name')] = getValue($(this));
	});
	return d;
}

function setInputValues(modelName, d) {
	$('[model=' + modelName+']').each(function() {
		var name = $(this).attr('name');
		if(d[name]) setValue($(this), d[name]);
	});
}

function initModels() {
	
}

function convertLinks(){
	$('a').each(function() {
		var href=$(this).attr('href');
		if(href && href.indexOf('#&') >= 0) {
			$(this).off('click');
			$(this).click(function(e) {
				loadPage(href);
				e.preventDefault();
			});
		}
	});
}

function setPage(html, data) {
	$('#main').html(html);
	$('#main').css('display', 'block');
	convertLinks();
	pageLoaded(data || {});
}

function createCellPos( n ){
    var ordA = 'A'.charCodeAt(0);
    var ordZ = 'Z'.charCodeAt(0);
    var len = ordZ - ordA + 1;
    var s = "";
 
    while( n >= 0 ) {
        s = String.fromCharCode(n % len + ordA) + s;
        n = Math.floor(n / len) - 1;
    }
 
    return s;
}


function  excelButton() {
	 return {
            extend: 'excelHtml5',
            text: '<i class="fa fa-excel"></i> Excel',
            customize: function( xlsx ) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                var lastCol = sheet.getElementsByTagName('col').length - 1;
                var colRange = createCellPos( lastCol ) + '1';
                //Has to be done this way to avoid creation of unwanted namespace atributes.
                var afSerializer = new XMLSerializer();
                var xmlString = afSerializer.serializeToString(sheet);
                var parser = new DOMParser();
                var xmlDoc = parser.parseFromString(xmlString,'text/xml');
                var xlsxFilter = xmlDoc.createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main','autoFilter');
                var filterAttr = xmlDoc.createAttribute('ref');
                filterAttr.value = 'A1:' + colRange;
                xlsxFilter.setAttributeNode(filterAttr);
                sheet.getElementsByTagName('worksheet')[0].appendChild(xlsxFilter);
            }
        }
}

function loadPage(href, data, options) {
	$('#main').css('display', 'block');
	$('#main').css('display', 'none');
	if(options && options.requireSession && ! getCookie('sessionKey')) {
		noValidSession();
		return false;
	}
	$.ajax({
		'url': href.replace('#&', ''),
		'type': 'GET',
		'cache' : false,
		'data' : ( data ? data : null ),
		'success' : function(html){
			currentPage = href;
			if(data) {
				pageData[href] = data;
			}
			setPage(html, data);
		},
		error: function (request, status, error) {
			errorMSG(request.responseText);
		}
	});
}

function getCurrentPageData() {
	return pageData[currentPage];
}



function setLoginButton(hasSession) {
	if(hasSession) {
		$('#loginLink').text('Log out');
	} else {
		$('#loginLink').text('Log in');
	}
}

function checkSession(callback) {
	if(getCookie('sessionKey')) {
		$.ajax({
			'url' : 'db/checkSession.php',
			'type' : 'GET',
			'dataType' : 'json'
		}).success(function(json) {
			if(json.result == 'OK') {
				if(json.data['hasSession']) {
                    $('#users_name').text(json.data['name']);
                    if(callback) callback(true);
				} else {
                    $('#users_name').text('Not logged in');
                    loadPage("login.html");
					message("Your session has expired.");
				}
			} else {
				errorMSG(json.error);
			}
		});
	} else {
        if(callback) callback(false);
	}
	setTimeout(function() { checkSession(); }, 600000); // every 10 minutes
}


function logout() {
    $.ajax({
       'url' : 'db/logout.php',
       'type' : 'POST',
       'dataType' : 'json'
    }).success(function(json) {
       message("You have logged out");
       $('#users_name').text('Not logged in');
    });
    clearSession();
    $('#users_name').text('Not logged in');
    loadPage("login.html");
}

function setSession(email, sessionKey, id) {
    var sessionTimeOut = null;
    if($('#keepLoggedIn').is(':checked')) {
        sessionTimeOut = 180
    }
    setCookie('sessionKey', sessionKey, sessionTimeOut);
    setCookie('email', email, sessionTimeOut);                
    setCookie('userid', id, sessionTimeOut);                  
}

function clearSession() {
    setCookie('sessionKey', '');
	setCookie('email', '');                
    setCookie('userid', '');                  
}


function loginSubmit() {
	var data = getInputValues('login');
	if(!data.password) {
		errorMSG("Password is required");
		return false;
	}	
	if(!data.email) {
		errorMSG("Email is required");
		return false;
	}
	$.ajax({
		'url' : 'db/login.php',
		'data' : data,
		'dataType' : 'json',
		'type' : 'POST'		
	}).success(function(json) {
		if(json.result == 'OK') {
			message(json.message);
			if(json.data.sessionKey) {
                setSession(data.email, json.data.sessionKey, json.data.id)
				setLoginButton(true);
                $('#users_name').text(json.data['name']);
                loadPage("menu.html");                
                
			} else {
                errorMSG("unexpected error: 10001");
                loadPage("login.html");                
            }
		} else {
			errorMSG(json.error);
            clearSession();
            loadPage("login.html");                            
            
		}
		$('#passwordInput').val('');		
	});
}

function noValidSession() {
    errorMSG("No valid session");
    clearSession();
    loadPage('login.html');
}

function getWorkbooks(name, id) {
	loadPage('showWorkbooks.php?name='+name+'&id='+id);
}

function showWorksheets(name, id) {
	loadPage('showWorksheets.php?name='+name+'&id='+id);
}

function errorMSG(error) {
	$('#modalSaveButton').hide();
	$('.modal-title').addClass('error');
	$('.modal-title').html('<span class="error"><i class="fa fa-exclamation-circle"></i> Error</span>');
	$('.modal-body').html(error);
	setTimeout(function() { $('#myModal').modal({}) }, 200);
}

function message(msg) {
	$('#modalSaveButton').hide();
	$('.modal-title').removeClass('error');
	$('.modal-title').text('Message');
	$('.modal-body').html(msg);
	setTimeout(function() { $('#myModal').modal({});  }, 200);
}

function getDateStr(d) {
    return (d.getMonth() +  1) + '/' + d.getDate() + '/' + d.getFullYear();
}

function addDays(d, days) {
    return new Date(d.getTime() + days * msInDay);
}

function getDateMySQL(s) {
    var ar = s.split('-');
    return new Date(ar[0], ar[1] - 1, ar[2]);
}

function getDate(s) {
    var ar = s.split('/');
    return new Date(ar[2], ar[0] - 1, ar[1]);
}


function getDayOfWeek(d) {
    var days = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fr', 'Sat'];
    return days[d.getDay()];
    
}

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

function gup( name, url ) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    return results == null ? null : results[1];
}

$.urlParam = function(name) {
	var results = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
	if(results) {
		return results[1];
	} else {
		if(currentPage) {
			var results = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(currentPage);
			return results[1];
		}
	}
	return null;
}


function addDataTable(json) {
	var conf = {
		dom: 'Bfrtip',
		data: json.data,
		columns: json.columns,
		'buttons' : [ 'excel' ]
	}
	
	if(json.editor) {
		var editor = new $.fn.dataTable.Editor( {
			ajax: json.editor.url,
			table: '#projects' +json['id'],
			fields: json.editor.fields,
			//idSrc: 'DT_RowId'
		});
		conf.select = true;
		conf.lengthChange = false;		
		conf.buttons.push({ extend: 'create', editor: editor });
		conf.buttons.push({ extend: 'edit',   editor: editor });
		conf.buttons.push({ extend: 'remove', editor: editor });		
	}
		
//	$('#debug').html(JSON.stringify(json));
	

	return $('#projects' + json['id']).DataTable(conf); 
}

function addDataTableFromID(id) {
	$.ajax({
		'url' : '/db/getData.php',
		'data' : { 'id' : id },
		'dataType' : 'json',
		'type' : 'GET'
	}).success(function(json) {
		addDataTable(json.data);
	});
}

function getColNr(name, res) {
	for(var i=0; i<res.columns.length; i++) {
		if(res.columns[i].title.toUpperCase() == name.toUpperCase()) return i;
	}
	return false;
}

function registerSubmit() {
    var data = getInputValues('register');
    $.ajax({
        'url' : 'db/register.php',
        'data' : data,
        'dataType' : 'json',
        'type' : 'POST',
    }).success(function(json) {
        if(json.result == 'OK') {
            alert(json.message);
        } else {
            alert("There was an error");
        }
    })
}

function resetPassword() {
    var data = getInputValues('resetPwd');
    if(data['password'].length < 8 || data['password'].length > 20)  {
        errorMSG('The password needs to be between 8 and 20 characters long');
        return false;
    }
   
    if(data['password'] == data['password2']) {
        $.ajax({
            'url' : 'db/resetPwd.php',
            'data' : data,
            'dataType' : 'json',
            'type' : 'POST',
        }).success(function(json) {
            if(json.result == 'OK') {
                message(json.message);
                loadPage('menu.html');
            } else {
                errorMSG(json.error);
            }
        })    
    } else {
        errorMSG("The passwords do not match!")
    }
}

function sendPassword() {
    var data = getInputValues('login');
    if(! data['email']) {
        errorMSG("You need to provide the email where to send the link to.");
        return false;
    }
    
    $.ajax({
        'url' : 'db/sendPwd.php',
        'data' : data,
        'dataType' : 'json',
        'type' : 'POST',
    }).success(function(json) {
        if(json.result == 'OK') {
            message(json.message);
        } else {
            errorMSG(json.error);
        }
    })   
}



function addTeamMember() {
    var data = getInputValues('addMember');
    $.ajax({
        'url' : 'db/addTeamMember.php',
        'type' : 'POST',
        'data' : data,
        'dataType' : 'json'
    }).success(function(json) {
        if(json.result == 'OK') {
            if(teamMemberTable) teamMemberTable.ajax.reload();
        } else {
            
        }
    });    
}

function getLocations(callback) {
    $.ajax({
        'url' : 'db/allLocations.php',
        'dataType' : 'json',
        'type' : 'GET'
    }).success(function(json) {
        if(json.result == 'OK') {
            callback(json.data);
        }
    });
}

function renderEditButton(p) {
    var s = '<button onclick="loadPage(\'' + p.page +  "', {'requiresSession' : true, 'id' : " + p.id + 
        ", 'action' : 'edit' })" + '")><i class="fa fa-pencil-alt" title="Edit"></i></button>'; 
    return s;
}

function requiresSession() {
    if(! getCookie('sessionKey')) {
        loadPage('login.html');
    }
}

function objSort(data, sortArray) {
    return data.sort(
        function(a, b) {
            for(var i=0; i<sortArray.length; i++) {
                if(a[sortArray[i]].toUpperCase() > b[sortArray[i]].toUpperCase()) return 1;
                if(a[sortArray[i]].toUpperCase() < b[sortArray[i]].toUpperCase()) return -11;
            }
            return 0;
        }
    );
}

function userSearchWidget(p) {
    $.ajax({
        'url' : 'db/userSearch.php',
        'type' : 'GET', 
        'dataType' : 'json',
        'data' : { 'search' : p['search'] }
    }).success(function(json) {
        if(json.result == 'OK') {
            var s = '<table class="table table-striped table-bordered" cellspacing="0" width="100%">';
            for(var i=0; i<json.data.length; i++) {
                var rec = json.data[i];
                var r = "<tr><td>" + rec['name'] + '</td><td>' + rec['email'] + '<td><td><i class="fa fa-plus-square" onclick="' +p['callback'] + 
                   '(' + rec['ID'] + ')"></i></td></tr>';
                s += r;
            }
            s += '</table>';
            $('#' + p['div']).html(s);
        }
    });
}

