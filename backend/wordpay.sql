-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2019 at 09:52 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manish_wordpay`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `cpd_id` int(11) UNSIGNED NOT NULL COMMENT 'company domain id',
  `company_id` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_vat` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`cpd_id`, `company_id`, `name`, `domain`, `key`, `secret_key`, `apply_vat`, `status`, `used_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'bibhash', 'www.bluethink.in', '20644ceffe95a2595ef2fd0e8d4078e4', '4d48ff75e0442e2aee8f62559c0de90f', 10, 1, NULL, '2019-02-21 08:13:32', '2019-02-21 08:13:32', NULL),
(2, 1, 'bibhash', 'www.bluethink.in', 'b35b3c95d416cc52c3d77f8f75ca5039', '96f1c9a774fc5355d537cc35b2c38d0b', 10, 1, NULL, '2019-02-21 08:15:40', NULL, NULL),
(3, 1, 'bibhash', 'www.bluethink.in', 'ff70bf633557b1db88ebcbf18cc14709', 'f7bf13b36b07bfd51bfc8c718c9e5480', 10, 1, NULL, '2019-02-23 01:58:27', NULL, NULL),
(4, 1, 'bibhash', 'www.bluethink.in', '05903da86d2250277228517f915ccba3', '17d7b225c850253a0d126fa935c08927', 1, 1, '2019-02-23 03:22:29', '2019-02-23 03:01:52', '2019-02-23 03:22:29', NULL),
(5, 1, 'bibhash', 'www.bluethink.in', '14427ae1ebb1dc37cdf9b3dabe683a0f', '8bd6e0b31f332c471faab91e7acdbe4d', 1, 1, NULL, '2019-02-23 03:04:22', NULL, NULL),
(6, 1, 'bibhash', 'www.bluethink.in', 'a1705f89d114fd83008d288063fa70e3', '08f10d3f7850aa89018de43f10d26837', 1, 1, NULL, '2019-02-23 03:09:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `article_id` int(11) UNSIGNED NOT NULL,
  `cpd_id` int(11) NOT NULL COMMENT 'company domain id',
  `wordpress_article_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wordpress_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wordpress_post_id` int(11) NOT NULL,
  `wordpress_post_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wordpress_max_price` double(10,2) NOT NULL,
  `wordpress_min_price` double(10,2) NOT NULL,
  `wordpress_fixed_coins` int(11) NOT NULL,
  `visitors` int(11) NOT NULL DEFAULT '0',
  `signups` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`article_id`, `cpd_id`, `wordpress_article_type`, `wordpress_title`, `wordpress_post_id`, `wordpress_post_url`, `wordpress_max_price`, `wordpress_min_price`, `wordpress_fixed_coins`, `visitors`, `signups`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Article', 'Test', 1001, 'http://wordpay.com/test', 5.00, 2.50, 2, 1, 0, '2019-02-23 02:57:32', NULL, NULL),
(2, 1, 'Article', 'Test', 1002, 'http://wordpay.com/test', 5.00, 2.50, 2, 1, 1, '2019-02-23 02:57:40', '2019-02-23 02:57:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `article_transactions`
--

CREATE TABLE `article_transactions` (
  `article_transaction_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `cpd_id` int(11) NOT NULL COMMENT 'company domain id',
  `wordpress_article_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wordpress_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wordpress_post_id` int(11) NOT NULL,
  `wordpress_post_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wordpress_max_price` double(10,2) NOT NULL,
  `wordpress_min_price` double(10,2) NOT NULL,
  `coins_used` double(10,2) DEFAULT '0.00',
  `coins_balance` double(10,2) NOT NULL DEFAULT '0.00',
  `purchased_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_transactions`
--

