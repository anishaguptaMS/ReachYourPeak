-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 15, 2018 at 03:41 PM
-- Server version: 5.6.35
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ryp`
--

-- --------------------------------------------------------

--
-- Table structure for table `cycle`
--

CREATE TABLE `cycle` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `active_flag` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cycle`
--

INSERT INTO `cycle` (`ID`, `name`, `year`, `start_date`, `end_date`, `active_flag`) VALUES
(1, '2018 3 month cycle', 2018, '2018-06-07', '2018-09-06', 'E'),
(2, '2018 6 month cycle', 2018, '2018-05-01', '2018-12-27', 'E');

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `ID` int(11) NOT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `body` text,
  `from_email` varchar(250) NOT NULL,
  `reply_to` varchar(250) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(30) NOT NULL DEFAULT 'DRAFT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`ID`, `subject`, `body`, `from_email`, `reply_to`, `date_added`, `status`) VALUES
(11, 'Hello there', 'What a body', 'donotreply@mountsinai.org', 'donotreply@mountsinai.org', '2018-05-11 17:19:36', 'DRAFT'),
(12, 'No subject', '', 'donotreply@mountsinai.org', 'donotreply@mountsinai.org', '2018-05-11 20:05:27', 'DRAFT'),
(13, 'Hello world', 'My world', 'donotreply@mountsinai.org', 'donotreply@mountsinai.org', '2018-05-11 20:12:37', 'DRAFT'),
(14, 'Reach your peak - Please verify your email', 'Dear Hans Solo,\r\n\r\nVerify your email here.\r\n\r\nhttp://localhost/ryp/resetPassword.html?link=a4cfff2f82da85e81bb59f671dc8bb1d\r\n\r\nThanks\r\n\r\nReach your peak.\r\n\r\n(Do not reply to this email)\r\n\r\n', 'donotreply@mountsinai.org', 'donotreply@mountsinai.org', '2018-05-11 20:22:23', 'DRAFT'),
(15, 'Reach your peak - Please verify your email', 'Dear Hans Solo,\r\n\r\nVerify your email here.\r\n\r\nhttp://localhost/ryp/resetPassword.html?link=fda2217a3921c464be73975603df7510\r\n\r\nThanks\r\n\r\nReach your peak.\r\n\r\n(Do not reply to this email)\r\n\r\n', 'donotreply@mountsinai.org', 'donotreply@mountsinai.org', '2018-05-11 20:24:37', 'DRAFT');

-- --------------------------------------------------------

--
-- Table structure for table `email_targets`
--

CREATE TABLE `email_targets` (
  `ID` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `target_type` varchar(10) NOT NULL,
  `email_list` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `email_targets`
--

INSERT INTO `email_targets` (`ID`, `email_id`, `target_type`, `email_list`) VALUES
(3, 11, 'BCC', '1@gmail.com;2@gmail.com;3@gmail.com;4@gmail.com;5@gmail.com;6@gmail.com;7@gmail.com;8@gmail.com;9@gmail.com;10@gmail.com;11@gmail.com;12@gmail.com;13@gmail.com;14@gmail.com;15@gmail.com;16@gmail.com;17@gmail.com;18@gmail.com;19@gmail.com;20@gmail.com;21@gmail.com;22@gmail.com;23@gmail.com;24@gmail.com'),
(4, 12, 'BCC', 'harry.andree@mssm.edu;B.@@gamil.com'),
(5, 13, 'BCC', 'harry.andree@mssm.edu;B.@@gamil.com'),
(6, 14, 'To', 'Hans.solo@stw.com'),
(7, 15, 'To', 'Hans.solo1@stw.com');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`ID`, `name`) VALUES
(1, 'Icahn School of Medicine'),
(3, 'MS Beth Israel'),
(6, 'MS Brooklyn Too Expensivce'),
(7, 'MS Corporate'),
(10, 'MS Queens'),
(5, 'MS St. Luke\'s'),
(2, 'MS West'),
(9, 'New York Eye and Ear'),
(4, 'Offsite Physician Practice'),
(8, 'The Mount Sinai Hospital');

-- --------------------------------------------------------

--
-- Table structure for table `mail_list`
--

CREATE TABLE `mail_list` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sql` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mail_list`
--

