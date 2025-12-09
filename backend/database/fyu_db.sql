-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 07:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fyu_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'HT Mathiang', 'hontap.cs@gmail.com', '$2y$10$KjJgFHLUg63Umowr3Fu4QOij/40WJ/KDxWrmmBRfjAIditOJ0iwIu', '2025-10-19 05:33:07'),
(4, 'HT', 'admin@example.com', '$2y$10$CKrFxw/qOmqkYYjlHQ3qf.CIkoPpzV40TgDEN22eMj3A7vNQK3j/.', '2025-12-01 13:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `body`, `starts_at`, `ends_at`, `is_published`, `created_at`) VALUES
(1, 'General Assembly', 'A youth initiative is a program or project that engages young people, often by providing opportunities for skill development, community involvement, and leadership. Examples include government-funded programs like the European Union\'s Youth Employment Initiative, which supports job training, and international efforts such as UNESCO\'s Global Youth Initiative, which focuses on educational reform. Other initiatives are local or specific, such as those creating community youth centers, promoting environmental projects, or focusing on human rights.', '2025-12-15 00:00:00', '2025-12-15 00:00:00', 1, '2025-12-04 17:29:21');

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `comment_body` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` (`id`, `post_id`, `user_name`, `user_email`, `comment_body`, `created_at`) VALUES
(1, 3, 'HT', 'hontmathiang@gmail.com', 'Nice one', '2025-12-04 15:53:31'),
(2, 3, 'HT', 'hontmathiang@gmail.com', 'Hello', '2025-12-04 15:53:53'),
(3, 3, 'Nyajal', 'nyajal@gmail.com', 'Congratulations', '2025-12-04 15:55:04'),
(5, 5, 'KT', 'tk@gmail.com', 'Nice work. It is good to be the first', '2025-12-08 18:13:55');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subheading` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `subheading`, `author`, `content`, `description`, `image`, `category`, `created_at`) VALUES
(3, 'Message of Appreciation:-To Both Kuol Nyang , Chairman of Nuer youth Union', 'The Emergency flood fundraising Response High level committee extends its heartfelt appreciation to Mr. Both Kuol Nyang', NULL, NULL, 'Our esteemed Chairman, for his generous contribution of $1,000 towards the Emergency Flood Fundraising Response.\r\nYour act of leadership and compassion reflects the true spirit of unity and service that defines our Union. At a time when our communities are facing great challenges, your personal sacrifice stands as an inspiration to all Nuer youth—reminding us that leadership is not only about position but about action and love for one’s people.\r\nThe Emergency Flood fundraising Response High level committee proudly acknowledges your unwavering commitment and generosity. Your support will go a long way in restoring hope and providing relief to those affected by the floods.\r\nThank you, Chairman Kuol Nyang, for leading by example and uplifting our collective mission.\r\nEng. Kojile Samuel Gai Chuol \r\nSecretary for Information, \r\nEmergency flood Response High level Committee & Nuer youth Union .', 'Both-kuol.jpg', NULL, '2025-11-06 08:58:05'),
(5, 'FYU Launches Community Cleanup Initiative', 'A step toward a greener Fangak', 'John Kuol', 'The Fangak Youth Union (FYU) is organizing a community-wide cleanup event this Saturday. Volunteers will gather at the central market at 8 AM to distribute supplies and divide into teams. The initiative aims to promote environmental awareness, foster community spirit, and improve public spaces. Snacks and refreshments will be provided. Everyone is welcome to join and contribute to a cleaner, greener Fangak.', 'FYU members are joining hands this weekend to clean up local neighborhoods and educate the public on environmental care.', 'Games.jpg', 'Community', '2025-12-04 08:57:26'),
(6, 'Save Life, Pay it Forward', 'Life under water, case: Fangak', 'Mawich Duoth', '<p><strong style=\"background-color: rgb(31, 31, 31); color: rgb(230, 232, 240);\"><em>Life under the water&nbsp;</em></strong><strong><em>includes a vast array of marine ecosystems and species that are crucial for the planet\'s health</em></strong><strong style=\"background-color: rgb(31, 31, 31); color: rgb(230, 232, 240);\"><em>, as oceans cover over 70% of the Earth and are home to an estimated 1.5 million species. This life produces at least 50% of the planet\'s oxygen and is a vital food source for billions of people. However, underwater life is under significant threat from pollution, overfishing, and climate change, necessitating urgent conservation efforts.&nbsp;</em></strong></p>', 'Life under the water includes a vast array of marine ecosystems and species that are crucial for the planet\'s health, as oceans cover over 70% of the Earth and are home to an estimated 1.5 million species. This life produces at least 50% of the planet\'s oxygen and is a vital food source for billions of people. However, underwater life is under significant threat from pollution, overfishing, and climate change, necessitating urgent conservation efforts.', 'Games.jpg', 'Advocacy', '2025-12-04 17:02:54'),
(7, 'FANGAK YOUTH UNION (FYU) Press Brief', 'Peace, Unity & Development', 'Chris Bamuom Gai, Secretary for Information and Publicity, FYU.', '<p>In just 22 days, the campaign mobilized SSP 76.6 million and USD 5,100 in pledges, with SSP 53.7 million already collected. Funds were used to procure and dispatch 2.31 tons of Non-Food Items (NFIs) including clothes, mosquito nets, medicines, and plastic sheets  to Fangak County, which has already arrived in Toch few days ago.</p><p>The Union reported total expenditure of SSP 53.59 million, mainly on supplies, logistics, and communication, leaving a balance of SSP 111,190. Outstanding pledges worth SSP 44.14 million and USD 1,500 remain to be collected for the next phase.</p><p> “This effort shows the power of unity and compassion among our people,”</p><p>FYU pledged continued transparency, regular updates, and further delivery of relief to affected families in Fangak</p>', 'The Fangak Youth Union (FYU) has presented its Interim Financial and Operational Report to the High-Level Task Force Committee following the successful launch  and completion of the Emergency Fangak Flood Relief Fundraising on 19th September 2025 in Juba.', '1765130354_FYUNews.jpg', 'Announcement', '2025-12-07 19:59:14'),
(10, 'Press Brief', 'Peace, Unity & Development', 'Chris Bamuom Gai, Secretary for Information and Publicity, FYU.', '<p>In just 22 days, the campaign mobilized SSP 76.6 million and USD 5,100 in pledges, with SSP 53.7 million already collected. Funds were used to procure and dispatch 2.31 tons of Non-Food Items (NFIs) including clothes, mosquito nets, medicines, and plastic sheets  to Fangak County, which has already arrived in Toch few days ago.</p><p>The Union reported total expenditure of SSP 53.59 million, mainly on supplies, logistics, and communication, leaving a balance of SSP 111,190. Outstanding pledges worth SSP 44.14 million and USD 1,500 remain to be collected for the next phase.</p><p> “This effort shows the power of unity and compassion among our people,”</p><p>FYU pledged continued transparency, regular updates, and further delivery of relief to affected families in Fangak.</p><p>Chris Bamuom Gai, Secretary for Information and Publicity, FYU.</p><p>Contact: fangakyouthunion2025@gmail.com | +211 920 494 545/+211929770743</p>', 'The Fangak Youth Union (FYU) has presented its Interim Financial and Operational Report to the High-Level Task Force Committee following the successful launch  and completion of the Emergency Fangak Flood Relief Fundraising on 19th September 2025 in Juba', 'Screenshot2025-12-08164319.jpg', 'Announcement', '2025-12-09 08:03:12');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts_archive`
--

CREATE TABLE `blog_posts_archive` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `subheading` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Upcoming','Ongoing','Completed') DEFAULT 'Upcoming',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `project_id`, `title`, `event_date`, `description`, `location`, `start_date`, `end_date`, `status`, `image`, `created_at`) VALUES
(1, 3, 'Fundraising Ceremony', '2026-12-15', 'This function is meant for the flood crisis response', 'Fangak', '2025-12-15', '2026-01-15', 'Upcoming', 'evt_69327eac815ed5.86093403.jpg', '2025-12-05 06:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `age` int(11) NOT NULL,
  `payam` varchar(100) NOT NULL,
  `education_level` enum('Not in School','Primary','Secondary','Undergraduate','Graduate','Others') NOT NULL,
  `course` varchar(255) DEFAULT NULL,
  `year_or_done` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `full_name`, `email`, `phone`, `gender`, `age`, `payam`, `education_level`, `course`, `year_or_done`, `photo`, `status`, `created_at`, `updated_at`, `approved_by`) VALUES
(3, 'Gai Gatjang', 'gaikang@gamil.com', '0925555555', 'Male', 29, 'Barbouy', 'Secondary', 'Arts', NULL, 'public/uploads/members/member_1764672094_c35f8d0c.jpg', 'active', '2025-12-02 10:41:34', '2025-12-06 15:38:48', NULL),
(4, 'John Puol', 'jpuol@gmail.com', '0923333333', 'Male', 28, 'Manajang', 'Secondary', 'Arts', NULL, 'public/uploads/members/member_1764961439_888ac2a8.png', 'active', '2025-12-05 19:03:59', '2025-12-06 15:28:50', NULL),
(5, 'Nyaluak Jeremiah', 'nyaj@gmail.com', '0922244444', 'Female', 19, 'Toch', 'Undergraduate', NULL, '1/2027', 'public/uploads/members/member_1765046949_6e12b4a9.png', 'active', '2025-12-06 18:49:09', '2025-12-06 18:49:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `sender_email` varchar(100) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('New','Current','Finished') DEFAULT 'New',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `featured` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `image`, `status`, `start_date`, `end_date`, `created_at`, `featured`, `sort_order`) VALUES
(2, 'Empowering Fangak Families through Emergency Relief Distribution', 'In late 2025, Fangak County faced one of the most severe flood seasons in recent memory, submerging villages, destroying farmland, and displacing hundreds of families. Recognizing the urgent need for coordinated relief, the Fangak Youth Union (FYU) mobilized local volunteers and partner organizations to distribute life-saving supplies to the most affected communities.\r\n\r\nThe project titled “Empowering Fangak Families through Emergency Relief Distribution” focused on providing essential aid — including food packages, clean drinking water, mosquito nets, hygiene kits, and emergency shelter materials. Over 350 households across Toch, Old Fangak, and New Fangak Payams received direct support through this coordinated effort.\r\n\r\nThe initiative was not just about aid delivery, but about community empowerment. FYU worked hand-in-hand with local chiefs, women’s groups, and youth representatives to ensure fairness, transparency, and accountability in the distribution process. Volunteers were trained to assess needs, maintain beneficiary records, and ensure that priority was given to vulnerable groups such as orphans, elderly persons, and mothers with young children.\r\n\r\nTransportation remained one of the toughest challenges — with many villages accessible only by canoe due to the flooded roads. Despite these obstacles, FYU teams persisted, using local boats and manpower to reach isolated areas that larger NGOs could not access. The success of this project highlighted the resilience and solidarity of Fangak’s youth and their ability to lead in crisis response when given the right support.\r\n\r\nLooking ahead, FYU aims to expand the project beyond emergency aid, moving toward recovery and resilience-building. Plans are underway to initiate a “Flood Resilience Fund” that will support small-scale farming, tree planting, and community-based disaster preparedness training. This project has not only provided immediate relief but also strengthened the foundation for a more self-reliant and unified Fangak community.', 'uploads/projects/Campus_1762266727_0856e20675d5.jpg', 'New', '2025-11-20', '2025-11-30', '2025-11-04 14:32:07', 0, 0),
(3, 'Reviving Education: Rebuilding Schools in Flood-Affected Fangak', 'Following the devastating floods that swept through Fangak County, dozens of schools were submerged, leaving thousands of children without classrooms or learning materials. The aftermath saw a sharp decline in school attendance, with many students forced to abandon their education altogether. In response, the Fangak Youth Union (FYU) launched the Reviving Education initiative — a community-based project aimed at reconstructing safe, accessible, and sustainable learning spaces for the youth of Fangak.\r\n\r\nThis project focuses on rebuilding three key primary schools across New Fangak, Toch, and Old Fangak areas — regions most affected by the prolonged flooding. The reconstruction involves not just repairing classrooms, but also elevating school grounds to prevent future flood damage, installing solar lighting for evening study sessions, and creating proper sanitation facilities to support health and hygiene for both students and teachers.\r\n\r\nTo ensure sustainability, FYU is integrating locally sourced materials and employing local artisans, empowering the community with both income and ownership of the rebuilding process. Beyond infrastructure, the project includes a “Back to Learning” campaign, providing essential supplies such as uniforms, textbooks, and school kits to over 500 children. Teachers displaced by the floods are also being re-engaged through temporary housing support and capacity-building workshops organized in collaboration with the County Education Department.\r\n\r\nBy restoring schools, FYU hopes to reignite hope and ambition in the hearts of young learners who have endured years of hardship. Education is not just a service — it’s a lifeline. Every new classroom symbolizes resilience, and every returning student represents a future leader in the making.\r\n\r\nThe Reviving Education project stands as a powerful reminder that rebuilding Fangak’s future begins with rebuilding its schools.', 'uploads/projects/Ludo_1762267118_342790515aa1.jpeg', 'New', '2025-11-15', '2025-11-15', '2025-11-04 14:38:38', 0, 0),
(11, 'gfyhb', 'lreghj yhbmokok bvggyokol nvb yg huhhf ghkmk', 'proj_69351ccaefb5b1.10854844.jpg', 'Current', '2025-12-09', '2025-12-10', '2025-12-07 06:20:22', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT '',
  `contact_email` varchar(255) DEFAULT '',
  `logo` varchar(255) DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `created_at`) VALUES
(1, 'htmathiang@gmail.com', '2025-11-09 07:52:48'),
(3, 'hontmathiang@gmail.com', '2025-11-09 07:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'HT Mathiang', 'hontap.cs@gmail.com', '$2y$10$O4jGSC76G2K6PM8wAgUea.T7lCMmfXQEaOI1chPWXgXb6wO1wFtiy', 'admin', '2025-10-19 05:43:50'),
(5, 'Gatluak James Biel', 'gatluakbiel@gmail.com', '$2y$10$8aidvH02KVBCknIHTrbrlOnuJD1v4/8uySv5nIJVigf7wI38w80bO', 'member', '2025-11-07 05:09:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts_archive`
--
ALTER TABLE `blog_posts_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `blog_posts_archive`
--
ALTER TABLE `blog_posts_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