INSERT INTO `article_transactions` (`article_transaction_id`, `user_id`, `cpd_id`, `wordpress_article_type`, `wordpress_title`, `wordpress_post_id`, `wordpress_post_url`, `wordpress_max_price`, `wordpress_min_price`, `coins_used`, `coins_balance`, `purchased_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Article', 'Test', 1001, 'http://wordpay.com/test', 5.00, 2.50, 1.50, 0.00, NULL, '2019-02-23 02:57:32', NULL, NULL),
(2, 1, 1, 'Article', 'Test', 1002, 'http://wordpay.com/test', 5.00, 2.50, 1.50, 995.50, '2019-02-23 02:57:50', '2019-02-23 02:57:40', '2019-02-23 02:57:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `bank_id` int(11) UNSIGNED NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `swift_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`bank_id`, `company_id`, `user_id`, `bank_name`, `iban_number`, `swift_code`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 2, 77, 'sbi', '222', '4444', 1, '2019-02-23 02:15:50', '2019-02-23 02:18:18', NULL),
(3, 2, 77, 'sbi', '222', '4444', 1, '2019-02-23 02:16:14', '2019-02-23 02:16:14', NULL),
(4, 2, 77, 'sbi', '222', '4444', 1, '2019-02-23 03:00:52', NULL, NULL),
(5, 2, 77, 'sbi', '222', '4444', 1, '2019-02-23 03:01:03', '2019-02-23 03:01:37', '2019-02-23 03:01:37');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `card_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_on` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cvc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`card_id`, `user_id`, `card_type`, `number`, `expired_on`, `cvc`, `first_name`, `last_name`, `address`, `city`, `state`, `postal_code`, `country`, `phone`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 20, 'text', '8888888888', '12/20', '222', 'xyz', 'yxz', 'noida', 'noida', 'noida', '11111', 'india', '4444444444', 1, '2019-02-21 08:25:51', '2019-02-23 02:20:52', NULL),
(2, 2, 'rupay', '22222222222', '12/20', '222', 'ramayan', 'kumar', 'gaya', 'city', 'delhi', '824025', 'india', '2222222222', 127, '2019-02-23 02:07:03', '2019-02-23 03:00:41', '2019-02-23 03:00:41'),
(3, 2, 'rupay', '22222222222', '12/20', '222', 'ramayan', 'kumar', 'gaya', 'city', 'delhi', '824025', 'india', '2222222222', 127, '2019-02-23 02:07:36', '2019-02-23 02:07:36', NULL),
(4, 2, 'rupay', '22222222222', '12/20', '222', 'ramayan', 'kumar', 'gaya', 'city', 'delhi', '824025', 'india', '2222222222', 1, '2019-02-23 02:20:17', '2019-02-23 02:20:17', NULL),
(5, 2, 'rupay', '22222222222', '12/20', '222', 'ramayan', 'kumar', 'gaya', 'city', 'delhi', '824025', 'india', '2222222222', 1, '2019-02-23 02:58:31', '2019-02-23 02:58:31', NULL),
(6, 20, 'text', '8888888888', '12/20', '222', 'xyz', 'yxz', 'noida', 'noida', 'noida', '11111', 'india', '4444444444', 1, '2019-02-23 02:59:04', '2019-02-23 03:00:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `name`, `email`, `vat_number`, `address`, `city`, `state`, `postal_code`, `country_id`, `phone`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Wordpay', 'info@wordpay.com', '1231231231231', 'Burari1', 'New Delhi1', 'Delhi1', '1100841', '121', '88265609291', '2019-02-17 18:30:00', '2019-02-21 07:17:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `vat` decimal(10,0) DEFAULT NULL,
  `currancy` varchar(255) DEFAULT NULL,
  `currancy_symbol` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_name`, `vat`, `currancy`, `currancy_symbol`) VALUES
(1, 'Afghanistan', NULL, NULL, NULL),
(2, 'A…land Islands', '100', 'Rs', 'R'),
(3, 'Albania', NULL, NULL, NULL),
(4, 'Algeria', NULL, NULL, NULL),
(5, 'American Samoa', NULL, NULL, NULL),
(6, 'Andorra', NULL, NULL, NULL),
(7, 'Angola', NULL, NULL, NULL),
(8, 'Anguilla', NULL, NULL, NULL),
(9, 'Antarctica', NULL, NULL, NULL),
(10, 'Antigua and Barbuda', NULL, NULL, NULL),
(11, 'Argentina', NULL, NULL, NULL),
(12, 'Armenia', NULL, NULL, NULL),
(13, 'Aruba', NULL, NULL, NULL),
(14, 'Australia', NULL, NULL, NULL),
(15, 'Austria', NULL, NULL, NULL),
(16, 'Azerbaijan', NULL, NULL, NULL),
(17, 'Bahamas', NULL, NULL, NULL),
(18, 'Bahrain', NULL, NULL, NULL),
(19, 'Bangladesh', NULL, NULL, NULL),
(20, 'Barbados', NULL, NULL, NULL),
(21, 'Belarus', NULL, NULL, NULL),
(22, 'Belgium', NULL, NULL, NULL),
(23, 'Belize', NULL, NULL, NULL),
(24, 'Benin', NULL, NULL, NULL),
(25, 'Bermuda', NULL, NULL, NULL),
(26, 'Bhutan', NULL, NULL, NULL),
(27, 'Bolivia (Plurinational State of)', NULL, NULL, NULL),
(28, 'Bonaire, Sint Eustatius and Saba', NULL, NULL, NULL),
(29, 'Bosnia and Herzegovina', NULL, NULL, NULL),
(30, 'Botswana', NULL, NULL, NULL),
(31, 'Bouvet Island', NULL, NULL, NULL),
(32, 'Brazil', NULL, NULL, NULL),
(33, 'British Indian Ocean Territory', NULL, NULL, NULL),
(34, 'United States Minor Outlying Islands', NULL, NULL, NULL),
(35, 'Virgin Islands (British)', NULL, NULL, NULL),
(36, 'Virgin Islands (U.S.)', NULL, NULL, NULL),
(37, 'Brunei Darussalam', NULL, NULL, NULL),
(38, 'Bulgaria', NULL, NULL, NULL),
(39, 'Burkina Faso', NULL, NULL, NULL),
(40, 'Burundi', NULL, NULL, NULL),
(41, 'Cambodia', NULL, NULL, NULL),
(42, 'Cameroon', NULL, NULL, NULL),
(43, 'Canada', NULL, NULL, NULL),
(44, 'Cabo Verde', NULL, NULL, NULL),
(45, 'Cayman Islands', NULL, NULL, NULL),
(46, 'Central African Republic', NULL, NULL, NULL),
(47, 'Chad', NULL, NULL, NULL),
(48, 'Chile', NULL, NULL, NULL),
(49, 'China', NULL, NULL, NULL),
(50, 'Christmas Island', NULL, NULL, NULL),
(51, 'Cocos (Keeling) Islands', NULL, NULL, NULL),
(52, 'Colombia', NULL, NULL, NULL),
(53, 'Comoros', NULL, NULL, NULL),
(54, 'Congo', NULL, NULL, NULL),
(55, 'Congo (Democratic Republic of the)', NULL, NULL, NULL),
(56, 'Cook Islands', NULL, NULL, NULL),
(57, 'Costa Rica', NULL, NULL, NULL),
(58, 'Croatia', NULL, NULL, NULL),
(59, 'Cuba', NULL, NULL, NULL),
(60, 'CuraÃ§ao', NULL, NULL, NULL),
(61, 'Cyprus', NULL, NULL, NULL),
(62, 'Czech Republic', NULL, NULL, NULL),
(63, 'Denmark', NULL, NULL, NULL),
(64, 'Djibouti', NULL, NULL, NULL),
(65, 'Dominica', NULL, NULL, NULL),
(66, 'Dominican Republic', NULL, NULL, NULL),
(67, 'Ecuador', NULL, NULL, NULL),
(68, 'Egypt', NULL, NULL, NULL),
(69, 'El Salvador', NULL, NULL, NULL),
(70, 'Equatorial Guinea', NULL, NULL, NULL),
(71, 'Eritrea', NULL, NULL, NULL),
(72, 'Estonia', NULL, NULL, NULL),
(73, 'Ethiopia', NULL, NULL, NULL),
(74, 'Falkland Islands (Malvinas)', NULL, NULL, NULL),
(75, 'Faroe Islands', NULL, NULL, NULL),
(76, 'Fiji', NULL, NULL, NULL),
(77, 'Finland', NULL, NULL, NULL),
(78, 'France', NULL, NULL, NULL),
(79, 'French Guiana', NULL, NULL, NULL),
(80, 'French Polynesia', NULL, NULL, NULL),
(81, 'French Southern Territories', NULL, NULL, NULL),
(82, 'Gabon', NULL, NULL, NULL),
(83, 'Gambia', NULL, NULL, NULL),
(84, 'Georgia', NULL, NULL, NULL),
(85, 'Germany', NULL, NULL, NULL),
(86, 'Ghana', NULL, NULL, NULL),
(87, 'Gibraltar', NULL, NULL, NULL),
(88, 'Greece', NULL, NULL, NULL),
(89, 'Greenland', NULL, NULL, NULL),
(90, 'Grenada', NULL, NULL, NULL),
(91, 'Guadeloupe', NULL, NULL, NULL),
(92, 'Guam', NULL, NULL, NULL),
(93, 'Guatemala', NULL, NULL, NULL),
(94, 'Guernsey', NULL, NULL, NULL),
(95, 'Guinea', NULL, NULL, NULL),
(96, 'Guinea-Bissau', NULL, NULL, NULL),
(97, 'Guyana', NULL, NULL, NULL),
(98, 'Haiti', NULL, NULL, NULL),
(99, 'Heard Island and McDonald Islands', NULL, NULL, NULL),
(100, 'Holy See', NULL, NULL, NULL),
(101, 'Honduras', NULL, NULL, NULL),
(102, 'Hong Kong', NULL, NULL, NULL),
(103, 'Hungary', NULL, NULL, NULL),
(104, 'Iceland', NULL, NULL, NULL),
(105, 'India', NULL, NULL, NULL),
(106, 'Indonesia', NULL, NULL, NULL),
(107, 'Iran (Islamic Republic of)', NULL, NULL, NULL),
(108, 'Iraq', NULL, NULL, NULL),
(109, 'Ireland', NULL, NULL, NULL),
(110, 'Isle of Man', NULL, NULL, NULL),
(111, 'Israel', NULL, NULL, NULL),
(112, 'Italy', NULL, NULL, NULL),
(113, 'Jamaica', NULL, NULL, NULL),
(114, 'Japan', NULL, NULL, NULL),
(115, 'Jersey', NULL, NULL, NULL),
(116, 'Jordan', NULL, NULL, NULL),
(117, 'Kazakhstan', NULL, NULL, NULL),
(118, 'Kenya', NULL, NULL, NULL),
(119, 'Kiribati', NULL, NULL, NULL),
(120, 'Kuwait', NULL, NULL, NULL),
(121, 'Kyrgyzstan', NULL, NULL, NULL),
(122, 'Latvia', NULL, NULL, NULL),
(123, 'Lebanon', NULL, NULL, NULL),
(124, 'Lesotho', NULL, NULL, NULL),
(125, 'Liberia', NULL, NULL, NULL),
(126, 'Libya', NULL, NULL, NULL),
(127, 'Liechtenstein', NULL, NULL, NULL),
(128, 'Lithuania', NULL, NULL, NULL),
(129, 'Luxembourg', NULL, NULL, NULL),
(130, 'Macao', NULL, NULL, NULL),
(131, 'Macedonia (the former Yugoslav Republic of)', NULL, NULL, NULL),
(132, 'Madagascar', NULL, NULL, NULL),
(133, 'Malawi', NULL, NULL, NULL),
(134, 'Malaysia', NULL, NULL, NULL),
(135, 'Maldives', NULL, NULL, NULL),
(136, 'Mali', NULL, NULL, NULL),
(137, 'Malta', NULL, NULL, NULL),
(138, 'Marshall Islands', NULL, NULL, NULL),
(139, 'Martinique', NULL, NULL, NULL),
(140, 'Mauritania', NULL, NULL, NULL),
(141, 'Mauritius', NULL, NULL, NULL),
(142, 'Mayotte', NULL, NULL, NULL),
(143, 'Mexico', NULL, NULL, NULL),
(144, 'Micronesia (Federated States of)', NULL, NULL, NULL),
(145, 'Moldova (Republic of)', NULL, NULL, NULL),
(146, 'Monaco', NULL, NULL, NULL),
(147, 'Mongolia', NULL, NULL, NULL),
(148, 'Montenegro', NULL, NULL, NULL),
(149, 'Montserrat', NULL, NULL, NULL),
(150, 'Morocco', NULL, NULL, NULL),
(151, 'Mozambique', NULL, NULL, NULL),
(152, 'Myanmar', NULL, NULL, NULL),
(153, 'Namibia', NULL, NULL, NULL),
(154, 'Nauru', NULL, NULL, NULL),
(155, 'Nepal', NULL, NULL, NULL),
(156, 'Netherlands', NULL, NULL, NULL),
(157, 'New Caledonia', NULL, NULL, NULL),
(158, 'New Zealand', NULL, NULL, NULL),
(159, 'Nicaragua', NULL, NULL, NULL),
(160, 'Niger', NULL, NULL, NULL),
(161, 'Nigeria', NULL, NULL, NULL),
(162, 'Niue', NULL, NULL, NULL),
(163, 'Norfolk Island', NULL, NULL, NULL),
(164, 'Northern Mariana Islands', NULL, NULL, NULL),
(165, 'Norway', NULL, NULL, NULL),
(166, 'Oman', NULL, NULL, NULL),
(167, 'Pakistan', NULL, NULL, NULL),
(168, 'Palau', NULL, NULL, NULL),
(169, 'Palestine, State of', NULL, NULL, NULL),
(170, 'Panama', NULL, NULL, NULL),
(171, 'Papua New Guinea', NULL, NULL, NULL),
(172, 'Paraguay', NULL, NULL, NULL),
(173, 'Peru', NULL, NULL, NULL),
(174, 'Philippines', NULL, NULL, NULL),
(175, 'Pitcairn', NULL, NULL, NULL),
(176, 'Poland', NULL, NULL, NULL),
(177, 'Portugal', NULL, NULL, NULL),
(178, 'Puerto Rico', NULL, NULL, NULL),
(179, 'Qatar', NULL, NULL, NULL),
(180, 'Republic of Kosovo', NULL, NULL, NULL),
(181, 'RÃ©union', NULL, NULL, NULL),
(182, 'Romania', NULL, NULL, NULL),
(183, 'Russian Federation', NULL, NULL, NULL),
(184, 'Rwanda', NULL, NULL, NULL),
(185, 'Saint BarthÃ©lemy', NULL, NULL, NULL),
(186, 'Saint Helena, Ascension and Tristan da Cunha', NULL, NULL, NULL),
(187, 'Saint Kitts and Nevis', NULL, NULL, NULL),
(188, 'Saint Lucia', NULL, NULL, NULL),
(189, 'Saint Martin (French part)', NULL, NULL, NULL),
(190, 'Saint Pierre and Miquelon', NULL, NULL, NULL),
(191, 'Saint Vincent and the Grenadines', NULL, NULL, NULL),
(192, 'Samoa', NULL, NULL, NULL),
(193, 'San Marino', NULL, NULL, NULL),
(194, 'Sao Tome and Principe', NULL, NULL, NULL),
(195, 'Saudi Arabia', NULL, NULL, NULL),
(196, 'Senegal', NULL, NULL, NULL),
(197, 'Serbia', NULL, NULL, NULL),
(198, 'Seychelles', NULL, NULL, NULL),
(199, 'Sierra Leone', NULL, NULL, NULL),
(200, 'Singapore', NULL, NULL, NULL),
(201, 'Sint Maarten (Dutch part)', NULL, NULL, NULL),
(202, 'Slovakia', NULL, NULL, NULL),
(203, 'Slovenia', NULL, NULL, NULL),
(204, 'Solomon Islands', NULL, NULL, NULL),
(205, 'Somalia', NULL, NULL, NULL),
(206, 'South Africa', NULL, NULL, NULL),
(207, 'South Georgia and the South Sandwich Islands', NULL, NULL, NULL),
(208, 'Korea (Republic of)', NULL, NULL, NULL),
(209, 'South Sudan', NULL, NULL, NULL),
(210, 'Spain', NULL, NULL, NULL),
(211, 'Sri Lanka', NULL, NULL, NULL),
(212, 'Sudan', NULL, NULL, NULL),
(213, 'Suriname', NULL, NULL, NULL),
(214, 'Svalbard and Jan Mayen', NULL, NULL, NULL),
(215, 'Swaziland', NULL, NULL, NULL),
(216, 'Sweden', NULL, NULL, NULL),
(217, 'Switzerland', NULL, NULL, NULL),
(218, 'Syrian Arab Republic', NULL, NULL, NULL),
(219, 'Taiwan', NULL, NULL, NULL),
(220, 'Tajikistan', NULL, NULL, NULL),
(221, 'Tanzania, United Republic of', NULL, NULL, NULL),
(222, 'Thailand', NULL, NULL, NULL),
(223, 'Timor-Leste', NULL, NULL, NULL),
(224, 'Togo', NULL, NULL, NULL),
(225, 'Tokelau', NULL, NULL, NULL),
(226, 'Tonga', NULL, NULL, NULL),
(227, 'Trinidad and Tobago', NULL, NULL, NULL),
(228, 'Tunisia', NULL, NULL, NULL),
(229, 'Turkey', NULL, NULL, NULL),
(230, 'Turkmenistan', NULL, NULL, NULL),
(231, 'Turks and Caicos Islands', NULL, NULL, NULL),
(232, 'Tuvalu', NULL, NULL, NULL),
(233, 'Uganda', NULL, NULL, NULL),
(234, 'Ukraine', NULL, NULL, NULL),
(235, 'United Arab Emirates', NULL, NULL, NULL),
(236, 'United Kingdom of Great Britain and Northern Ireland', NULL, NULL, NULL),
(237, 'United States of America', NULL, NULL, NULL),
(238, 'Uruguay', NULL, NULL, NULL),
(239, 'Uzbekistan', NULL, NULL, NULL),
(240, 'Vanuatu', NULL, NULL, NULL),
(241, 'Venezuela (Bolivarian Republic of)', NULL, NULL, NULL),
(242, 'Viet Nam', NULL, NULL, NULL),
(243, 'Wallis and Futuna', NULL, NULL, NULL),
(244, 'Western Sahara', NULL, NULL, NULL),
(245, 'Yemen', NULL, NULL, NULL),
(246, 'Zambia', NULL, NULL, NULL),
(247, 'Zimbabwe', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_02_16_155127_cards', 1),
(9, '2019_02_17_062615_create_api_keys', 1),
(10, '2019_02_17_075330_create_company', 2),
(11, '2019_02_17_082459_add_company_in_user', 2),
(14, '2019_02_18_100050_add_vat_in_api_keys', 3),
(15, '2019_02_18_122820_create_bank_table', 4),
(16, '2019_02_20_080653_add_domain_in_users', 5),
(17, '2019_02_20_083224_add_user_login_log', 6),
(21, '2019_02_20_100441_create_package', 7),
(24, '2019_02_20_102940_create_articles', 8),
(26, '2019_02_20_112613_user_coin_logs', 9),
(31, '2019_02_20_113549_update_articles_table', 10),
(32, '2019_02_20_113824_add_coin_in_users', 10);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('d005778391feb7e48f64160e8996d313fef5e5bb927789a4ea87291d99a9a74df237b140d7a49afc', 1, 1, 'wordpay', '[]', 0, '2019-02-21 07:20:51', '2019-02-21 07:20:51', '2020-02-21 12:50:51');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'c9maMb3oGcoj3IVi060uO8FQNY2hX2Jl5jcp83N3', 'http://localhost', 1, 0, 0, '2019-02-17 01:38:36', '2019-02-17 01:38:36'),
(2, NULL, 'Laravel Password Grant Client', 'HhJbLqewM1fnmmZkCTSJwljLtyPgqJsohJxe6BDp', 'http://localhost', 0, 1, 0, '2019-02-17 01:38:36', '2019-02-17 01:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2019-02-17 01:38:36', '2019-02-17 01:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) UNSIGNED NOT NULL,
  `package_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_type` enum('package','subscription') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'package',
  `currency_id` int(11) NOT NULL,
  `coins` double(10,2) NOT NULL DEFAULT '0.00',
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `discount` double(10,2) NOT NULL DEFAULT '0.00',
  `discount_schedule` datetime NOT NULL,
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `package_type`, `currency_id`, `coins`, `price`, `discount`, `discount_schedule`, `color_code`, `published_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'bibhash', 'package', 1, 2.00, 55555.00, 22.00, '2019-02-22 17:12:03', 'red', NULL, '2019-02-23 01:19:59', '2019-02-23 01:19:59', NULL),
(2, 'text1', 'package', 1, 11.00, 2000.00, 20.00, '2019-02-22 17:12:03', '#41722', NULL, '2019-02-23 01:24:41', '2019-02-23 01:36:06', '2019-02-23 01:36:06'),
(3, 'bibhash', 'package', 1, 2.00, 55555.00, 22.00, '2019-02-22 17:12:03', 'red', '2019-02-22 11:42:03', '2019-02-23 01:43:07', NULL, NULL),
(4, 'bibhash', 'package', 1, 2.00, 55555.00, 22.00, '2019-02-22 17:12:03', '#41722', '2019-02-22 11:42:03', '2019-02-23 01:43:43', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_type` enum('user','media','admin') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance_coins` double(10,2) DEFAULT '1000.00',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `first_name`, `last_name`, `email`, `account_id`, `password`, `address`, `city`, `state`, `postal_code`, `country_id`, `company_id`, `phone`, `domain`, `balance_coins`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'user', 'Ramayan', 'prasad', 'ram.developer89@gmail.com', '467-QBN5AF', '$2y$10$cKzOWLcPf/SWJ4Hrx77wA.5543tS2DRmGoeEWfm1IR3oPgVDDo1QG', 'Burari', 'New Delhi', 'Delhi', '110084', 11, 5, '8826560929', NULL, 995.50, NULL, '2019-02-17 01:43:33', '2019-02-23 02:57:50'),
(2, 'user', 'Ramayan', 'prasad', 'ram.developer891@gmail.com', 'V62-J6P43H', '$2y$10$LJnDm5WA.hsmINxhPig88O/SN7oWJXpK.0LAWGr/O5TVCT4NUbFcK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www.google.com', 0.00, NULL, '2019-02-20 03:13:52', '2019-02-20 03:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_coin_transactions`
--

CREATE TABLE `user_coin_transactions` (
  `ct_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(10,2) NOT NULL,
  `coins` double(10,2) NOT NULL,
  `balance_coins` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_logs`
--

CREATE TABLE `user_login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `server_details` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`cpd_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`);

--
-- Indexes for table `article_transactions`
--
ALTER TABLE `article_transactions`
  ADD PRIMARY KEY (`article_transaction_id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`card_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_coin_transactions`
--
ALTER TABLE `user_coin_transactions`
  ADD PRIMARY KEY (`ct_id`);

--
-- Indexes for table `user_login_logs`
--
ALTER TABLE `user_login_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `cpd_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'company domain id', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `article_transactions`
--
ALTER TABLE `article_transactions`
  MODIFY `article_transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `bank_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `card_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_coin_transactions`
--
ALTER TABLE `user_coin_transactions`
  MODIFY `ct_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_login_logs`
--
ALTER TABLE `user_login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