INSERT INTO `mail_list` (`ID`, `name`, `sql`) VALUES
(1, 'Captains', 'select u.email, u.name from users u\r\njoin role_members m on u.id = m.user_id\r\njoin roles r on r.id = m.role_id\r\njoin team_cycles tc on tc.id = m.team_id\r\njoin cycle c on tc.cycle_id = c.id\r\njoin team t on t.id = tc.team_id where r.name = \'Captain\''),
(2, 'Admin', 'select u.email, u.name from users u \r\njoin role_members m on u.id = m.user_id \r\njoin roles r on r.id = m.role_id \r\nwhere r.name = \'Admin\''),
(3, 'Participants', 'select u.email, u.name from users u\r\njoin role_members m on u.id = m.user_id\r\njoin roles r on r.id = m.role_id\r\njoin team_cycles tc on tc.id = m.team_id\r\njoin cycle c on tc.cycle_id = c.id\r\njoin team t on t.id = tc.team_id');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`ID`, `name`) VALUES
(1, 'Admin'),
(2, 'Captain'),
(3, 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `role_members`
--

CREATE TABLE `role_members` (
  `ID` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_members`
--

INSERT INTO `role_members` (`ID`, `role_id`, `user_id`, `team_id`) VALUES
(1, 1, 5, NULL),
(7, 3, 6, 1),
(13, 1, 8, NULL),
(20, 3, 5, 0),
(21, 3, 5, 5),
(22, 2, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `browser_string` varchar(256) DEFAULT NULL,
  `session_start` date DEFAULT NULL,
  `session_cookie` varchar(100) DEFAULT NULL,
  `session_type` varchar(20) DEFAULT NULL,
  `session_ip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`ID`, `user_id`, `browser_string`, `session_start`, `session_cookie`, `session_type`, `session_ip`) VALUES
(1, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-03', 'bfebf2c52000d2bac1a130520ace5dd7', 'Permanent', '::1'),
(2, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-08', '7436b0dc99f8aed11026252aeade1a3a', '', '::1'),
(3, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-08', '0801dafc88d3117e0db830a42ddf944c', 'Permanent', '::1'),
(4, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-09', 'bf4d73f316737b26f1e860da0ea63ec8', '', '::1'),
(5, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-09', '6aa3f7e91ef087b5e9f647e25241745f', 'Permanent', '::1'),
(6, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-10', '1ffcb5b752250faafdbeed38ec2cbcc4', '', '::1'),
(7, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-10', 'c64a9829fa4638ff5de86330dd227e35', '', '::1'),
(8, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-10', 'eb4ccb5a339da7a1f01b8f9688896b65', 'Permanent', '::1'),
(9, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-11', '57b01adc7eb0a085a9eed546e5b0f617', '', '::1'),
(10, 5, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36', '2018-05-11', '837e868ffbb3a67451e480e1864e071d', 'Permanent', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `steps`
--

CREATE TABLE `steps` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `steps` int(11) DEFAULT NULL,
  `step_date` date DEFAULT NULL,
  `date_entered` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `steps`
--

INSERT INTO `steps` (`ID`, `user_id`, `steps`, `step_date`, `date_entered`) VALUES
(15, 5, 34636, '2018-05-08', '2018-05-15'),
(16, 5, 523, '2018-05-09', '2018-05-15'),
(17, 5, 235, '2018-05-10', '2018-05-15'),
(18, 5, 46, '2018-05-11', '2018-05-15'),
(19, 5, 35, '2018-05-12', '2018-05-15'),
(20, 5, 37, '2018-05-13', '2018-05-15');

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `active_flag` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`ID`, `name`, `location_id`, `active_flag`) VALUES
(1, 'Genesis', NULL, 'E'),
(2, 'Avengers', 6, 'A'),
(3, 'Heros', 2, 'E'),
(4, 'Dumbos', 1, 'E');

-- --------------------------------------------------------

--
-- Table structure for table `team_cycles`
--

CREATE TABLE `team_cycles` (
  `ID` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `cycle_id` int(11) NOT NULL,
  `active_flag` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `team_cycles`
--

INSERT INTO `team_cycles` (`ID`, `team_id`, `cycle_id`, `active_flag`) VALUES
(1, 2, 2, 'A'),
(4, 3, 1, 'E'),
(5, 4, 2, 'E');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `email` varchar(128) NOT NULL,
  `name` varchar(150) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `verified_flag` varchar(1) DEFAULT NULL,
  `last_logged_in` date DEFAULT NULL,
  `active_flag` varchar(1) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `pwd_reset_link` varchar(100) DEFAULT NULL,
  `reset_link_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `email`, `name`, `password`, `verified_flag`, `last_logged_in`, `active_flag`, `location_id`, `pwd_reset_link`, `reset_link_date`) VALUES
(5, 'harry.andree@mssm.edu', 'Harry Andree', '04b56b75cc01b3c0c0fbba9c1ee9953d', 'Y', NULL, 'A', 2, '28c1e04ceae885ace553dd756e8e54bb', '2018-05-10'),
(6, 'piet.puk@gmail.com', 'Piet Puk', NULL, 'Y', NULL, 'A', 7, 'f84b89bcf33c161bdaa19c925928f74d', '2018-05-08'),
(7, 'Snow.White@disney.com', 'Snow White', NULL, 'Y', NULL, 'A', 4, NULL, NULL),
(8, 'B.@@gamil.com', 'Bryan', 'ac1ef17c2db40995e9fdd40b04a5a649', 'Y', NULL, 'A', 9, NULL, '2018-05-10'),
(11, 'Hans.solo@stw.com', 'Hans Solo', NULL, 'N', NULL, NULL, 10, 'a4cfff2f82da85e81bb59f671dc8bb1d', '2018-05-11'),
(12, 'Hans.solo1@stw.com', 'Hans Solo', NULL, 'N', NULL, NULL, 10, 'fda2217a3921c464be73975603df7510', '2018-05-11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cycle`
--
ALTER TABLE `cycle`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `email_targets`
--
ALTER TABLE `email_targets`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `mail_list`
--
ALTER TABLE `mail_list`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `mail_list_name_idx` (`name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_members`
--
ALTER TABLE `role_members`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `steps`
--
ALTER TABLE `steps`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `team_cycles`
--
ALTER TABLE `team_cycles`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cycle`
--
ALTER TABLE `cycle`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `email_targets`
--
ALTER TABLE `email_targets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `mail_list`
--
ALTER TABLE `mail_list`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `role_members`
--
ALTER TABLE `role_members`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `steps`
--
ALTER TABLE `steps`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `team_cycles`
--
ALTER TABLE `team_cycles`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
