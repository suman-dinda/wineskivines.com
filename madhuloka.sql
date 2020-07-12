-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 12, 2020 at 08:15 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `madhuloka`
--

-- --------------------------------------------------------

--
-- Table structure for table `mt_address_book`
--

CREATE TABLE `mt_address_book` (
  `id` int(14) NOT NULL,
  `client_id` int(14) NOT NULL DEFAULT '0',
  `street` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(255) NOT NULL DEFAULT '',
  `location_name` varchar(255) NOT NULL DEFAULT '',
  `country_code` varchar(3) NOT NULL DEFAULT '',
  `as_default` int(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT '',
  `latitude` varchar(100) NOT NULL DEFAULT '',
  `longitude` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_address_book_location`
--

CREATE TABLE `mt_address_book_location` (
  `id` int(11) NOT NULL,
  `client_id` int(14) NOT NULL DEFAULT '0',
  `street` varchar(255) NOT NULL DEFAULT '',
  `location_name` varchar(255) NOT NULL DEFAULT '',
  `country_id` int(14) NOT NULL DEFAULT '0',
  `state_id` int(14) NOT NULL DEFAULT '0',
  `city_id` int(14) NOT NULL DEFAULT '0',
  `area_id` int(14) NOT NULL DEFAULT '0',
  `as_default` int(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `latitude` varchar(100) NOT NULL DEFAULT '',
  `longitude` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_admin_user`
--

CREATE TABLE `mt_admin_user` (
  `admin_id` int(14) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `user_lang` int(14) NOT NULL DEFAULT '0',
  `email_address` varchar(255) NOT NULL DEFAULT '',
  `lost_password_code` varchar(255) NOT NULL DEFAULT '',
  `session_token` varchar(255) NOT NULL DEFAULT '',
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_access` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_admin_user`
--

INSERT INTO `mt_admin_user` (`admin_id`, `username`, `password`, `first_name`, `last_name`, `role`, `date_created`, `date_modified`, `ip_address`, `user_lang`, `email_address`, `lost_password_code`, `session_token`, `last_login`, `user_access`) VALUES
(1, 'dindasu', '21232f297a57a5a743894a0e4a801fc3', 'Dindasu', 'Admin', '', '2020-07-03 08:29:30', '2020-07-07 17:45:09', '106.51.104.25', 0, 'lovelysin1990@gmail.com', '', '583280006599c2ceead1f9bc2ba32155a2403047ee5', '2020-07-08 20:41:06', '[\"autologin\",\"dashboard\",\"merchant\",\"sponsoredMerchantList\",\"packages\",\"Cuisine\",\"dishes\",\"OrderStatus\",\"incomingorders\",\"cancel_order\",\"settings\",\"themesettings\",\"managelocation\",\"commisionsettings\",\"voucher\",\"invoice\",\"merchantcommission\",\"withdrawal\",\"incomingwithdrawal\",\"withdrawalsettings\",\"emailsettings\",\"emailtpl\",\"notisettings\",\"emailogs\",\"cronjobs\",\"customPage\",\"Ratings\",\"ContactSettings\",\"SocialSettings\",\"ManageCurrency\",\"ManageLanguage\",\"Seo\",\"addons\",\"addonexport\",\"mobileapp\",\"pointsprogram\",\"merchantapp\",\"printermodule\",\"singlemerchant\",\"mobileappv2\",\"analytics\",\"customerlist\",\"subscriberlist\",\"reviews\",\"bankdeposit\",\"paymentgatewaysettings\",\"paymentgateway\",\"paypalSettings\",\"paypal_v2\",\"cardpaymentsettings\",\"stripeSettings\",\"mercadopagoSettings\",\"mercadopago\",\"sisowsettings\",\"payumonenysettings\",\"obdsettings\",\"payserasettings\",\"payondelivery\",\"barclay\",\"epaybg\",\"authorize\",\"braintree\",\"razor\",\"voguepay\",\"sms\",\"smsSettings\",\"smsPackage\",\"smstransaction\",\"smslogs\",\"fax\",\"faxtransaction\",\"faxpackage\",\"faxlogs\",\"faxsettings\",\"reports\",\"rptMerchantReg\",\"rptMerchantPayment\",\"rptMerchanteSales\",\"rptmerchantsalesummary\",\"rptbookingsummary\",\"userList\"]');

-- --------------------------------------------------------

--
-- Table structure for table `mt_bank_deposit`
--

CREATE TABLE `mt_bank_deposit` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `branch_code` varchar(100) NOT NULL DEFAULT '',
  `date_of_deposit` date DEFAULT NULL,
  `time_of_deposit` varchar(50) NOT NULL DEFAULT '',
  `amount` float(14,4) NOT NULL DEFAULT '0.0000',
  `scanphoto` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` date DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `transaction_type` varchar(255) NOT NULL DEFAULT 'merchant_signup',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `order_id` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_barclay_trans`
--

CREATE TABLE `mt_barclay_trans` (
  `id` int(14) NOT NULL,
  `orderid` varchar(14) NOT NULL DEFAULT '',
  `token` varchar(255) NOT NULL DEFAULT '',
  `transaction_type` varchar(255) NOT NULL DEFAULT 'signup',
  `date_created` date DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `param1` varchar(255) NOT NULL DEFAULT '',
  `param2` text,
  `param3` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_bookingtable`
--

CREATE TABLE `mt_bookingtable` (
  `booking_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `number_guest` int(14) NOT NULL DEFAULT '0',
  `date_booking` date DEFAULT NULL,
  `booking_time` varchar(50) NOT NULL DEFAULT '',
  `booking_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(100) NOT NULL DEFAULT '',
  `booking_notes` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT '',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `viewed` int(1) NOT NULL DEFAULT '1',
  `client_id` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_bookingtable_history`
--

CREATE TABLE `mt_bookingtable_history` (
  `id` int(14) NOT NULL,
  `booking_id` int(14) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '',
  `remarks` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_category`
--

CREATE TABLE `mt_category` (
  `cat_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `category_name` varchar(255) NOT NULL DEFAULT '',
  `category_description` text,
  `photo` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` varchar(50) NOT NULL DEFAULT '',
  `date_modified` varchar(50) DEFAULT '',
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `spicydish` int(2) NOT NULL DEFAULT '1',
  `spicydish_notes` text,
  `dish` text,
  `category_name_trans` text,
  `category_description_trans` text,
  `parent_cat_id` int(14) NOT NULL DEFAULT '0',
  `monday` int(1) NOT NULL DEFAULT '0',
  `tuesday` int(1) NOT NULL DEFAULT '0',
  `wednesday` int(1) NOT NULL DEFAULT '0',
  `thursday` int(1) NOT NULL DEFAULT '0',
  `friday` int(1) NOT NULL DEFAULT '0',
  `saturday` int(1) NOT NULL DEFAULT '0',
  `sunday` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_client`
--

CREATE TABLE `mt_client` (
  `client_id` int(14) NOT NULL,
  `social_strategy` varchar(100) NOT NULL DEFAULT 'web',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `email_address` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `street` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(100) NOT NULL DEFAULT '',
  `country_code` varchar(3) NOT NULL DEFAULT '',
  `location_name` text,
  `contact_phone` varchar(20) NOT NULL DEFAULT '',
  `lost_password_token` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'active',
  `token` varchar(255) NOT NULL DEFAULT '',
  `mobile_verification_code` int(14) NOT NULL DEFAULT '0',
  `mobile_verification_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `custom_field1` varchar(255) NOT NULL DEFAULT '',
  `custom_field2` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `email_verification_code` varchar(14) NOT NULL DEFAULT '',
  `is_guest` int(1) NOT NULL DEFAULT '2',
  `verify_code_requested` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `single_app_merchant_id` int(14) NOT NULL DEFAULT '0',
  `social_id` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_client_cc`
--

CREATE TABLE `mt_client_cc` (
  `cc_id` int(14) NOT NULL,
  `client_id` int(14) NOT NULL DEFAULT '0',
  `card_name` varchar(255) NOT NULL DEFAULT '',
  `credit_card_number` varchar(20) NOT NULL DEFAULT '',
  `expiration_month` varchar(5) NOT NULL DEFAULT '',
  `expiration_yr` varchar(5) NOT NULL DEFAULT '',
  `cvv` varchar(20) NOT NULL DEFAULT '',
  `billing_address` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `encrypted_card` binary(255) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_cooking_ref`
--

CREATE TABLE `mt_cooking_ref` (
  `cook_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `cooking_name` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT 'published',
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `cooking_name_trans` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_cuisine`
--

CREATE TABLE `mt_cuisine` (
  `cuisine_id` int(14) NOT NULL,
  `cuisine_name` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `cuisine_name_trans` text,
  `status` varchar(100) NOT NULL DEFAULT 'publish',
  `featured_image` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_cuisine`
--

INSERT INTO `mt_cuisine` (`cuisine_id`, `cuisine_name`, `sequence`, `date_created`, `date_modified`, `ip_address`, `cuisine_name_trans`, `status`, `featured_image`) VALUES
(1, 'American', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(2, 'Deli', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(3, 'Indian', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(4, 'Mediterranean', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(5, 'Sandwiches', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(6, 'Barbeque', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(7, 'Diner', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(8, 'Italian', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(9, 'Mexican', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(10, 'Sushi', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(11, 'Burgers', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(12, 'Greek', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(13, 'Japanese', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(14, 'Middle Eastern', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(15, 'Thai', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(16, 'Chinese', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(17, 'Healthy', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(18, 'Korean', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(19, 'Pizza', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', ''),
(20, 'Vegetarian', 0, '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28', '', 'publish', '');

-- --------------------------------------------------------

--
-- Table structure for table `mt_currency`
--

CREATE TABLE `mt_currency` (
  `currency_code` varchar(3) NOT NULL DEFAULT '',
  `currency_symbol` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_currency`
--

INSERT INTO `mt_currency` (`currency_code`, `currency_symbol`, `date_created`, `date_modified`, `ip_address`) VALUES
('AUD', '&#36;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('CAD', '&#36;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('CNY', '&yen;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('EUR', '&euro;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('HKD', '&#36;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('INR', '₹', '2020-07-04 10:07:44', '2020-07-04 10:07:44', '49.207.62.216'),
('JPY', '&yen;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('MXN', '&#36;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('NZD', '&#36;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
('USD', '&#36;', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28');

-- --------------------------------------------------------

--
-- Table structure for table `mt_custom_page`
--

CREATE TABLE `mt_custom_page` (
  `id` int(14) NOT NULL,
  `slug_name` varchar(255) NOT NULL DEFAULT '',
  `page_name` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `icons` varchar(255) NOT NULL DEFAULT '',
  `assign_to` varchar(50) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT '',
  `open_new_tab` int(11) NOT NULL DEFAULT '1',
  `is_custom_link` int(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_dishes`
--

CREATE TABLE `mt_dishes` (
  `dish_id` int(14) NOT NULL,
  `dish_name` varchar(255) NOT NULL DEFAULT '',
  `photo` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_email_logs`
--

CREATE TABLE `mt_email_logs` (
  `id` int(14) NOT NULL,
  `email_address` varchar(255) NOT NULL DEFAULT '',
  `sender` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `status` varchar(200) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `module_type` varchar(255) NOT NULL DEFAULT '',
  `user_type` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(14) NOT NULL DEFAULT '0',
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `email_provider` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_epos_client`
--

CREATE TABLE `mt_epos_client` (
  `client_id` int(14) NOT NULL,
  `id_epos` int(14) NOT NULL DEFAULT '-1',
  `social_strategy` varchar(100) NOT NULL DEFAULT 'web',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email_address` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zipcode` varchar(100) NOT NULL,
  `country_code` varchar(3) NOT NULL,
  `location_name` text NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `lost_password_token` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'active',
  `token` varchar(255) NOT NULL,
  `mobile_verification_code` int(14) NOT NULL,
  `mobile_verification_date` datetime NOT NULL,
  `custom_field1` varchar(255) NOT NULL,
  `custom_field2` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `email_verification_code` varchar(14) NOT NULL,
  `is_guest` int(1) NOT NULL DEFAULT '2',
  `cNotes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(15) NOT NULL,
  `merchant_id` int(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_epos_order`
--

CREATE TABLE `mt_epos_order` (
  `order_id` int(15) NOT NULL,
  `id_epos` int(14) DEFAULT NULL,
  `merchant_id` int(15) NOT NULL,
  `client_id` int(15) NOT NULL,
  `json_details` text NOT NULL,
  `payment_type` text NOT NULL,
  `subtotal` text NOT NULL,
  `taxable_total` text NOT NULL,
  `total_w_tax` text NOT NULL,
  `status` text NOT NULL,
  `delivery_charge` float NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_time` text NOT NULL,
  `delivery_instruction` text NOT NULL,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL,
  `delivery_asap` text NOT NULL,
  `isCleared` enum('true','false') NOT NULL DEFAULT 'false',
  `order_id_epos` int(15) NOT NULL,
  `cOrder` text NOT NULL,
  `cReason` text NOT NULL,
  `isInvoice` enum('true','false') NOT NULL DEFAULT 'false',
  `ot` int(255) DEFAULT '2',
  `request_from` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'POS'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mt_epos_order_details`
--

CREATE TABLE `mt_epos_order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `item_name` text NOT NULL,
  `addon` text NOT NULL,
  `price` double(10,2) NOT NULL,
  `merchant_id` int(15) NOT NULL,
  `size` text NOT NULL,
  `isCleared` enum('true','false') NOT NULL DEFAULT 'false',
  `tax` double(10,2) NOT NULL,
  `item_tax` double(10,2) NOT NULL DEFAULT '7.00',
  `date_created` date DEFAULT NULL,
  `time_created` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mt_favorites`
--

CREATE TABLE `mt_favorites` (
  `id` int(14) NOT NULL,
  `fav_type` varchar(100) NOT NULL DEFAULT 'restaurant',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_fax_broadcast`
--

CREATE TABLE `mt_fax_broadcast` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `faxno` varchar(50) NOT NULL DEFAULT '',
  `recipname` varchar(32) NOT NULL DEFAULT '',
  `faxurl` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `jobid` varchar(255) NOT NULL DEFAULT '',
  `api_raw_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_process` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_postback` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_fax_package`
--

CREATE TABLE `mt_fax_package` (
  `fax_package_id` int(14) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `price` float(14,4) NOT NULL DEFAULT '0.0000',
  `promo_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `fax_limit` int(14) NOT NULL DEFAULT '0',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_fax_package_trans`
--

CREATE TABLE `mt_fax_package_trans` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `fax_package_id` int(14) NOT NULL DEFAULT '0',
  `payment_type` varchar(50) NOT NULL DEFAULT '',
  `package_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `fax_limit` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) NOT NULL DEFAULT '',
  `payment_gateway_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_ingredients`
--

CREATE TABLE `mt_ingredients` (
  `ingredients_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `ingredients_name` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT 'published',
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `ingredients_name_trans` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_invoice`
--

CREATE TABLE `mt_invoice` (
  `invoice_number` int(14) NOT NULL,
  `invoice_token` varchar(100) NOT NULL DEFAULT '',
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `merchant_name` varchar(255) NOT NULL DEFAULT '',
  `merchant_contact_email` varchar(200) NOT NULL DEFAULT '',
  `merchant_contact_phone` varchar(50) NOT NULL DEFAULT '',
  `invoice_terms` int(14) NOT NULL DEFAULT '0',
  `invoice_total` float(14,4) NOT NULL DEFAULT '0.0000',
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `pdf_filename` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `viewed` varchar(2) NOT NULL DEFAULT '2',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_process` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_invoice_history`
--

CREATE TABLE `mt_invoice_history` (
  `id` int(14) NOT NULL,
  `invoice_number` varchar(14) NOT NULL DEFAULT '',
  `payment_status` varchar(100) NOT NULL DEFAULT '',
  `remarks` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_item`
--

CREATE TABLE `mt_item` (
  `item_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `item_name` varchar(255) NOT NULL DEFAULT '',
  `item_description` text,
  `status` varchar(50) NOT NULL DEFAULT '',
  `category` text,
  `price` text,
  `addon_item` text,
  `cooking_ref` text,
  `discount` varchar(14) NOT NULL DEFAULT '',
  `multi_option` text,
  `multi_option_value` text,
  `photo` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `is_featured` varchar(1) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `ingredients` text,
  `spicydish` int(2) NOT NULL DEFAULT '1',
  `two_flavors` int(2) NOT NULL,
  `two_flavors_position` text,
  `require_addon` text,
  `dish` text,
  `item_name_trans` text,
  `item_description_trans` text,
  `non_taxable` int(1) NOT NULL DEFAULT '1',
  `not_available` int(1) NOT NULL DEFAULT '1',
  `gallery_photo` text,
  `points_earned` int(14) NOT NULL DEFAULT '0',
  `points_disabled` int(1) NOT NULL DEFAULT '1',
  `packaging_fee` float(14,4) NOT NULL DEFAULT '0.0000',
  `packaging_incremental` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_item`
--

INSERT INTO `mt_item` (`item_id`, `merchant_id`, `item_name`, `item_description`, `status`, `category`, `price`, `addon_item`, `cooking_ref`, `discount`, `multi_option`, `multi_option_value`, `photo`, `sequence`, `is_featured`, `date_created`, `date_modified`, `ip_address`, `ingredients`, `spicydish`, `two_flavors`, `two_flavors_position`, `require_addon`, `dish`, `item_name_trans`, `item_description_trans`, `non_taxable`, `not_available`, `gallery_photo`, `points_earned`, `points_disabled`, `packaging_fee`, `packaging_incremental`) VALUES
(1, 1, 'Royal Challenger', '', 'publish', '', '{\"1\":\"130\",\"2\":\"400\",\"3\":\"2000\"}', '', '', '', '', '', '', 0, '', '2020-07-04 14:45:23', '2020-07-04 12:45:23', '49.207.62.216', '', 0, 0, '', '', '', NULL, NULL, 1, 1, '', 0, 1, 0.0000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `mt_languages`
--

CREATE TABLE `mt_languages` (
  `lang_id` int(14) NOT NULL,
  `country_code` varchar(14) NOT NULL DEFAULT '',
  `language_code` varchar(255) NOT NULL DEFAULT '',
  `source_text` text,
  `is_assign` int(1) NOT NULL DEFAULT '2',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL DEFAULT '',
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_location_area`
--

CREATE TABLE `mt_location_area` (
  `area_id` int(14) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `city_id` int(14) NOT NULL DEFAULT '0',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_location_cities`
--

CREATE TABLE `mt_location_cities` (
  `city_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `state_id` int(11) NOT NULL,
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_location_countries`
--

CREATE TABLE `mt_location_countries` (
  `country_id` int(11) NOT NULL,
  `shortcode` varchar(3) NOT NULL DEFAULT '',
  `country_name` varchar(150) NOT NULL DEFAULT '',
  `phonecode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_location_countries`
--

INSERT INTO `mt_location_countries` (`country_id`, `shortcode`, `country_name`, `phonecode`) VALUES
(1, 'AF', 'Afghanistan', 93),
(2, 'AL', 'Albania', 355),
(3, 'DZ', 'Algeria', 213),
(4, 'AS', 'American Samoa', 1684),
(5, 'AD', 'Andorra', 376),
(6, 'AO', 'Angola', 244),
(7, 'AI', 'Anguilla', 1264),
(8, 'AQ', 'Antarctica', 0),
(9, 'AG', 'Antigua And Barbuda', 1268),
(10, 'AR', 'Argentina', 54),
(11, 'AM', 'Armenia', 374),
(12, 'AW', 'Aruba', 297),
(13, 'AU', 'Australia', 61),
(14, 'AT', 'Austria', 43),
(15, 'AZ', 'Azerbaijan', 994),
(16, 'BS', 'Bahamas The', 1242),
(17, 'BH', 'Bahrain', 973),
(18, 'BD', 'Bangladesh', 880),
(19, 'BB', 'Barbados', 1246),
(20, 'BY', 'Belarus', 375),
(21, 'BE', 'Belgium', 32),
(22, 'BZ', 'Belize', 501),
(23, 'BJ', 'Benin', 229),
(24, 'BM', 'Bermuda', 1441),
(25, 'BT', 'Bhutan', 975),
(26, 'BO', 'Bolivia', 591),
(27, 'BA', 'Bosnia and Herzegovina', 387),
(28, 'BW', 'Botswana', 267),
(29, 'BV', 'Bouvet Island', 0),
(30, 'BR', 'Brazil', 55),
(31, 'IO', 'British Indian Ocean Territory', 246),
(32, 'BN', 'Brunei', 673),
(33, 'BG', 'Bulgaria', 359),
(34, 'BF', 'Burkina Faso', 226),
(35, 'BI', 'Burundi', 257),
(36, 'KH', 'Cambodia', 855),
(37, 'CM', 'Cameroon', 237),
(38, 'CA', 'Canada', 1),
(39, 'CV', 'Cape Verde', 238),
(40, 'KY', 'Cayman Islands', 1345),
(41, 'CF', 'Central African Republic', 236),
(42, 'TD', 'Chad', 235),
(43, 'CL', 'Chile', 56),
(44, 'CN', 'China', 86),
(45, 'CX', 'Christmas Island', 61),
(46, 'CC', 'Cocos (Keeling) Islands', 672),
(47, 'CO', 'Colombia', 57),
(48, 'KM', 'Comoros', 269),
(49, 'CG', 'Congo', 242),
(50, 'CD', 'Congo The Democratic Republic Of The', 242),
(51, 'CK', 'Cook Islands', 682),
(52, 'CR', 'Costa Rica', 506),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', 225),
(54, 'HR', 'Croatia (Hrvatska)', 385),
(55, 'CU', 'Cuba', 53),
(56, 'CY', 'Cyprus', 357),
(57, 'CZ', 'Czech Republic', 420),
(58, 'DK', 'Denmark', 45),
(59, 'DJ', 'Djibouti', 253),
(60, 'DM', 'Dominica', 1767),
(61, 'DO', 'Dominican Republic', 1809),
(62, 'TP', 'East Timor', 670),
(63, 'EC', 'Ecuador', 593),
(64, 'EG', 'Egypt', 20),
(65, 'SV', 'El Salvador', 503),
(66, 'GQ', 'Equatorial Guinea', 240),
(67, 'ER', 'Eritrea', 291),
(68, 'EE', 'Estonia', 372),
(69, 'ET', 'Ethiopia', 251),
(70, 'XA', 'External Territories of Australia', 61),
(71, 'FK', 'Falkland Islands', 500),
(72, 'FO', 'Faroe Islands', 298),
(73, 'FJ', 'Fiji Islands', 679),
(74, 'FI', 'Finland', 358),
(75, 'FR', 'France', 33),
(76, 'GF', 'French Guiana', 594),
(77, 'PF', 'French Polynesia', 689),
(78, 'TF', 'French Southern Territories', 0),
(79, 'GA', 'Gabon', 241),
(80, 'GM', 'Gambia The', 220),
(81, 'GE', 'Georgia', 995),
(82, 'DE', 'Germany', 49),
(83, 'GH', 'Ghana', 233),
(84, 'GI', 'Gibraltar', 350),
(85, 'GR', 'Greece', 30),
(86, 'GL', 'Greenland', 299),
(87, 'GD', 'Grenada', 1473),
(88, 'GP', 'Guadeloupe', 590),
(89, 'GU', 'Guam', 1671),
(90, 'GT', 'Guatemala', 502),
(91, 'XU', 'Guernsey and Alderney', 44),
(92, 'GN', 'Guinea', 224),
(93, 'GW', 'Guinea-Bissau', 245),
(94, 'GY', 'Guyana', 592),
(95, 'HT', 'Haiti', 509),
(96, 'HM', 'Heard and McDonald Islands', 0),
(97, 'HN', 'Honduras', 504),
(98, 'HK', 'Hong Kong S.A.R.', 852),
(99, 'HU', 'Hungary', 36),
(100, 'IS', 'Iceland', 354),
(101, 'IN', 'India', 91),
(102, 'ID', 'Indonesia', 62),
(103, 'IR', 'Iran', 98),
(104, 'IQ', 'Iraq', 964),
(105, 'IE', 'Ireland', 353),
(106, 'IL', 'Israel', 972),
(107, 'IT', 'Italy', 39),
(108, 'JM', 'Jamaica', 1876),
(109, 'JP', 'Japan', 81),
(110, 'XJ', 'Jersey', 44),
(111, 'JO', 'Jordan', 962),
(112, 'KZ', 'Kazakhstan', 7),
(113, 'KE', 'Kenya', 254),
(114, 'KI', 'Kiribati', 686),
(115, 'KP', 'Korea North', 850),
(116, 'KR', 'Korea South', 82),
(117, 'KW', 'Kuwait', 965),
(118, 'KG', 'Kyrgyzstan', 996),
(119, 'LA', 'Laos', 856),
(120, 'LV', 'Latvia', 371),
(121, 'LB', 'Lebanon', 961),
(122, 'LS', 'Lesotho', 266),
(123, 'LR', 'Liberia', 231),
(124, 'LY', 'Libya', 218),
(125, 'LI', 'Liechtenstein', 423),
(126, 'LT', 'Lithuania', 370),
(127, 'LU', 'Luxembourg', 352),
(128, 'MO', 'Macau S.A.R.', 853),
(129, 'MK', 'Macedonia', 389),
(130, 'MG', 'Madagascar', 261),
(131, 'MW', 'Malawi', 265),
(132, 'MY', 'Malaysia', 60),
(133, 'MV', 'Maldives', 960),
(134, 'ML', 'Mali', 223),
(135, 'MT', 'Malta', 356),
(136, 'XM', 'Man (Isle of)', 44),
(137, 'MH', 'Marshall Islands', 692),
(138, 'MQ', 'Martinique', 596),
(139, 'MR', 'Mauritania', 222),
(140, 'MU', 'Mauritius', 230),
(141, 'YT', 'Mayotte', 269),
(142, 'MX', 'Mexico', 52),
(143, 'FM', 'Micronesia', 691),
(144, 'MD', 'Moldova', 373),
(145, 'MC', 'Monaco', 377),
(146, 'MN', 'Mongolia', 976),
(147, 'MS', 'Montserrat', 1664),
(148, 'MA', 'Morocco', 212),
(149, 'MZ', 'Mozambique', 258),
(150, 'MM', 'Myanmar', 95),
(151, 'NA', 'Namibia', 264),
(152, 'NR', 'Nauru', 674),
(153, 'NP', 'Nepal', 977),
(154, 'AN', 'Netherlands Antilles', 599),
(155, 'NL', 'Netherlands The', 31),
(156, 'NC', 'New Caledonia', 687),
(157, 'NZ', 'New Zealand', 64),
(158, 'NI', 'Nicaragua', 505),
(159, 'NE', 'Niger', 227),
(160, 'NG', 'Nigeria', 234),
(161, 'NU', 'Niue', 683),
(162, 'NF', 'Norfolk Island', 672),
(163, 'MP', 'Northern Mariana Islands', 1670),
(164, 'NO', 'Norway', 47),
(165, 'OM', 'Oman', 968),
(166, 'PK', 'Pakistan', 92),
(167, 'PW', 'Palau', 680),
(168, 'PS', 'Palestinian Territory Occupied', 970),
(169, 'PA', 'Panama', 507),
(170, 'PG', 'Papua new Guinea', 675),
(171, 'PY', 'Paraguay', 595),
(172, 'PE', 'Peru', 51),
(173, 'PH', 'Philippines', 63),
(174, 'PN', 'Pitcairn Island', 0),
(175, 'PL', 'Poland', 48),
(176, 'PT', 'Portugal', 351),
(177, 'PR', 'Puerto Rico', 1787),
(178, 'QA', 'Qatar', 974),
(179, 'RE', 'Reunion', 262),
(180, 'RO', 'Romania', 40),
(181, 'RU', 'Russia', 70),
(182, 'RW', 'Rwanda', 250),
(183, 'SH', 'Saint Helena', 290),
(184, 'KN', 'Saint Kitts And Nevis', 1869),
(185, 'LC', 'Saint Lucia', 1758),
(186, 'PM', 'Saint Pierre and Miquelon', 508),
(187, 'VC', 'Saint Vincent And The Grenadines', 1784),
(188, 'WS', 'Samoa', 684),
(189, 'SM', 'San Marino', 378),
(190, 'ST', 'Sao Tome and Principe', 239),
(191, 'SA', 'Saudi Arabia', 966),
(192, 'SN', 'Senegal', 221),
(193, 'RS', 'Serbia', 381),
(194, 'SC', 'Seychelles', 248),
(195, 'SL', 'Sierra Leone', 232),
(196, 'SG', 'Singapore', 65),
(197, 'SK', 'Slovakia', 421),
(198, 'SI', 'Slovenia', 386),
(199, 'XG', 'Smaller Territories of the UK', 44),
(200, 'SB', 'Solomon Islands', 677),
(201, 'SO', 'Somalia', 252),
(202, 'ZA', 'South Africa', 27),
(203, 'GS', 'South Georgia', 0),
(204, 'SS', 'South Sudan', 211),
(205, 'ES', 'Spain', 34),
(206, 'LK', 'Sri Lanka', 94),
(207, 'SD', 'Sudan', 249),
(208, 'SR', 'Suriname', 597),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', 47),
(210, 'SZ', 'Swaziland', 268),
(211, 'SE', 'Sweden', 46),
(212, 'CH', 'Switzerland', 41),
(213, 'SY', 'Syria', 963),
(214, 'TW', 'Taiwan', 886),
(215, 'TJ', 'Tajikistan', 992),
(216, 'TZ', 'Tanzania', 255),
(217, 'TH', 'Thailand', 66),
(218, 'TG', 'Togo', 228),
(219, 'TK', 'Tokelau', 690),
(220, 'TO', 'Tonga', 676),
(221, 'TT', 'Trinidad And Tobago', 1868),
(222, 'TN', 'Tunisia', 216),
(223, 'TR', 'Turkey', 90),
(224, 'TM', 'Turkmenistan', 7370),
(225, 'TC', 'Turks And Caicos Islands', 1649),
(226, 'TV', 'Tuvalu', 688),
(227, 'UG', 'Uganda', 256),
(228, 'UA', 'Ukraine', 380),
(229, 'AE', 'United Arab Emirates', 971),
(230, 'GB', 'United Kingdom', 44),
(231, 'US', 'United States', 1),
(232, 'UM', 'United States Minor Outlying Islands', 1),
(233, 'UY', 'Uruguay', 598),
(234, 'UZ', 'Uzbekistan', 998),
(235, 'VU', 'Vanuatu', 678),
(236, 'VA', 'Vatican City State (Holy See)', 39),
(237, 'VE', 'Venezuela', 58),
(238, 'VN', 'Vietnam', 84),
(239, 'VG', 'Virgin Islands (British)', 1284),
(240, 'VI', 'Virgin Islands (US)', 1340),
(241, 'WF', 'Wallis And Futuna Islands', 681),
(242, 'EH', 'Western Sahara', 212),
(243, 'YE', 'Yemen', 967),
(244, 'YU', 'Yugoslavia', 38),
(245, 'ZM', 'Zambia', 260),
(246, 'ZW', 'Zimbabwe', 263);

-- --------------------------------------------------------

--
-- Table structure for table `mt_location_rate`
--

CREATE TABLE `mt_location_rate` (
  `rate_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `country_id` int(14) NOT NULL DEFAULT '0',
  `state_id` int(14) NOT NULL DEFAULT '0',
  `city_id` int(14) DEFAULT '0',
  `area_id` int(14) NOT NULL DEFAULT '0',
  `fee` float(14,5) NOT NULL DEFAULT '0.00000',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_location_states`
--

CREATE TABLE `mt_location_states` (
  `state_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `country_id` int(11) NOT NULL DEFAULT '1',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_merchant`
--

CREATE TABLE `mt_merchant` (
  `merchant_id` int(14) NOT NULL,
  `restaurant_slug` varchar(255) NOT NULL DEFAULT '',
  `restaurant_name` varchar(255) NOT NULL DEFAULT '',
  `restaurant_phone` varchar(100) NOT NULL DEFAULT '',
  `contact_name` varchar(255) NOT NULL DEFAULT '',
  `contact_phone` varchar(100) NOT NULL DEFAULT '',
  `contact_email` varchar(255) NOT NULL DEFAULT '',
  `country_code` varchar(3) NOT NULL DEFAULT '',
  `street` text,
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `post_code` varchar(100) NOT NULL DEFAULT '',
  `cuisine` text,
  `service` varchar(255) NOT NULL DEFAULT '',
  `free_delivery` int(1) NOT NULL DEFAULT '2',
  `delivery_estimation` varchar(100) NOT NULL DEFAULT '',
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `activation_key` varchar(50) NOT NULL DEFAULT '',
  `activation_token` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_activated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `package_id` int(14) NOT NULL DEFAULT '0',
  `package_price` float(14,5) NOT NULL DEFAULT '0.00000',
  `membership_expired` date DEFAULT NULL,
  `payment_steps` int(1) NOT NULL DEFAULT '1',
  `is_featured` int(1) NOT NULL DEFAULT '1',
  `is_ready` int(1) NOT NULL DEFAULT '1',
  `is_sponsored` int(2) NOT NULL DEFAULT '1',
  `sponsored_expiration` date DEFAULT NULL,
  `lost_password_code` varchar(50) NOT NULL DEFAULT '',
  `user_lang` int(14) NOT NULL DEFAULT '0',
  `membership_purchase_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sort_featured` int(14) NOT NULL DEFAULT '0',
  `is_commission` int(1) NOT NULL DEFAULT '1',
  `percent_commision` float(14,5) NOT NULL DEFAULT '0.00000',
  `abn` varchar(255) NOT NULL DEFAULT '',
  `session_token` varchar(255) NOT NULL DEFAULT '',
  `commision_type` varchar(50) NOT NULL DEFAULT 'percentage',
  `mobile_session_token` varchar(255) NOT NULL DEFAULT '',
  `merchant_key` varchar(255) NOT NULL DEFAULT '',
  `latitude` varchar(50) NOT NULL DEFAULT '',
  `lontitude` varchar(50) NOT NULL DEFAULT '',
  `delivery_charges` float(14,5) NOT NULL DEFAULT '0.00000',
  `minimum_order` float(14,5) NOT NULL DEFAULT '0.00000',
  `delivery_minimum_order` float(14,5) NOT NULL DEFAULT '0.00000',
  `delivery_maximum_order` float(14,5) NOT NULL DEFAULT '0.00000',
  `pickup_minimum_order` float(14,5) NOT NULL DEFAULT '0.00000',
  `pickup_maximum_order` float(14,5) NOT NULL DEFAULT '0.00000',
  `country_name` varchar(255) NOT NULL DEFAULT '',
  `country_id` int(14) NOT NULL DEFAULT '0',
  `state_id` int(14) NOT NULL DEFAULT '0',
  `city_id` int(14) NOT NULL DEFAULT '0',
  `area_id` int(14) NOT NULL DEFAULT '0',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `merchant_type` int(1) NOT NULL DEFAULT '1',
  `invoice_terms` int(14) NOT NULL DEFAULT '7',
  `payment_gateway_ref` varchar(255) NOT NULL DEFAULT '',
  `user_access` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_merchant`
--

INSERT INTO `mt_merchant` (`merchant_id`, `restaurant_slug`, `restaurant_name`, `restaurant_phone`, `contact_name`, `contact_phone`, `contact_email`, `country_code`, `street`, `city`, `state`, `post_code`, `cuisine`, `service`, `free_delivery`, `delivery_estimation`, `username`, `password`, `activation_key`, `activation_token`, `status`, `date_created`, `date_modified`, `date_activated`, `last_login`, `ip_address`, `package_id`, `package_price`, `membership_expired`, `payment_steps`, `is_featured`, `is_ready`, `is_sponsored`, `sponsored_expiration`, `lost_password_code`, `user_lang`, `membership_purchase_date`, `sort_featured`, `is_commission`, `percent_commision`, `abn`, `session_token`, `commision_type`, `mobile_session_token`, `merchant_key`, `latitude`, `lontitude`, `delivery_charges`, `minimum_order`, `delivery_minimum_order`, `delivery_maximum_order`, `pickup_minimum_order`, `pickup_maximum_order`, `country_name`, `country_id`, `state_id`, `city_id`, `area_id`, `logo`, `merchant_type`, `invoice_terms`, `payment_gateway_ref`, `user_access`) VALUES
(1, 'madhuloka', 'Madhuloka', '9000999888', 'Madhu', '9000999888', 'madhuloka.store@gmail.com', 'IN', 'No 5, 1st Floor, MGR Complex, Sarjapur Main Rd, Kaikondrahalli', 'Bengaluru', 'Karnataka', '560048', '[\"3\"]', '1', 2, '', 'madhuloka', '21232f297a57a5a743894a0e4a801fc3', '', '', 'active', '2020-07-04 14:18:20', '2020-07-04 12:18:20', '2020-07-04 12:18:20', '2020-07-04 12:18:20', '49.207.62.216', 0, 0.00000, '0000-00-00', 1, 2, 2, 1, NULL, '', 0, '2020-07-04 12:18:20', 0, 2, 5.00000, '', '979832996124ac9fa371da06b782f72e36dc7108008', 'percentage', '', '', '12.8838837', '77.5887602', 0.00000, 199.00000, 199.00000, 0.00000, 0.00000, 0.00000, '', 0, 0, 0, 0, '1593865244-madhuloka.png', 2, 1, '', '[]');

-- --------------------------------------------------------

--
-- Table structure for table `mt_merchant_cc`
--

CREATE TABLE `mt_merchant_cc` (
  `mt_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `card_name` varchar(255) NOT NULL DEFAULT '',
  `credit_card_number` varchar(20) NOT NULL DEFAULT '',
  `expiration_month` varchar(5) NOT NULL DEFAULT '',
  `expiration_yr` varchar(5) NOT NULL DEFAULT '',
  `cvv` varchar(20) NOT NULL,
  `billing_address` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `encrypted_card` binary(255) DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_merchant_user`
--

CREATE TABLE `mt_merchant_user` (
  `merchant_user_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `user_access` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(100) NOT NULL DEFAULT 'active',
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `contact_email` varchar(255) NOT NULL DEFAULT '',
  `session_token` varchar(255) NOT NULL DEFAULT '',
  `mobile_session_token` varchar(255) NOT NULL DEFAULT '',
  `lost_password_code` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_minimum_table`
--

CREATE TABLE `mt_minimum_table` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `distance_from` float(14,4) NOT NULL DEFAULT '0.0000',
  `distance_to` float(14,4) NOT NULL DEFAULT '0.0000',
  `shipping_units` varchar(5) NOT NULL DEFAULT '',
  `min_order` float(14,4) NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_broadcast`
--

CREATE TABLE `mt_mobile2_broadcast` (
  `broadcast_id` int(14) NOT NULL,
  `push_title` varchar(255) NOT NULL DEFAULT '',
  `push_message` varchar(255) NOT NULL DEFAULT '',
  `device_platform` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `fcm_response` text,
  `fcm_version` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_cart`
--

CREATE TABLE `mt_mobile2_cart` (
  `cart_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `device_uiid` varchar(255) DEFAULT '',
  `device_platform` varchar(50) NOT NULL DEFAULT '',
  `cart` text,
  `cart_count` int(14) NOT NULL DEFAULT '0',
  `voucher_details` text,
  `street` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zipcode` varchar(100) NOT NULL DEFAULT '',
  `delivery_instruction` varchar(255) NOT NULL DEFAULT '',
  `location_name` varchar(255) NOT NULL DEFAULT '',
  `contact_phone` varchar(50) NOT NULL DEFAULT '',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tips` float(14,4) NOT NULL DEFAULT '0.0000',
  `points_earn` int(14) NOT NULL DEFAULT '0',
  `points_apply` int(14) NOT NULL DEFAULT '0',
  `points_amount` float(14,4) NOT NULL DEFAULT '0.0000',
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `delivery_fee` float(14,4) NOT NULL DEFAULT '0.0000',
  `min_delivery_order` float(14,4) NOT NULL DEFAULT '0.0000',
  `delivery_lat` varchar(50) NOT NULL DEFAULT '',
  `delivery_long` varchar(50) NOT NULL DEFAULT '',
  `save_address` int(1) NOT NULL DEFAULT '0',
  `distance` varchar(255) NOT NULL DEFAULT '',
  `distance_unit` varchar(15) NOT NULL DEFAULT '',
  `state_id` int(14) NOT NULL DEFAULT '0',
  `city_id` int(14) NOT NULL DEFAULT '0',
  `area_id` int(14) NOT NULL DEFAULT '0',
  `cart_subtotal` float(14,4) NOT NULL DEFAULT '0.0000',
  `remove_tip` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_device_reg`
--

CREATE TABLE `mt_mobile2_device_reg` (
  `id` int(14) NOT NULL,
  `client_id` int(14) NOT NULL DEFAULT '0',
  `device_uiid` varchar(255) NOT NULL DEFAULT '',
  `device_id` text,
  `device_platform` varchar(50) NOT NULL DEFAULT '',
  `push_enabled` int(1) NOT NULL DEFAULT '1',
  `status` varchar(100) NOT NULL DEFAULT 'active',
  `code_version` varchar(14) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `subscribe_topic` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `mt_mobile2_device_reg_view`
-- (See below for the actual view)
--
CREATE TABLE `mt_mobile2_device_reg_view` (
`id` int(14)
,`client_id` int(14)
,`device_uiid` varchar(255)
,`device_id` text
,`device_platform` varchar(50)
,`push_enabled` int(1)
,`status` varchar(100)
,`code_version` varchar(14)
,`date_created` datetime
,`date_modified` datetime
,`ip_address` varchar(50)
,`subscribe_topic` int(1)
,`full_name` varchar(511)
,`last_login` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_homebanner`
--

CREATE TABLE `mt_mobile2_homebanner` (
  `banner_id` int(14) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `banner_name` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `sub_title` varchar(255) NOT NULL DEFAULT '',
  `tag_id` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_order_trigger`
--

CREATE TABLE `mt_mobile2_order_trigger` (
  `trigger_id` int(14) NOT NULL,
  `trigger_type` varchar(100) NOT NULL DEFAULT 'order',
  `order_id` int(14) NOT NULL DEFAULT '0',
  `order_status` varchar(255) NOT NULL,
  `remarks` text,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_process` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_pages`
--

CREATE TABLE `mt_mobile2_pages` (
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `icon` varchar(100) DEFAULT '',
  `use_html` varchar(1) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_push_logs`
--

CREATE TABLE `mt_mobile2_push_logs` (
  `id` int(11) NOT NULL,
  `broadcast_id` int(14) NOT NULL DEFAULT '0',
  `trigger_id` int(14) NOT NULL DEFAULT '0',
  `push_type` varchar(100) NOT NULL DEFAULT 'order',
  `client_id` int(14) DEFAULT '0',
  `client_name` varchar(255) NOT NULL DEFAULT '',
  `device_platform` varchar(100) NOT NULL DEFAULT '',
  `device_id` text,
  `device_uiid` varchar(255) NOT NULL DEFAULT '',
  `push_title` varchar(255) NOT NULL DEFAULT '',
  `push_message` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `json_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_process` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `is_read` int(1) NOT NULL DEFAULT '0',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_recent_location`
--

CREATE TABLE `mt_mobile2_recent_location` (
  `id` int(14) NOT NULL,
  `device_uiid` varchar(255) DEFAULT '',
  `search_address` text,
  `street` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(255) NOT NULL DEFAULT '',
  `location_name` varchar(255) NOT NULL DEFAULT '',
  `latitude` varchar(100) NOT NULL DEFAULT '',
  `longitude` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile2_recent_search`
--

CREATE TABLE `mt_mobile2_recent_search` (
  `id` int(11) NOT NULL,
  `device_uiid` varchar(255) NOT NULL DEFAULT '',
  `search_string` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_mobile_broadcast`
--

CREATE TABLE `mt_mobile_broadcast` (
  `broadcast_id` int(14) NOT NULL,
  `push_title` varchar(255) NOT NULL DEFAULT '',
  `push_message` varchar(255) NOT NULL DEFAULT '',
  `device_platform` int(14) NOT NULL DEFAULT '1',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_newsletter`
--

CREATE TABLE `mt_newsletter` (
  `id` int(14) NOT NULL,
  `email_address` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_offers`
--

CREATE TABLE `mt_offers` (
  `offers_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `offer_percentage` float(14,4) NOT NULL DEFAULT '0.0000',
  `offer_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `applicable_to` varchar(100) NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_option`
--

CREATE TABLE `mt_option` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `option_name` varchar(255) NOT NULL DEFAULT '',
  `option_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_option`
--

INSERT INTO `mt_option` (`id`, `merchant_id`, `option_name`, `option_value`) VALUES
(1, 0, 'website_title', 'Madhuloka'),
(2, 0, 'website_address', 'Cuttack, Odisha'),
(3, 0, 'admin_country_set', 'IN'),
(4, 0, 'website_contact_phone', '08050986742'),
(5, 0, 'website_contact_email', 'support@liquorboutique.com'),
(6, 0, 'contact_us_tpl_subject_en', 'contact us form'),
(7, 0, 'contact_us_tpl_content_en', 'hi admin \n\nnew contact us\nname : [name] \nemail : [email]  \ncountry : [country] \nphone : [phone] \nmessage : [message] \n\nRegards \n- [sitename]'),
(8, 0, 'contact_us_email', '1'),
(9, 0, 'customer_welcome_email_tpl_subject_en', 'Welcome [firstname]'),
(10, 0, 'customer_welcome_email_tpl_content_en', 'hi [firstname]\n\nthank you for signup \n\nRegards\n- [sitename]'),
(11, 0, 'customer_welcome_email_email', '1'),
(12, 0, 'customer_forgot_password_tpl_subject_en', 'Forgot password'),
(13, 0, 'customer_forgot_password_tpl_content_en', 'Hi [firstname]\n\nYou have requested for your password\nclick in the link to change your password\n[change_pass_link]\n\nRegards\n - [sitename]'),
(14, 0, 'customer_forgot_password_email', '1'),
(15, 0, 'customer_verification_code_email_tpl_subject_en', 'Signup Verification Code'),
(16, 0, 'customer_verification_code_email_tpl_content_en', 'hi [firstname]\n\nYour signup verification is [code]\n\nRegards\n- [sitename]'),
(17, 0, 'customer_verification_code_email_email', '1'),
(18, 0, 'merchant_verification_code_tpl_subject_en', 'Your Activation Code'),
(19, 0, 'merchant_verification_code_tpl_content_en', 'hi [restaurant_name]\n\nYour activation code is [code]\n\nRegards\n  - [sitename]'),
(20, 0, 'merchant_verification_code_email', '1'),
(21, 0, 'merchant_forgot_password_tpl_subject_en', 'Merchant Forgot Password'),
(22, 0, 'merchant_forgot_password_tpl_content_en', 'hi [restaurant_name]\n\nYour Your verification code is  [code]\n\nRegards\n - [sitename]'),
(23, 0, 'merchant_forgot_password_email', '1'),
(24, 0, 'admin_forgot_password_tpl_subject_en', 'Admin Change Password'),
(25, 0, 'admin_forgot_password_tpl_content_en', 'hi admin\n\nYour password has been reset to : [newpassword]\n\nRegards\n- [sitename]'),
(26, 0, 'admin_forgot_password_email', '1'),
(27, 0, 'merchant_new_signup_tpl_subject_en', 'New Restaurant Signup - [restaurant_name]'),
(28, 0, 'merchant_new_signup_tpl_content_en', 'hi admin\n\nNew restaurant signup \nname : [restaurant_name]\npackage : [package_name]\ntype : [merchant_type]\n\nregard'),
(29, 0, 'merchant_new_signup_sms_content_en', 'New Restaurant Signup name : [restaurant_name]\npackage : [package_name]\ntype : [merchant_type] '),
(30, 0, 'receipt_template_tpl_subject_en', 'Your Receipt for Order ID : [order_id]'),
(31, 0, 'receipt_template_tpl_content_en', 'Dear [customer_name],\n\n\nThank you for shopping at [sitename]. We hope you enjoy your new purchase! Your order number is [order_id]. We have included your order receipt and delivery details below:\n\n[receipt]\n\nKind Regards'),
(32, 0, 'receipt_template_sms_content_en', 'Hi [customer_name] \nWe have receive your order,\nDetails:\nOrder ID : [order_id]\nRestaurant : [restaurant_name]\nTotal Amount : [total_amount]\nOrder Details : \n[order_details]\n\nRegards\n[sitename]\n'),
(33, 0, 'receipt_template_email', '1'),
(34, 0, 'receipt_template_sms', '1'),
(35, 0, 'receipt_send_to_merchant_tpl_subject_en', 'New Order : [order_id] From [customer_name]'),
(36, 0, 'receipt_send_to_merchant_tpl_content_en', 'hi [restaurant_name],\n\nThere is a new order with the reference number [order_id] from customer [customer_name]\n\n[receipt]\n\nAccept Order\n[accept_link]\n\nDecline Order\n[decline_link]\n\nKind Regards'),
(37, 0, 'receipt_send_to_merchant_sms_content_en', 'New Order : [order_id] From [customer_name]\ntotal amout : [total_amount]\ndetails : \n[order_details]\n'),
(38, 0, 'receipt_send_to_merchant_email', '1'),
(39, 0, 'receipt_send_to_admin_email', '1'),
(40, 0, 'receipt_send_to_admin_tpl_subject_en', 'New Order : [order_id] From [customer_name]'),
(41, 0, 'receipt_send_to_admin_tpl_content_en', 'hi admin,\n\nThere is a new order to [restaurant_name]\nwith the reference number [order_id] from customer [customer_name]\n\n[receipt]\n\nAccept Order\n[accept_link]\n\nDecline Order\n[decline_link]\n\nKind Regards'),
(42, 0, 'receipt_send_to_admin_sms_content_en', 'New Order : [order_id] From [customer_name]\ntotal amout : [total_amount]\ndetails : [order_details]'),
(43, 0, 'offline_bank_deposit_tpl_subject_en', 'Bank deposit instructions'),
(44, 0, 'offline_bank_deposit_tpl_content_en', 'Hi [restaurant_name]\n\nDeposit Instructions\n\nPlease deposit [amount] to :\n\nBank : Your bank name\nAccount Number : Your bank account number\nAccount Name : Your bank account name\n\nWhen deposit is completed \nfill in your bank deposit information \n[verify_payment_link]\n\n\nKind Regards\n  -[sitename]'),
(45, 0, 'offline_bank_deposit_signup_merchant_tpl_subject_en', 'Bank deposit instructions'),
(46, 0, 'offline_bank_deposit_signup_merchant_tpl_content_en', 'Hi [restaurant_name]\n\nDeposit Instructions\n\nPlease deposit [amount] to :\n\nBank : Your bank name\nAccount Number : Your bank account number\nAccount Name : Your bank account name\n\nWhen deposit is completed \nfill in your bank deposit information \n[verify_payment_link]\n\n\nKind Regards\n  -[sitename]'),
(47, 0, 'offline_bank_deposit_signup_merchant_email', '1'),
(48, 0, 'offline_bank_deposit_purchase_email', '1'),
(49, 0, 'offline_bank_deposit_purchase_tpl_subject_en', 'Bank deposit instructions for order id : [order_id]'),
(50, 0, 'offline_bank_deposit_purchase_tpl_content_en', 'Hi [customer_name]\n\nDeposit Instructions\n\nPlease deposit [amount] to :\n\nBank : Your bank name\nAccount Number : Your bank account number\nAccount Name : Your bank account name\n\nWhen deposit is completed \nfill in your bank deposit information \n[verify_payment_link]\n\n\nKind Regards\n  -[sitename]'),
(51, 0, 'merchant_near_expiration_email', '1'),
(52, 0, 'merchant_near_expiration_sms', '1'),
(53, 0, 'merchant_near_expiration_tpl_subject_en', 'Your membership about to expired'),
(54, 0, 'merchant_near_expiration_tpl_content_en', 'hi [restaurant_name]\n\n Your membership is about to expire in [expiration_date]\n\nRegards\n - [sitename]'),
(55, 0, 'merchant_near_expiration_sms_content_en', 'hi [restaurant_name]\n Your membership is about to expire in [expiration_date]\nRegards\n - [sitename]'),
(56, 0, 'merchant_near_expiration_day', '5'),
(57, 0, 'merchant_change_status_email', '1'),
(58, 0, 'merchant_change_status_tpl_subject_en', 'Account Status Updated'),
(59, 0, 'merchant_change_status_tpl_content_en', 'hi [restaurant_name]\n\nYour merchant records status has change to [status]\n\nRegards\n - [sitename]'),
(60, 0, 'merchant_change_status_sms_content_en', 'hi [restaurant_name]\n\nYour merchant records status has change to [status]\n\nRegards\n - [sitename]'),
(61, 0, 'customer_booked_email', '1'),
(62, 0, 'customer_booked_tpl_subject_en', 'We have receive your Booking'),
(63, 0, 'customer_booked_tpl_content_en', 'Hi [customer_name]\n\nwe have receive your booking \nfor restaurant [restaurant_name]\nwith the following information\n\nNumber of guest : [number_guest]\nDate of Booking : [date_booking]\nTime : [time]\nEmail address : [email]\nMobile  : [mobile]\nSpecial instruction : [instruction]\n\nYour booking id : [booking_id]\n\nPlease wait for the merchant to accept your booking.\nyou will receive another email for confirmation from the merchant\n\nRegards\n - [sitename]'),
(64, 0, 'booked_notify_admin_email', '1'),
(65, 0, 'booked_notify_admin_tpl_subject_en', 'New booking from [restaurant_name]'),
(66, 0, 'booked_notify_admin_tpl_content_en', 'hi admin\n\nthere is new booking from [restaurant_name]\nwith the following information\n\nBooking ID : [booking_id]\nName : [customer_name]\nNumber of guest : [number_guest]\nDate of Booking : [date_booking]\nTime : [time]\nEmail address : [email]\nMobile  : [mobile]\nSpecial instruction : [instruction]\n'),
(67, 0, 'booked_notify_merchant_tpl_subject_en', 'New booking from [customer_name]'),
(68, 0, 'booked_notify_merchant_tpl_content_en', 'hi [restaurant_name]\n\nthere is new booking from [customer_name]\nwith the following information\n\nBooking ID : [booking_id]\nName : [customer_name]\nNumber of guest : [number_guest]\nDate of Booking : [date_booking]\nTime : [time]\nEmail address : [email]\nMobile  : [mobile]\nSpecial instruction : [instruction]\n\n'),
(69, 0, 'booking_update_status_tpl_subject_en', 'Update with your booking id [booking_id]'),
(70, 0, 'booking_update_status_tpl_content_en', 'Hi [customer_name]\n\nYour booking id [booking_id]\nstatus updated to [status]\n\nmerchant remarks :\n[merchant_remarks]\n\nRegards\n - [sitename]'),
(71, 0, 'merchant_welcome_signup_email', '1'),
(72, 0, 'merchant_welcome_signup_tpl_subject_en', 'Welcome [restaurant_name]'),
(73, 0, 'merchant_welcome_signup_tpl_content_en', 'hi  [restaurant_name]\n\nThank you for joining us!\n\nLogin here\n[login_url]\n\nRegards\n - [sitename]'),
(74, 0, 'order_idle_to_merchant_tpl_subject_en', 'Order ID [order_id] has been IDLE for [idle_time]'),
(75, 0, 'order_idle_to_merchant_tpl_content_en', 'hi [restaurant_name]\n\nKindly take  action on Order ID : [order_Id]\n\nRegards\n- [sitename]'),
(76, 0, 'order_idle_to_merchant_sms_content_en', 'hi [restaurant_name]\n\nKindly take action on Order ID : [order_Id]\n\nRegards\n- [sitename]'),
(77, 0, 'order_idle_to_admin_tpl_subject_en', 'Order ID [order_id] has been IDLE for [idle_time]'),
(78, 0, 'order_idle_to_admin_tpl_content_en', 'hi admin\n\nKindly take action on Order ID : [order_id]\n\nRegards\n- [sitename]'),
(79, 0, 'order_idle_to_admin_sms_content_en', 'hi admin\n\nKindly take action on Order ID : [order_Id]\n\nRegards\n- [sitename]'),
(80, 0, 'merchant_invoice_email', '1'),
(81, 0, 'merchant_invoice_tpl_subject_en', 'Your new Invoice : [invoice_number]'),
(82, 0, 'merchant_invoice_tpl_content_en', 'hi [restaurant_name]\n\nyour invoice is now ready\nInvoice number : [invoice_number]\nTerms : [terms]\nPeriod : [period]\n\nYou can donwload your invoice below\n[invoice_link]\n\nRegards\n - [sitename]'),
(83, 0, 'admin_decimal_place', '2'),
(84, 0, 'installation_done', '1'),
(85, 0, 'website_disbaled_auto_cart', ''),
(86, 0, 'website_enabled_mobile_verification', ''),
(87, 0, 'website_date_picker_format', 'yy-mm-dd'),
(88, 0, 'website_time_picker_format', '24'),
(89, 0, 'website_date_format', ''),
(90, 0, 'website_time_format', ''),
(91, 0, 'merchant_sigup_status', 'pending'),
(92, 0, 'merchant_email_verification', ''),
(93, 0, 'merchant_payment_enabled', ''),
(94, 0, 'admin_enabled_paypal', ''),
(95, 0, 'admin_enabled_card', ''),
(96, 0, 'admin_currency_set', 'INR'),
(97, 0, 'home_search_text', ''),
(98, 0, 'home_search_subtext', ''),
(99, 0, 'home_search_mode', ''),
(100, 0, 'website_logo', '1593859087-lb_logo.png'),
(101, 0, 'admin_use_separators', ''),
(102, 0, 'google_auto_address', ''),
(103, 0, 'home_search_radius', ''),
(104, 0, 'home_search_unit_type', 'mi'),
(105, 0, 'google_default_country', ''),
(106, 0, 'enabled_advance_search', ''),
(107, 0, 'disabled_share_location', ''),
(108, 0, 'enabled_search_map', ''),
(109, 0, 'admin_currency_position', 'left'),
(110, 0, 'merchant_default_country', 'IN'),
(111, 0, 'merchant_specific_country', ''),
(112, 0, 'map_marker', ''),
(113, 0, 'global_admin_sender_email', 'no-reply@liquorboutique.com'),
(114, 0, 'merchant_disabled_registration', ''),
(115, 0, 'disabled_subscription', ''),
(116, 0, 'disabled_featured_merchant', ''),
(117, 0, 'merchant_days_can_edit_status', ''),
(118, 0, 'disabled_website_ordering', ''),
(119, 0, 'admin_activated_menu', ''),
(120, 0, 'website_hide_foodprice', ''),
(121, 0, 'disabled_cc_management', ''),
(122, 0, 'merchant_reg_abn', ''),
(123, 0, 'spicydish', ''),
(124, 0, 'website_timezone', ''),
(125, 0, 'website_admin_mutiple_login', ''),
(126, 0, 'website_merchant_mutiple_login', ''),
(127, 0, 'website_disabled_guest_checkout', ''),
(128, 0, 'website_reviews_actual_purchase', ''),
(129, 0, 'website_terms_merchant', ''),
(130, 0, 'website_terms_merchant_url', ''),
(131, 0, 'website_terms_customer', ''),
(132, 0, 'website_terms_customer_url', ''),
(133, 0, 'admin_thousand_separator', ''),
(134, 0, 'admin_decimal_separator', ''),
(135, 0, 'website_disabled_login_popup', ''),
(136, 0, 'merchant_can_edit_reviews', ''),
(137, 0, 'website_enabled_rcpt', ''),
(138, 0, 'website_receipt_logo', ''),
(139, 0, 'disabled_cart_sticky', ''),
(140, 0, 'search_result_bydistance', ''),
(141, 0, 'google_geo_api_key', ''),
(142, 0, 'website_enabled_map_address', ''),
(143, 0, 'client_custom_field_name1', ''),
(144, 0, 'client_custom_field_name2', ''),
(145, 0, 'merchant_days_can_edit_status_basedon', '1'),
(146, 0, 'merchant_status_disabled', ''),
(147, 0, 'view_map_disabled', ''),
(148, 0, 'view_map_default_zoom', ''),
(149, 0, 'view_map_default_zoom_s', ''),
(150, 0, 'receipt_default_subject', ''),
(151, 0, 'merchant_tbl_book_disabled', '2'),
(152, 0, 'merchant_changeorder_sms', ''),
(153, 0, 'customer_ask_address', ''),
(154, 0, 'captcha_site_key', ''),
(155, 0, 'captcha_secret', ''),
(156, 0, 'captcha_lang', ''),
(157, 0, 'captcha_customer_signup', ''),
(158, 0, 'captcha_merchant_signup', ''),
(159, 0, 'captcha_customer_login', ''),
(160, 0, 'captcha_merchant_login', ''),
(161, 0, 'captcha_admin_login', ''),
(162, 0, 'captcha_order', ''),
(163, 0, 'blocked_email_add', ''),
(164, 0, 'blocked_mobile', ''),
(165, 0, 'admin_zipcode_searchtype', '1'),
(166, 0, 'mobilelogo', ''),
(167, 0, 'theme_enabled_email_verification', ''),
(168, 0, 'google_distance_method', 'straight_line'),
(169, 0, 'google_use_curl', ''),
(170, 0, 'admin_menu_allowed_merchant', ''),
(171, 0, 'admin_printing_receipt_width', ''),
(172, 0, 'admin_printing_receipt_size', ''),
(173, 0, 'enabled_merchant_check_closing_time', ''),
(174, 0, 'admin_add_space_between_price', '1'),
(175, 0, 'enabled_food_search_menu', '1'),
(176, 0, 'location_default_country', '101'),
(177, 0, 'cod_change_required', ''),
(178, 0, 'website_disabled_cart_validation', '1'),
(179, 0, 'cancel_order_days_applied', ''),
(180, 0, 'cancel_order_status_accepted', '1'),
(181, 0, 'cancel_order_enabled', ''),
(182, 0, 'website_review_type', '1'),
(183, 0, 'review_baseon_status', '1'),
(184, 0, 'website_use_date_picker', '1'),
(185, 0, 'website_use_time_picker', '1'),
(186, 0, 'website_time_picker_interval', ''),
(187, 0, 'mapbox_access_token', ''),
(188, 0, 'map_provider', 'google.maps'),
(189, 0, 'mapbox_default_zoom', ''),
(190, 0, 'earn_points_review_status', '1'),
(191, 0, 'earn_points_booking_status', '1'),
(192, 0, 'website_review_approved_status', 'pending'),
(193, 0, 'cancel_order_hours', '0'),
(194, 0, 'cancel_order_minutes', '0'),
(195, 0, 'website_review_decline_status', ''),
(196, 0, 'publish_review_status', '1'),
(197, 0, 'book_table_earn_status', '1'),
(198, 0, 'pre_configure_size', ''),
(199, 0, 'enabled_map_selection_delivery', ''),
(200, 0, 'disabled_order_confirm_page', ''),
(201, 0, 'review_merchant_can_add_review_status', ''),
(202, 0, 'theme_hide_logo', ''),
(203, 0, 'theme_hide_how_works', ''),
(204, 0, 'theme_hide_cuisine', '2'),
(205, 0, 'social_flag', ''),
(206, 0, 'theme_custom_footer', ''),
(207, 0, 'theme_show_app', ''),
(208, 0, 'theme_app_android', ''),
(209, 0, 'theme_app_ios', ''),
(210, 0, 'theme_app_windows', ''),
(211, 0, 'theme_filter_colapse', ''),
(212, 0, 'theme_list_style', 'gridview'),
(213, 0, 'theme_menu_colapse', ''),
(214, 0, 'theme_top_menu', '[\\\"browse\\\",\\\"resto_signup\\\",\\\"contact\\\",\\\"signup\\\"]'),
(215, 0, 'show_language', '1'),
(216, 0, 'theme_promo_tab', ''),
(217, 0, 'theme_hours_tab', ''),
(218, 0, 'theme_reviews_tab', ''),
(219, 0, 'theme_map_tab', ''),
(220, 0, 'theme_info_tab', ''),
(221, 0, 'theme_photos_tab', ''),
(222, 0, 'cookie_law_enabled', ''),
(223, 0, 'cookie_accept_text', ''),
(224, 0, 'cookie_info_text', ''),
(225, 0, 'cookie_msg_text', ''),
(226, 0, 'cookie_info_link', ''),
(227, 0, 'theme_search_merchant_name', ''),
(228, 0, 'theme_search_street_name', ''),
(229, 0, 'theme_search_cuisine', ''),
(230, 0, 'theme_search_foodname', ''),
(231, 0, 'theme_compression', ''),
(232, 0, 'theme_search_merchant_address', ''),
(233, 0, 'theme_lang_pos', 'bottom'),
(234, 0, 'theme_hide_footer_section1', ''),
(235, 0, 'theme_hide_footer_section2', ''),
(236, 0, 'theme_time_pick', ''),
(237, 0, 'featured_merchant_sort', ''),
(238, 0, 'age_restriction', ''),
(239, 0, 'age_restriction_content', ''),
(240, 0, 'age_restriction_exit_link', ''),
(241, 0, 'browse_page_sort', ''),
(242, 0, 'theme_enabled_rtl', ''),
(243, 1, 'merchant_switch_master_cod', ''),
(244, 1, 'merchant_switch_master_ccr', ''),
(245, 1, 'merchant_switch_master_pyr', ''),
(246, 1, 'merchant_latitude', '12.8838837'),
(247, 1, 'merchant_longtitude', '77.5887602'),
(248, 1, 'merchant_master_table_boooking', '1'),
(249, 1, 'disabled_single_app_modules', ''),
(250, 1, 'merchant_master_disabled_ordering', ''),
(251, 1, 'merchant_minimum_order', '199'),
(252, 1, 'merchant_tax', ''),
(253, 1, 'merchant_delivery_charges', ''),
(254, 1, 'stores_open_day', ''),
(255, 1, 'stores_open_starts', '{\\\"monday\\\":\\\"\\\",\\\"tuesday\\\":\\\"\\\",\\\"wednesday\\\":\\\"\\\",\\\"thursday\\\":\\\"\\\",\\\"friday\\\":\\\"\\\",\\\"saturday\\\":\\\"\\\",\\\"sunday\\\":\\\"\\\"}'),
(256, 1, 'stores_open_ends', '{\\\"monday\\\":\\\"\\\",\\\"tuesday\\\":\\\"\\\",\\\"wednesday\\\":\\\"\\\",\\\"thursday\\\":\\\"\\\",\\\"friday\\\":\\\"\\\",\\\"saturday\\\":\\\"\\\",\\\"sunday\\\":\\\"\\\"}'),
(257, 1, 'stores_open_custom_text', '{\\\"monday\\\":\\\"\\\",\\\"tuesday\\\":\\\"\\\",\\\"wednesday\\\":\\\"\\\",\\\"thursday\\\":\\\"\\\",\\\"friday\\\":\\\"\\\",\\\"saturday\\\":\\\"\\\",\\\"sunday\\\":\\\"\\\"}'),
(258, 1, 'merchant_photo', '1593865244-madhuloka.png'),
(259, 1, 'merchant_delivery_estimation', ''),
(260, 1, 'merchant_delivery_miles', ''),
(261, 1, 'merchant_photo_bg', '1593865330-madhuloka_banner.jpg'),
(262, 1, 'merchant_disabled_cod', ''),
(263, 1, 'merchant_disabled_ccr', ''),
(264, 1, 'merchant_extenal', ''),
(265, 1, 'merchant_enabled_voucher', ''),
(266, 1, 'merchant_distance_type', 'mi'),
(267, 1, 'merchant_timezone', ''),
(268, 1, 'merchant_close_msg', ''),
(269, 1, 'merchant_preorder', ''),
(270, 1, 'merchant_maximum_order', ''),
(271, 1, 'merchant_packaging_charge', ''),
(272, 1, 'merchant_close_msg_holiday', ''),
(273, 1, 'merchant_holiday', '[\\\"\\\"]'),
(274, 1, 'merchant_activated_menu', ''),
(275, 1, 'spicydish', ''),
(276, 1, 'merchant_required_delivery_time', ''),
(277, 1, 'merchant_close_store', ''),
(278, 1, 'merchant_packaging_increment', ''),
(279, 1, 'merchant_show_time', ''),
(280, 1, 'merchant_enabled_tip', ''),
(281, 1, 'merchant_tip_default', ''),
(282, 1, 'merchant_minimum_order_pickup', ''),
(283, 1, 'merchant_maximum_order_pickup', ''),
(284, 1, 'merchant_disabled_ordering', ''),
(285, 1, 'merchant_tax_charges', ''),
(286, 1, 'stores_open_pm_start', '{\\\"monday\\\":\\\"\\\",\\\"tuesday\\\":\\\"\\\",\\\"wednesday\\\":\\\"\\\",\\\"thursday\\\":\\\"\\\",\\\"friday\\\":\\\"\\\",\\\"saturday\\\":\\\"\\\",\\\"sunday\\\":\\\"\\\"}'),
(287, 1, 'stores_open_pm_ends', '{\\\"monday\\\":\\\"\\\",\\\"tuesday\\\":\\\"\\\",\\\"wednesday\\\":\\\"\\\",\\\"thursday\\\":\\\"\\\",\\\"friday\\\":\\\"\\\",\\\"saturday\\\":\\\"\\\",\\\"sunday\\\":\\\"\\\"}'),
(288, 1, 'food_option_not_available', ''),
(289, 1, 'order_verification', ''),
(290, 1, 'order_sms_code_waiting', ''),
(291, 1, 'disabled_food_gallery', ''),
(292, 1, 'merchant_apply_tax', ''),
(293, 1, 'printing_receipt_width', ''),
(294, 1, 'printing_receipt_size', ''),
(295, 1, 'free_delivery_above_price', ''),
(296, 1, 'merchant_minimum_order_dinein', ''),
(297, 1, 'merchant_maximum_order_dinein', ''),
(298, 1, 'food_viewing_private', ''),
(299, 1, 'merchant_tax_number', ''),
(300, 1, 'merchant_packaging_wise', ''),
(301, 1, 'merchant_show_category_image', ''),
(302, 1, 'merchant_two_flavor_option', '1'),
(303, 0, 'mobile2_home_cuisine', '1'),
(304, 0, 'mobile2_home_all_restaurant', '1'),
(305, 0, 'mobileapp2_merchant_list_type', '1'),
(306, 0, 'mobileapp2_merchant_menu_type', '3'),
(307, 0, 'mobileapp2_distance_results', '1'),
(308, 0, 'mobile2_search_data', '{\\\"1\\\":\\\"open_tag\\\",\\\"2\\\":\\\"review\\\",\\\"3\\\":\\\"cuisine\\\",\\\"5\\\":\\\"minimum_order\\\",\\\"6\\\":\\\"distace\\\",\\\"8\\\":\\\"delivery_fee\\\"}'),
(309, 0, 'mobileapp2_order_processing', '[\\\"pending\\\",\\\"paid\\\",\\\"accepted\\\",\\\"acknowledged\\\",\\\"started\\\",\\\"inprogress\\\"]'),
(310, 0, 'mobileapp2_order_completed', '[\\\"delivered\\\",\\\"successful\\\"]'),
(311, 0, 'mobileapp2_order_cancelled', '[\\\"cancelled\\\",\\\"decline\\\",\\\"failed\\\"]'),
(312, 0, 'mobileapp2_api_has_key', '1bdbbcef62c97faab7d59a02985672c0');

-- --------------------------------------------------------

--
-- Table structure for table `mt_order`
--

CREATE TABLE `mt_order` (
  `order_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `json_details` text,
  `trans_type` varchar(100) NOT NULL DEFAULT '',
  `payment_type` varchar(100) NOT NULL DEFAULT '',
  `sub_total` float(14,4) NOT NULL DEFAULT '0.0000',
  `tax` float(14,4) NOT NULL DEFAULT '0.0000',
  `taxable_total` decimal(14,4) NOT NULL,
  `total_w_tax` float(14,4) NOT NULL DEFAULT '0.0000',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `stats_id` int(14) NOT NULL DEFAULT '0',
  `viewed` int(1) NOT NULL DEFAULT '1',
  `delivery_charge` float(14,4) NOT NULL DEFAULT '0.0000',
  `delivery_date` date DEFAULT NULL,
  `delivery_time` varchar(100) NOT NULL DEFAULT '',
  `delivery_asap` varchar(14) NOT NULL DEFAULT '',
  `delivery_instruction` varchar(255) NOT NULL DEFAULT '',
  `voucher_code` varchar(100) NOT NULL DEFAULT '',
  `voucher_amount` float(14,4) NOT NULL DEFAULT '0.0000',
  `voucher_type` varchar(100) NOT NULL DEFAULT '',
  `cc_id` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `order_change` float(14,4) NOT NULL DEFAULT '0.0000',
  `payment_provider_name` varchar(255) NOT NULL DEFAULT '',
  `discounted_amount` float(14,5) NOT NULL DEFAULT '0.00000',
  `discount_percentage` float(14,5) NOT NULL DEFAULT '0.00000',
  `percent_commision` float(14,4) NOT NULL DEFAULT '0.0000',
  `total_commission` float(14,4) NOT NULL DEFAULT '0.0000',
  `commision_ontop` int(2) NOT NULL DEFAULT '2',
  `merchant_earnings` float(14,4) NOT NULL DEFAULT '0.0000',
  `packaging` float(14,4) NOT NULL DEFAULT '0.0000',
  `cart_tip_percentage` float(14,4) NOT NULL DEFAULT '0.0000',
  `cart_tip_value` float(14,4) NOT NULL DEFAULT '0.0000',
  `card_fee` float(14,4) NOT NULL DEFAULT '0.0000',
  `donot_apply_tax_delivery` int(1) NOT NULL DEFAULT '1',
  `order_locked` int(1) NOT NULL DEFAULT '1',
  `request_from` varchar(10) NOT NULL DEFAULT 'web',
  `mobile_cart_details` text,
  `points_discount` float(14,4) NOT NULL DEFAULT '0.0000',
  `apply_food_tax` int(1) NOT NULL DEFAULT '0',
  `order_id_token` varchar(100) NOT NULL DEFAULT '',
  `admin_viewed` int(1) NOT NULL DEFAULT '0',
  `merchantapp_viewed` int(1) NOT NULL DEFAULT '0',
  `dinein_number_of_guest` varchar(14) NOT NULL DEFAULT '',
  `dinein_special_instruction` varchar(255) NOT NULL DEFAULT '',
  `critical` int(1) NOT NULL DEFAULT '1',
  `commision_type` varchar(50) NOT NULL DEFAULT 'percentage',
  `calculation_method` int(1) NOT NULL DEFAULT '1',
  `request_cancel` int(1) NOT NULL DEFAULT '2',
  `request_cancel_viewed` int(1) NOT NULL DEFAULT '2',
  `request_cancel_status` varchar(255) NOT NULL DEFAULT 'pending',
  `sofort_trans_id` varchar(255) NOT NULL DEFAULT '',
  `dinein_table_number` varchar(50) NOT NULL DEFAULT '',
  `payment_gateway_ref` varchar(255) NOT NULL DEFAULT '',
  `cancel_reason` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_order_delivery_address`
--

CREATE TABLE `mt_order_delivery_address` (
  `id` int(14) NOT NULL,
  `order_id` int(14) NOT NULL DEFAULT '0',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `street` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(255) NOT NULL DEFAULT '',
  `location_name` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `contact_phone` varchar(100) NOT NULL DEFAULT '',
  `formatted_address` text,
  `google_lat` varchar(50) NOT NULL DEFAULT '',
  `google_lng` varchar(50) NOT NULL DEFAULT '',
  `area_name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_order_details`
--

CREATE TABLE `mt_order_details` (
  `id` int(14) NOT NULL,
  `order_id` int(14) NOT NULL DEFAULT '0',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `item_id` int(14) NOT NULL DEFAULT '0',
  `item_name` varchar(255) NOT NULL DEFAULT '',
  `order_notes` text,
  `normal_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `discounted_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `size` varchar(255) NOT NULL DEFAULT '',
  `qty` int(14) NOT NULL DEFAULT '0',
  `cooking_ref` varchar(255) NOT NULL DEFAULT '',
  `addon` text,
  `ingredients` text,
  `non_taxable` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_order_history`
--

CREATE TABLE `mt_order_history` (
  `id` int(14) NOT NULL,
  `order_id` int(14) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '',
  `remarks` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `task_id` int(14) NOT NULL DEFAULT '0',
  `reason` text,
  `customer_signature` varchar(255) NOT NULL DEFAULT '',
  `notification_viewed` int(1) NOT NULL DEFAULT '2',
  `driver_id` int(14) NOT NULL DEFAULT '0',
  `driver_location_lat` varchar(50) NOT NULL DEFAULT '',
  `driver_location_lng` varchar(50) NOT NULL DEFAULT '',
  `remarks2` varchar(255) NOT NULL DEFAULT '',
  `remarks_args` varchar(255) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL DEFAULT '',
  `photo_task_id` int(14) NOT NULL DEFAULT '0',
  `receive_by` varchar(255) NOT NULL DEFAULT '',
  `signature_base30` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_order_sms`
--

CREATE TABLE `mt_order_sms` (
  `id` int(14) NOT NULL,
  `mobile` varchar(50) NOT NULL DEFAULT '',
  `code` int(4) NOT NULL,
  `session` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_order_status`
--

CREATE TABLE `mt_order_status` (
  `stats_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `description` varchar(200) NOT NULL DEFAULT '',
  `date_created` date DEFAULT NULL,
  `date_modified` date DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_order_status`
--

INSERT INTO `mt_order_status` (`stats_id`, `merchant_id`, `description`, `date_created`, `date_modified`, `ip_address`) VALUES
(1, 0, 'pending', '2020-07-03', NULL, '106.51.104.28'),
(2, 0, 'cancelled', '2020-07-03', NULL, '106.51.104.28'),
(3, 0, 'delivered', '2020-07-03', NULL, '106.51.104.28'),
(4, 0, 'paid', '2020-07-03', NULL, '106.51.104.28'),
(5, 0, 'accepted', '2020-07-03', NULL, '106.51.104.28'),
(6, 0, 'decline', '2020-07-03', NULL, '106.51.104.28'),
(7, 0, 'failed', '2020-07-03', NULL, '106.51.104.28'),
(8, 0, 'declined', '2020-07-03', NULL, '106.51.104.28'),
(9, 0, 'acknowledged', '2020-07-03', NULL, '106.51.104.28'),
(10, 0, 'started', '2020-07-03', NULL, '106.51.104.28'),
(11, 0, 'inprogress', '2020-07-03', NULL, '106.51.104.28'),
(12, 0, 'successful', '2020-07-03', NULL, '106.51.104.28');

-- --------------------------------------------------------

--
-- Table structure for table `mt_packages`
--

CREATE TABLE `mt_packages` (
  `package_id` int(14) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `price` float(14,4) NOT NULL DEFAULT '0.0000',
  `promo_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `expiration` int(14) NOT NULL DEFAULT '0',
  `expiration_type` varchar(50) NOT NULL DEFAULT 'days',
  `unlimited_post` int(1) NOT NULL DEFAULT '2',
  `post_limit` int(14) NOT NULL DEFAULT '0',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `sell_limit` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_package_trans`
--

CREATE TABLE `mt_package_trans` (
  `id` int(14) NOT NULL,
  `package_id` int(14) NOT NULL DEFAULT '0',
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `price` float(14,4) NOT NULL DEFAULT '0.0000',
  `payment_type` varchar(100) NOT NULL DEFAULT '',
  `mt_id` int(14) NOT NULL DEFAULT '0',
  `TOKEN` varchar(255) NOT NULL DEFAULT '',
  `membership_expired` date DEFAULT NULL,
  `TRANSACTIONID` varchar(255) NOT NULL DEFAULT '',
  `TRANSACTIONTYPE` varchar(100) NOT NULL DEFAULT '',
  `PAYMENTSTATUS` varchar(100) NOT NULL DEFAULT '',
  `PAYPALFULLRESPONSE` text,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `fee` float(14,4) NOT NULL DEFAULT '0.0000',
  `merchant_ref` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_payment_order`
--

CREATE TABLE `mt_payment_order` (
  `id` int(14) NOT NULL,
  `payment_type` varchar(100) NOT NULL DEFAULT '',
  `payment_reference` varchar(255) NOT NULL DEFAULT '',
  `order_id` int(14) NOT NULL DEFAULT '0',
  `raw_response` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL,
  `merchant_ref` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_payment_provider`
--

CREATE TABLE `mt_payment_provider` (
  `id` int(14) NOT NULL,
  `payment_name` varchar(255) NOT NULL DEFAULT '',
  `payment_logo` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'publish',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_paypal_checkout`
--

CREATE TABLE `mt_paypal_checkout` (
  `id` int(14) NOT NULL,
  `order_id` int(14) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL DEFAULT '',
  `paypal_request` text,
  `paypal_response` text,
  `status` varchar(255) NOT NULL DEFAULT 'checkout',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_paypal_payment`
--

CREATE TABLE `mt_paypal_payment` (
  `id` int(14) NOT NULL,
  `order_id` int(14) NOT NULL DEFAULT '0',
  `TOKEN` varchar(255) NOT NULL DEFAULT '',
  `TRANSACTIONID` varchar(100) NOT NULL DEFAULT '',
  `TRANSACTIONTYPE` varchar(100) NOT NULL DEFAULT '',
  `PAYMENTTYPE` varchar(100) NOT NULL DEFAULT '',
  `ORDERTIME` varchar(100) NOT NULL DEFAULT '',
  `AMT` varchar(14) NOT NULL DEFAULT '',
  `FEEAMT` varchar(14) NOT NULL DEFAULT '',
  `TAXAMT` varchar(14) NOT NULL DEFAULT '',
  `CURRENCYCODE` varchar(3) NOT NULL DEFAULT '',
  `PAYMENTSTATUS` varchar(100) NOT NULL DEFAULT '',
  `CORRELATIONID` varchar(100) NOT NULL DEFAULT '',
  `TIMESTAMP` varchar(100) NOT NULL DEFAULT '',
  `json_details` text,
  `date_created` varchar(50) NOT NULL DEFAULT '',
  `ip_address` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_rating`
--

CREATE TABLE `mt_rating` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `ratings` float(14,1) NOT NULL DEFAULT '0.0',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_rating_meaning`
--

CREATE TABLE `mt_rating_meaning` (
  `id` int(14) NOT NULL,
  `rating_start` float(14,1) NOT NULL DEFAULT '0.0',
  `rating_end` float(14,1) NOT NULL DEFAULT '0.0',
  `meaning` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_rating_meaning`
--

INSERT INTO `mt_rating_meaning` (`id`, `rating_start`, `rating_end`, `meaning`, `date_created`, `date_modified`, `ip_address`) VALUES
(1, 1.0, 1.9, 'poor', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
(2, 2.0, 2.9, 'good', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
(3, 3.0, 4.0, 'very good', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28'),
(4, 4.1, 5.0, 'excellent', '2020-07-03 08:29:30', '2020-07-03 06:29:30', '106.51.104.28');

-- --------------------------------------------------------

--
-- Table structure for table `mt_receive_post`
--

CREATE TABLE `mt_receive_post` (
  `id` int(14) NOT NULL,
  `payment_type` varchar(3) NOT NULL DEFAULT '',
  `receive_post` text,
  `status` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_review`
--

CREATE TABLE `mt_review` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `review` text,
  `rating` float(14,1) NOT NULL DEFAULT '0.0',
  `status` varchar(100) NOT NULL DEFAULT 'publish',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `order_id` varchar(14) NOT NULL DEFAULT '',
  `parent_id` int(14) NOT NULL DEFAULT '0',
  `reply_from` varchar(255) NOT NULL DEFAULT '',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `as_anonymous` varchar(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_shipping_rate`
--

CREATE TABLE `mt_shipping_rate` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `distance_from` float(14,2) NOT NULL DEFAULT '0.00',
  `distance_to` float(14,2) NOT NULL DEFAULT '0.00',
  `shipping_units` varchar(5) NOT NULL DEFAULT '',
  `distance_price` float(14,4) NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_size`
--

CREATE TABLE `mt_size` (
  `size_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `size_name` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'published',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `size_name_trans` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mt_size`
--

INSERT INTO `mt_size` (`size_id`, `merchant_id`, `size_name`, `sequence`, `status`, `date_created`, `date_modified`, `ip_address`, `size_name_trans`) VALUES
(1, 1, 'small', 0, 'publish', '2020-07-04 14:18:20', '2020-07-04 12:18:20', '49.207.62.216', ''),
(2, 1, 'medium', 0, 'publish', '2020-07-04 14:18:20', '2020-07-04 12:18:20', '49.207.62.216', ''),
(3, 1, 'large', 0, 'publish', '2020-07-04 14:18:20', '2020-07-04 12:18:20', '49.207.62.216', '');

-- --------------------------------------------------------

--
-- Table structure for table `mt_sms_broadcast`
--

CREATE TABLE `mt_sms_broadcast` (
  `broadcast_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `send_to` int(14) NOT NULL DEFAULT '0',
  `list_mobile_number` text NOT NULL,
  `sms_alert_message` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_sms_broadcast_details`
--

CREATE TABLE `mt_sms_broadcast_details` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `broadcast_id` int(14) NOT NULL DEFAULT '0',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `client_name` varchar(255) NOT NULL DEFAULT '',
  `contact_phone` varchar(50) NOT NULL DEFAULT '',
  `sms_message` text,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `gateway_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_executed` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `gateway` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_sms_package`
--

CREATE TABLE `mt_sms_package` (
  `sms_package_id` int(14) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `price` float(14,4) NOT NULL DEFAULT '0.0000',
  `promo_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `sms_limit` int(14) NOT NULL DEFAULT '0',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_sms_package_trans`
--

CREATE TABLE `mt_sms_package_trans` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `sms_package_id` int(14) NOT NULL DEFAULT '0',
  `payment_type` varchar(50) NOT NULL DEFAULT '',
  `package_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `sms_limit` int(14) NOT NULL DEFAULT '0',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) NOT NULL DEFAULT '',
  `payment_gateway_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `cc_id` int(14) NOT NULL DEFAULT '0',
  `merchant_ref` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_sms_trans_logs`
--

CREATE TABLE `mt_sms_trans_logs` (
  `id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `sms_package_id` int(14) NOT NULL DEFAULT '0',
  `payment_type` varchar(100) NOT NULL DEFAULT '',
  `package_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `sms_limit` varchar(14) NOT NULL DEFAULT '',
  `payment_reference` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_gateway_ref` varchar(255) NOT NULL DEFAULT '',
  `gateway_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_stripe_logger`
--

CREATE TABLE `mt_stripe_logger` (
  `id` int(14) NOT NULL,
  `trans_type` varchar(255) NOT NULL DEFAULT '',
  `payment_intent` varchar(255) NOT NULL DEFAULT '',
  `post_receive` text,
  `webhooks_response` text,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_receive_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_stripe_logs`
--

CREATE TABLE `mt_stripe_logs` (
  `id` int(14) NOT NULL,
  `order_id` int(14) NOT NULL,
  `json_result` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_subcategory`
--

CREATE TABLE `mt_subcategory` (
  `subcat_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `subcategory_name` varchar(255) NOT NULL DEFAULT '',
  `subcategory_description` text,
  `discount` varchar(20) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'publish',
  `subcategory_name_trans` text,
  `subcategory_description_trans` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_subcategory_item`
--

CREATE TABLE `mt_subcategory_item` (
  `sub_item_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `sub_item_name` varchar(255) NOT NULL DEFAULT '',
  `item_description` text,
  `category` varchar(255) NOT NULL DEFAULT '',
  `price` varchar(15) NOT NULL DEFAULT '',
  `photo` varchar(255) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `sub_item_name_trans` text,
  `item_description_trans` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `mt_view_location_rate`
-- (See below for the actual view)
--
CREATE TABLE `mt_view_location_rate` (
`rate_id` int(14)
,`merchant_id` int(14)
,`country_id` int(14)
,`country_name` varchar(150)
,`state_id` int(14)
,`state_name` varchar(30)
,`city_id` int(14)
,`city_name` varchar(30)
,`postal_code` varchar(255)
,`area_id` int(14)
,`area_name` varchar(255)
,`fee` float(14,5)
,`sequence` int(14)
,`date_created` datetime
,`date_modified` datetime
,`ip_address` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mt_view_merchant`
-- (See below for the actual view)
--
CREATE TABLE `mt_view_merchant` (
`merchant_id` int(14)
,`restaurant_slug` varchar(255)
,`restaurant_name` varchar(255)
,`restaurant_phone` varchar(100)
,`contact_name` varchar(255)
,`contact_phone` varchar(100)
,`contact_email` varchar(255)
,`country_code` varchar(3)
,`street` text
,`city` varchar(255)
,`state` varchar(255)
,`post_code` varchar(100)
,`cuisine` text
,`service` varchar(255)
,`free_delivery` int(1)
,`delivery_estimation` varchar(100)
,`username` varchar(100)
,`password` varchar(100)
,`activation_key` varchar(50)
,`activation_token` varchar(255)
,`status` varchar(100)
,`date_created` datetime
,`date_modified` datetime
,`date_activated` datetime
,`last_login` datetime
,`ip_address` varchar(50)
,`package_id` int(14)
,`package_price` float(14,5)
,`membership_expired` date
,`payment_steps` int(1)
,`is_featured` int(1)
,`is_ready` int(1)
,`is_sponsored` int(2)
,`sponsored_expiration` date
,`lost_password_code` varchar(50)
,`user_lang` int(14)
,`membership_purchase_date` datetime
,`sort_featured` int(14)
,`is_commission` int(1)
,`percent_commision` float(14,5)
,`abn` varchar(255)
,`session_token` varchar(255)
,`commision_type` varchar(50)
,`mobile_session_token` varchar(255)
,`merchant_key` varchar(255)
,`latitude` varchar(50)
,`lontitude` varchar(50)
,`delivery_charges` float(14,5)
,`minimum_order` float(14,5)
,`delivery_minimum_order` float(14,5)
,`delivery_maximum_order` float(14,5)
,`pickup_minimum_order` float(14,5)
,`pickup_maximum_order` float(14,5)
,`country_name` varchar(255)
,`country_id` int(14)
,`state_id` int(14)
,`city_id` int(14)
,`area_id` int(14)
,`logo` varchar(255)
,`merchant_type` int(1)
,`invoice_terms` int(14)
,`payment_gateway_ref` varchar(255)
,`user_access` text
,`ratings` double(22,5)
,`review_count` bigint(21)
,`ratings_votes` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mt_view_order_details`
-- (See below for the actual view)
--
CREATE TABLE `mt_view_order_details` (
`id` int(14)
,`order_id` int(14)
,`client_id` int(14)
,`item_id` int(14)
,`item_name` varchar(255)
,`order_notes` text
,`normal_price` float(14,4)
,`discounted_price` float(14,4)
,`size` varchar(255)
,`qty` int(14)
,`cooking_ref` varchar(255)
,`addon` text
,`ingredients` text
,`non_taxable` int(1)
,`merchant_id` int(14)
,`status` varchar(255)
,`date_created` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mt_view_ratings`
-- (See below for the actual view)
--
CREATE TABLE `mt_view_ratings` (
`merchant_id` int(14)
,`review_count` bigint(21)
,`ratings` double(22,5)
);

-- --------------------------------------------------------

--
-- Table structure for table `mt_voucher`
--

CREATE TABLE `mt_voucher` (
  `voucher_id` int(14) NOT NULL,
  `voucher_name` varchar(255) NOT NULL DEFAULT '',
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `number_of_voucher` int(14) NOT NULL DEFAULT '0',
  `amount` float NOT NULL,
  `voucher_type` varchar(100) NOT NULL DEFAULT 'fixed amount',
  `status` varchar(100) NOT NULL DEFAULT '',
  `date_created` varchar(50) NOT NULL DEFAULT '',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_voucher_list`
--

CREATE TABLE `mt_voucher_list` (
  `voucher_id` int(14) NOT NULL,
  `voucher_code` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT 'unused',
  `client_id` int(14) NOT NULL DEFAULT '0',
  `date_used` varchar(50) NOT NULL DEFAULT '',
  `order_id` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_voucher_new`
--

CREATE TABLE `mt_voucher_new` (
  `voucher_id` int(14) NOT NULL,
  `voucher_owner` varchar(255) NOT NULL DEFAULT 'merchant',
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `joining_merchant` text,
  `voucher_name` varchar(255) NOT NULL DEFAULT '',
  `voucher_type` varchar(255) NOT NULL DEFAULT '',
  `amount` float(14,4) NOT NULL DEFAULT '0.0000',
  `expiration` date DEFAULT NULL,
  `status` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(100) NOT NULL DEFAULT '',
  `used_once` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_withdrawal`
--

CREATE TABLE `mt_withdrawal` (
  `withdrawal_id` int(14) NOT NULL,
  `merchant_id` int(14) NOT NULL DEFAULT '0',
  `payment_type` varchar(100) NOT NULL DEFAULT '',
  `payment_method` varchar(100) NOT NULL DEFAULT '',
  `amount` float(14,4) NOT NULL DEFAULT '0.0000',
  `current_balance` float(14,4) NOT NULL DEFAULT '0.0000',
  `balance` float(14,4) NOT NULL DEFAULT '0.0000',
  `currency_code` varchar(3) NOT NULL DEFAULT '',
  `account` varchar(255) NOT NULL DEFAULT '',
  `account_name` varchar(255) NOT NULL DEFAULT '',
  `bank_account_number` varchar(255) NOT NULL DEFAULT '',
  `swift_code` varchar(100) NOT NULL DEFAULT '',
  `bank_name` varchar(255) NOT NULL DEFAULT '',
  `bank_branch` varchar(255) NOT NULL DEFAULT '',
  `bank_country` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `viewed` int(2) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_to_process` date DEFAULT NULL,
  `date_process` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `api_raw_response` text,
  `withdrawal_token` text,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `bank_type` varchar(255) NOT NULL DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mt_zipcode`
--

CREATE TABLE `mt_zipcode` (
  `zipcode_id` int(14) NOT NULL,
  `zipcode` varchar(255) NOT NULL DEFAULT '',
  `country_code` varchar(5) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `area` varchar(255) NOT NULL DEFAULT '',
  `stree_name` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `mt_mobile2_device_reg_view`
--
DROP TABLE IF EXISTS `mt_mobile2_device_reg_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mt_mobile2_device_reg_view`  AS  select `a`.`id` AS `id`,`a`.`client_id` AS `client_id`,`a`.`device_uiid` AS `device_uiid`,`a`.`device_id` AS `device_id`,`a`.`device_platform` AS `device_platform`,`a`.`push_enabled` AS `push_enabled`,`a`.`status` AS `status`,`a`.`code_version` AS `code_version`,`a`.`date_created` AS `date_created`,`a`.`date_modified` AS `date_modified`,`a`.`ip_address` AS `ip_address`,`a`.`subscribe_topic` AS `subscribe_topic`,concat(`b`.`first_name`,' ',`b`.`last_name`) AS `full_name`,`b`.`last_login` AS `last_login` from (`mt_mobile2_device_reg` `a` left join `mt_client` `b` on((`a`.`client_id` = `b`.`client_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `mt_view_location_rate`
--
DROP TABLE IF EXISTS `mt_view_location_rate`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mt_view_location_rate`  AS  select `a`.`rate_id` AS `rate_id`,`a`.`merchant_id` AS `merchant_id`,`a`.`country_id` AS `country_id`,`b`.`country_name` AS `country_name`,`a`.`state_id` AS `state_id`,`c`.`name` AS `state_name`,`a`.`city_id` AS `city_id`,`d`.`name` AS `city_name`,`d`.`postal_code` AS `postal_code`,`a`.`area_id` AS `area_id`,`e`.`name` AS `area_name`,`a`.`fee` AS `fee`,`a`.`sequence` AS `sequence`,`a`.`date_created` AS `date_created`,`a`.`date_modified` AS `date_modified`,`a`.`ip_address` AS `ip_address` from ((((`mt_location_rate` `a` left join `mt_location_countries` `b` on((`a`.`country_id` = `b`.`country_id`))) left join `mt_location_states` `c` on((`a`.`state_id` = `c`.`state_id`))) left join `mt_location_cities` `d` on((`a`.`city_id` = `d`.`city_id`))) left join `mt_location_area` `e` on((`a`.`area_id` = `e`.`area_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `mt_view_merchant`
--
DROP TABLE IF EXISTS `mt_view_merchant`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mt_view_merchant`  AS  select `a`.`merchant_id` AS `merchant_id`,`a`.`restaurant_slug` AS `restaurant_slug`,`a`.`restaurant_name` AS `restaurant_name`,`a`.`restaurant_phone` AS `restaurant_phone`,`a`.`contact_name` AS `contact_name`,`a`.`contact_phone` AS `contact_phone`,`a`.`contact_email` AS `contact_email`,`a`.`country_code` AS `country_code`,`a`.`street` AS `street`,`a`.`city` AS `city`,`a`.`state` AS `state`,`a`.`post_code` AS `post_code`,`a`.`cuisine` AS `cuisine`,`a`.`service` AS `service`,`a`.`free_delivery` AS `free_delivery`,`a`.`delivery_estimation` AS `delivery_estimation`,`a`.`username` AS `username`,`a`.`password` AS `password`,`a`.`activation_key` AS `activation_key`,`a`.`activation_token` AS `activation_token`,`a`.`status` AS `status`,`a`.`date_created` AS `date_created`,`a`.`date_modified` AS `date_modified`,`a`.`date_activated` AS `date_activated`,`a`.`last_login` AS `last_login`,`a`.`ip_address` AS `ip_address`,`a`.`package_id` AS `package_id`,`a`.`package_price` AS `package_price`,`a`.`membership_expired` AS `membership_expired`,`a`.`payment_steps` AS `payment_steps`,`a`.`is_featured` AS `is_featured`,`a`.`is_ready` AS `is_ready`,`a`.`is_sponsored` AS `is_sponsored`,`a`.`sponsored_expiration` AS `sponsored_expiration`,`a`.`lost_password_code` AS `lost_password_code`,`a`.`user_lang` AS `user_lang`,`a`.`membership_purchase_date` AS `membership_purchase_date`,`a`.`sort_featured` AS `sort_featured`,`a`.`is_commission` AS `is_commission`,`a`.`percent_commision` AS `percent_commision`,`a`.`abn` AS `abn`,`a`.`session_token` AS `session_token`,`a`.`commision_type` AS `commision_type`,`a`.`mobile_session_token` AS `mobile_session_token`,`a`.`merchant_key` AS `merchant_key`,`a`.`latitude` AS `latitude`,`a`.`lontitude` AS `lontitude`,`a`.`delivery_charges` AS `delivery_charges`,`a`.`minimum_order` AS `minimum_order`,`a`.`delivery_minimum_order` AS `delivery_minimum_order`,`a`.`delivery_maximum_order` AS `delivery_maximum_order`,`a`.`pickup_minimum_order` AS `pickup_minimum_order`,`a`.`pickup_maximum_order` AS `pickup_maximum_order`,`a`.`country_name` AS `country_name`,`a`.`country_id` AS `country_id`,`a`.`state_id` AS `state_id`,`a`.`city_id` AS `city_id`,`a`.`area_id` AS `area_id`,`a`.`logo` AS `logo`,`a`.`merchant_type` AS `merchant_type`,`a`.`invoice_terms` AS `invoice_terms`,`a`.`payment_gateway_ref` AS `payment_gateway_ref`,`a`.`user_access` AS `user_access`,ifnull(`f`.`ratings`,0) AS `ratings`,ifnull(`f`.`review_count`,0) AS `review_count`,ifnull(`f`.`review_count`,0) AS `ratings_votes` from (`mt_merchant` `a` left join `mt_view_ratings` `f` on((`a`.`merchant_id` = `f`.`merchant_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `mt_view_order_details`
--
DROP TABLE IF EXISTS `mt_view_order_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mt_view_order_details`  AS  select `a`.`id` AS `id`,`a`.`order_id` AS `order_id`,`a`.`client_id` AS `client_id`,`a`.`item_id` AS `item_id`,`a`.`item_name` AS `item_name`,`a`.`order_notes` AS `order_notes`,`a`.`normal_price` AS `normal_price`,`a`.`discounted_price` AS `discounted_price`,`a`.`size` AS `size`,`a`.`qty` AS `qty`,`a`.`cooking_ref` AS `cooking_ref`,`a`.`addon` AS `addon`,`a`.`ingredients` AS `ingredients`,`a`.`non_taxable` AS `non_taxable`,`b`.`merchant_id` AS `merchant_id`,`b`.`status` AS `status`,`b`.`date_created` AS `date_created` from (`mt_order_details` `a` left join `mt_order` `b` on((`a`.`order_id` = `b`.`order_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `mt_view_ratings`
--
DROP TABLE IF EXISTS `mt_view_ratings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mt_view_ratings`  AS  select `mt_review`.`merchant_id` AS `merchant_id`,count(0) AS `review_count`,(sum(`mt_review`.`rating`) / count(0)) AS `ratings` from `mt_review` where (`mt_review`.`status` in ('publish','published')) group by `mt_review`.`merchant_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mt_address_book`
--
ALTER TABLE `mt_address_book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `as_default` (`as_default`);

--
-- Indexes for table `mt_address_book_location`
--
ALTER TABLE `mt_address_book_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_admin_user`
--
ALTER TABLE `mt_admin_user`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `username` (`username`),
  ADD KEY `password` (`password`),
  ADD KEY `lost_password_code` (`lost_password_code`),
  ADD KEY `session_token` (`session_token`);

--
-- Indexes for table `mt_bank_deposit`
--
ALTER TABLE `mt_bank_deposit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `status` (`status`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `mt_barclay_trans`
--
ALTER TABLE `mt_barclay_trans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderid` (`orderid`),
  ADD KEY `token` (`token`),
  ADD KEY `transaction_type` (`transaction_type`);

--
-- Indexes for table `mt_bookingtable`
--
ALTER TABLE `mt_bookingtable`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `status` (`status`),
  ADD KEY `viewed` (`viewed`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `date_booking` (`date_booking`);

--
-- Indexes for table `mt_bookingtable_history`
--
ALTER TABLE `mt_bookingtable_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_category`
--
ALTER TABLE `mt_category`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `category_name` (`category_name`),
  ADD KEY `status` (`status`),
  ADD KEY `sequence` (`sequence`),
  ADD KEY `parent_cat_id` (`parent_cat_id`);

--
-- Indexes for table `mt_client`
--
ALTER TABLE `mt_client`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `social_strategy` (`social_strategy`),
  ADD KEY `email_address` (`email_address`),
  ADD KEY `password` (`password`),
  ADD KEY `street` (`street`),
  ADD KEY `city` (`city`),
  ADD KEY `state` (`state`),
  ADD KEY `contact_phone` (`contact_phone`),
  ADD KEY `lost_password_token` (`lost_password_token`),
  ADD KEY `status` (`status`),
  ADD KEY `token` (`token`),
  ADD KEY `mobile_verification_code` (`mobile_verification_code`),
  ADD KEY `is_guest` (`is_guest`),
  ADD KEY `email_verification_code` (`email_verification_code`);

--
-- Indexes for table `mt_client_cc`
--
ALTER TABLE `mt_client_cc`
  ADD PRIMARY KEY (`cc_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `mt_cooking_ref`
--
ALTER TABLE `mt_cooking_ref`
  ADD PRIMARY KEY (`cook_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `cooking_name` (`cooking_name`),
  ADD KEY `sequence` (`sequence`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_cuisine`
--
ALTER TABLE `mt_cuisine`
  ADD PRIMARY KEY (`cuisine_id`),
  ADD KEY `cuisine_name` (`cuisine_name`),
  ADD KEY `sequence` (`sequence`);

--
-- Indexes for table `mt_currency`
--
ALTER TABLE `mt_currency`
  ADD PRIMARY KEY (`currency_code`),
  ADD KEY `currency_symbol` (`currency_symbol`),
  ADD KEY `currency_code` (`currency_code`);

--
-- Indexes for table `mt_custom_page`
--
ALTER TABLE `mt_custom_page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug_name` (`slug_name`);

--
-- Indexes for table `mt_dishes`
--
ALTER TABLE `mt_dishes`
  ADD PRIMARY KEY (`dish_id`),
  ADD KEY `dish_name` (`dish_name`);

--
-- Indexes for table `mt_email_logs`
--
ALTER TABLE `mt_email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `module_type` (`module_type`),
  ADD KEY `email_address` (`email_address`);

--
-- Indexes for table `mt_epos_client`
--
ALTER TABLE `mt_epos_client`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `social_strategy` (`social_strategy`),
  ADD KEY `email_address` (`email_address`),
  ADD KEY `password` (`password`),
  ADD KEY `street` (`street`),
  ADD KEY `city` (`city`),
  ADD KEY `state` (`state`),
  ADD KEY `contact_phone` (`contact_phone`),
  ADD KEY `lost_password_token` (`lost_password_token`),
  ADD KEY `status` (`status`),
  ADD KEY `token` (`token`),
  ADD KEY `mobile_verification_code` (`mobile_verification_code`),
  ADD KEY `is_guest` (`is_guest`),
  ADD KEY `email_verification_code` (`email_verification_code`);

--
-- Indexes for table `mt_epos_order`
--
ALTER TABLE `mt_epos_order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `mt_epos_order_details`
--
ALTER TABLE `mt_epos_order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_favorites`
--
ALTER TABLE `mt_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_fax_broadcast`
--
ALTER TABLE `mt_fax_broadcast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`);

--
-- Indexes for table `mt_fax_package`
--
ALTER TABLE `mt_fax_package`
  ADD PRIMARY KEY (`fax_package_id`),
  ADD KEY `title` (`title`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_fax_package_trans`
--
ALTER TABLE `mt_fax_package_trans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_ingredients`
--
ALTER TABLE `mt_ingredients`
  ADD PRIMARY KEY (`ingredients_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `status` (`status`),
  ADD KEY `ingredients_name` (`ingredients_name`);

--
-- Indexes for table `mt_invoice`
--
ALTER TABLE `mt_invoice`
  ADD PRIMARY KEY (`invoice_number`),
  ADD KEY `invoice_token` (`invoice_token`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `status` (`status`),
  ADD KEY `date_from` (`date_from`),
  ADD KEY `date_to` (`date_to`),
  ADD KEY `invoice_total` (`invoice_total`);

--
-- Indexes for table `mt_invoice_history`
--
ALTER TABLE `mt_invoice_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_number` (`invoice_number`),
  ADD KEY `payment_status` (`payment_status`);

--
-- Indexes for table `mt_item`
--
ALTER TABLE `mt_item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `item_name` (`item_name`),
  ADD KEY `status` (`status`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `spicydish` (`spicydish`),
  ADD KEY `two_flavors` (`two_flavors`),
  ADD KEY `points_earned` (`points_earned`),
  ADD KEY `points_disabled` (`points_disabled`);

--
-- Indexes for table `mt_languages`
--
ALTER TABLE `mt_languages`
  ADD PRIMARY KEY (`lang_id`);

--
-- Indexes for table `mt_location_area`
--
ALTER TABLE `mt_location_area`
  ADD PRIMARY KEY (`area_id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `sequence` (`sequence`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `mt_location_cities`
--
ALTER TABLE `mt_location_cities`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `postal_code` (`postal_code`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `sequence` (`sequence`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `mt_location_countries`
--
ALTER TABLE `mt_location_countries`
  ADD PRIMARY KEY (`country_id`),
  ADD KEY `shortcode` (`shortcode`);

--
-- Indexes for table `mt_location_rate`
--
ALTER TABLE `mt_location_rate`
  ADD PRIMARY KEY (`rate_id`);

--
-- Indexes for table `mt_location_states`
--
ALTER TABLE `mt_location_states`
  ADD PRIMARY KEY (`state_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `sequence` (`sequence`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `mt_merchant`
--
ALTER TABLE `mt_merchant`
  ADD PRIMARY KEY (`merchant_id`),
  ADD KEY `restaurant_slug` (`restaurant_slug`),
  ADD KEY `restaurant_name` (`restaurant_name`),
  ADD KEY `post_code` (`post_code`),
  ADD KEY `service` (`service`),
  ADD KEY `username` (`username`),
  ADD KEY `password` (`password`),
  ADD KEY `status` (`status`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `payment_steps` (`payment_steps`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `is_ready` (`is_ready`),
  ADD KEY `is_sponsored` (`is_sponsored`),
  ADD KEY `is_commission` (`is_commission`),
  ADD KEY `percent_commision` (`percent_commision`),
  ADD KEY `session_token` (`session_token`),
  ADD KEY `commision_type` (`commision_type`),
  ADD KEY `delivery_charges` (`delivery_charges`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `mt_merchant_cc`
--
ALTER TABLE `mt_merchant_cc`
  ADD PRIMARY KEY (`mt_id`),
  ADD KEY `merchant_id` (`merchant_id`);

--
-- Indexes for table `mt_merchant_user`
--
ALTER TABLE `mt_merchant_user`
  ADD PRIMARY KEY (`merchant_user_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `username` (`username`),
  ADD KEY `password` (`password`),
  ADD KEY `status` (`status`),
  ADD KEY `session_token` (`session_token`),
  ADD KEY `mobile_session_token` (`mobile_session_token`),
  ADD KEY `lost_password_code` (`lost_password_code`);

--
-- Indexes for table `mt_minimum_table`
--
ALTER TABLE `mt_minimum_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`);

--
-- Indexes for table `mt_mobile2_broadcast`
--
ALTER TABLE `mt_mobile2_broadcast`
  ADD PRIMARY KEY (`broadcast_id`);

--
-- Indexes for table `mt_mobile2_cart`
--
ALTER TABLE `mt_mobile2_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `device_platform` (`device_platform`),
  ADD KEY `device_uiid` (`device_uiid`);

--
-- Indexes for table `mt_mobile2_device_reg`
--
ALTER TABLE `mt_mobile2_device_reg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `device_uiid` (`device_uiid`),
  ADD KEY `device_platform` (`device_platform`),
  ADD KEY `status` (`status`);
ALTER TABLE `mt_mobile2_device_reg` ADD FULLTEXT KEY `device_id` (`device_id`);

--
-- Indexes for table `mt_mobile2_homebanner`
--
ALTER TABLE `mt_mobile2_homebanner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `mt_mobile2_order_trigger`
--
ALTER TABLE `mt_mobile2_order_trigger`
  ADD PRIMARY KEY (`trigger_id`);

--
-- Indexes for table `mt_mobile2_pages`
--
ALTER TABLE `mt_mobile2_pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `mt_mobile2_push_logs`
--
ALTER TABLE `mt_mobile2_push_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_mobile2_recent_location`
--
ALTER TABLE `mt_mobile2_recent_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_uiid` (`device_uiid`);
ALTER TABLE `mt_mobile2_recent_location` ADD FULLTEXT KEY `search_address` (`search_address`);

--
-- Indexes for table `mt_mobile2_recent_search`
--
ALTER TABLE `mt_mobile2_recent_search`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_mobile_broadcast`
--
ALTER TABLE `mt_mobile_broadcast`
  ADD PRIMARY KEY (`broadcast_id`);

--
-- Indexes for table `mt_newsletter`
--
ALTER TABLE `mt_newsletter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_address` (`email_address`);

--
-- Indexes for table `mt_offers`
--
ALTER TABLE `mt_offers`
  ADD PRIMARY KEY (`offers_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_option`
--
ALTER TABLE `mt_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `option_name` (`option_name`);

--
-- Indexes for table `mt_order`
--
ALTER TABLE `mt_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `order_id_token` (`order_id_token`),
  ADD KEY `merchantapp_viewed` (`merchantapp_viewed`),
  ADD KEY `admin_viewed` (`admin_viewed`),
  ADD KEY `viewed` (`viewed`),
  ADD KEY `total_commission` (`total_commission`),
  ADD KEY `merchant_earnings` (`merchant_earnings`),
  ADD KEY `total_w_tax` (`total_w_tax`),
  ADD KEY `taxable_total` (`taxable_total`),
  ADD KEY `sub_total` (`sub_total`),
  ADD KEY `payment_type` (`payment_type`),
  ADD KEY `trans_type` (`trans_type`);

--
-- Indexes for table `mt_order_delivery_address`
--
ALTER TABLE `mt_order_delivery_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `google_lat` (`google_lat`),
  ADD KEY `google_lng` (`google_lng`);

--
-- Indexes for table `mt_order_details`
--
ALTER TABLE `mt_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `mt_order_history`
--
ALTER TABLE `mt_order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `status` (`status`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `driver_location_lat` (`driver_location_lat`),
  ADD KEY `driver_location_lng` (`driver_location_lng`);

--
-- Indexes for table `mt_order_sms`
--
ALTER TABLE `mt_order_sms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session` (`session`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `mt_order_status`
--
ALTER TABLE `mt_order_status`
  ADD PRIMARY KEY (`stats_id`),
  ADD KEY `merchant_id` (`merchant_id`);

--
-- Indexes for table `mt_packages`
--
ALTER TABLE `mt_packages`
  ADD PRIMARY KEY (`package_id`),
  ADD KEY `title` (`title`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_package_trans`
--
ALTER TABLE `mt_package_trans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `TRANSACTIONID` (`TRANSACTIONID`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_payment_order`
--
ALTER TABLE `mt_payment_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payment_type` (`payment_type`),
  ADD KEY `payment_reference` (`payment_reference`);

--
-- Indexes for table `mt_payment_provider`
--
ALTER TABLE `mt_payment_provider`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_name` (`payment_name`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_paypal_checkout`
--
ALTER TABLE `mt_paypal_checkout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `token` (`token`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_paypal_payment`
--
ALTER TABLE `mt_paypal_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_rating`
--
ALTER TABLE `mt_rating`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `ratings` (`ratings`);

--
-- Indexes for table `mt_rating_meaning`
--
ALTER TABLE `mt_rating_meaning`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_receive_post`
--
ALTER TABLE `mt_receive_post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_review`
--
ALTER TABLE `mt_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `rating` (`rating`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_shipping_rate`
--
ALTER TABLE `mt_shipping_rate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`);

--
-- Indexes for table `mt_size`
--
ALTER TABLE `mt_size`
  ADD PRIMARY KEY (`size_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `size_name` (`size_name`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_sms_broadcast`
--
ALTER TABLE `mt_sms_broadcast`
  ADD PRIMARY KEY (`broadcast_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `send_to` (`send_to`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_sms_broadcast_details`
--
ALTER TABLE `mt_sms_broadcast_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `broadcast_id` (`broadcast_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `status` (`status`),
  ADD KEY `gateway` (`gateway`);

--
-- Indexes for table `mt_sms_package`
--
ALTER TABLE `mt_sms_package`
  ADD PRIMARY KEY (`sms_package_id`);

--
-- Indexes for table `mt_sms_package_trans`
--
ALTER TABLE `mt_sms_package_trans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_sms_trans_logs`
--
ALTER TABLE `mt_sms_trans_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_stripe_logger`
--
ALTER TABLE `mt_stripe_logger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_stripe_logs`
--
ALTER TABLE `mt_stripe_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_subcategory`
--
ALTER TABLE `mt_subcategory`
  ADD PRIMARY KEY (`subcat_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `subcategory_name` (`subcategory_name`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_subcategory_item`
--
ALTER TABLE `mt_subcategory_item`
  ADD PRIMARY KEY (`sub_item_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `sub_item_name` (`sub_item_name`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_voucher`
--
ALTER TABLE `mt_voucher`
  ADD PRIMARY KEY (`voucher_id`),
  ADD KEY `voucher_name` (`voucher_name`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `voucher_type` (`voucher_type`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `mt_voucher_list`
--
ALTER TABLE `mt_voucher_list`
  ADD PRIMARY KEY (`voucher_id`),
  ADD KEY `voucher_code` (`voucher_code`),
  ADD KEY `status` (`status`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `date_used` (`date_used`);

--
-- Indexes for table `mt_voucher_new`
--
ALTER TABLE `mt_voucher_new`
  ADD PRIMARY KEY (`voucher_id`),
  ADD KEY `voucher_name` (`voucher_name`),
  ADD KEY `status` (`status`),
  ADD KEY `voucher_owner` (`voucher_owner`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `voucher_type` (`voucher_type`);

--
-- Indexes for table `mt_withdrawal`
--
ALTER TABLE `mt_withdrawal`
  ADD PRIMARY KEY (`withdrawal_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `payment_type` (`payment_type`),
  ADD KEY `payment_method` (`payment_method`),
  ADD KEY `status` (`status`),
  ADD KEY `viewed` (`viewed`);

--
-- Indexes for table `mt_zipcode`
--
ALTER TABLE `mt_zipcode`
  ADD PRIMARY KEY (`zipcode_id`),
  ADD KEY `country_code` (`country_code`),
  ADD KEY `area` (`area`),
  ADD KEY `stree_name` (`stree_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mt_address_book`
--
ALTER TABLE `mt_address_book`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_address_book_location`
--
ALTER TABLE `mt_address_book_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_admin_user`
--
ALTER TABLE `mt_admin_user`
  MODIFY `admin_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mt_bank_deposit`
--
ALTER TABLE `mt_bank_deposit`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_barclay_trans`
--
ALTER TABLE `mt_barclay_trans`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_bookingtable`
--
ALTER TABLE `mt_bookingtable`
  MODIFY `booking_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_bookingtable_history`
--
ALTER TABLE `mt_bookingtable_history`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_category`
--
ALTER TABLE `mt_category`
  MODIFY `cat_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_client`
--
ALTER TABLE `mt_client`
  MODIFY `client_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_client_cc`
--
ALTER TABLE `mt_client_cc`
  MODIFY `cc_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_cooking_ref`
--
ALTER TABLE `mt_cooking_ref`
  MODIFY `cook_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_cuisine`
--
ALTER TABLE `mt_cuisine`
  MODIFY `cuisine_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `mt_custom_page`
--
ALTER TABLE `mt_custom_page`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_dishes`
--
ALTER TABLE `mt_dishes`
  MODIFY `dish_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_email_logs`
--
ALTER TABLE `mt_email_logs`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_epos_client`
--
ALTER TABLE `mt_epos_client`
  MODIFY `client_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_epos_order`
--
ALTER TABLE `mt_epos_order`
  MODIFY `order_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13708;

--
-- AUTO_INCREMENT for table `mt_epos_order_details`
--
ALTER TABLE `mt_epos_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30997;

--
-- AUTO_INCREMENT for table `mt_favorites`
--
ALTER TABLE `mt_favorites`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_fax_broadcast`
--
ALTER TABLE `mt_fax_broadcast`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_fax_package`
--
ALTER TABLE `mt_fax_package`
  MODIFY `fax_package_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_fax_package_trans`
--
ALTER TABLE `mt_fax_package_trans`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_ingredients`
--
ALTER TABLE `mt_ingredients`
  MODIFY `ingredients_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_invoice`
--
ALTER TABLE `mt_invoice`
  MODIFY `invoice_number` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_invoice_history`
--
ALTER TABLE `mt_invoice_history`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_item`
--
ALTER TABLE `mt_item`
  MODIFY `item_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mt_languages`
--
ALTER TABLE `mt_languages`
  MODIFY `lang_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_location_area`
--
ALTER TABLE `mt_location_area`
  MODIFY `area_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_location_cities`
--
ALTER TABLE `mt_location_cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_location_countries`
--
ALTER TABLE `mt_location_countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `mt_location_rate`
--
ALTER TABLE `mt_location_rate`
  MODIFY `rate_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_location_states`
--
ALTER TABLE `mt_location_states`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_merchant`
--
ALTER TABLE `mt_merchant`
  MODIFY `merchant_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mt_merchant_cc`
--
ALTER TABLE `mt_merchant_cc`
  MODIFY `mt_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_merchant_user`
--
ALTER TABLE `mt_merchant_user`
  MODIFY `merchant_user_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_minimum_table`
--
ALTER TABLE `mt_minimum_table`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_broadcast`
--
ALTER TABLE `mt_mobile2_broadcast`
  MODIFY `broadcast_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_cart`
--
ALTER TABLE `mt_mobile2_cart`
  MODIFY `cart_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_device_reg`
--
ALTER TABLE `mt_mobile2_device_reg`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_homebanner`
--
ALTER TABLE `mt_mobile2_homebanner`
  MODIFY `banner_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_order_trigger`
--
ALTER TABLE `mt_mobile2_order_trigger`
  MODIFY `trigger_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_pages`
--
ALTER TABLE `mt_mobile2_pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_push_logs`
--
ALTER TABLE `mt_mobile2_push_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_recent_location`
--
ALTER TABLE `mt_mobile2_recent_location`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile2_recent_search`
--
ALTER TABLE `mt_mobile2_recent_search`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_mobile_broadcast`
--
ALTER TABLE `mt_mobile_broadcast`
  MODIFY `broadcast_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_newsletter`
--
ALTER TABLE `mt_newsletter`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_offers`
--
ALTER TABLE `mt_offers`
  MODIFY `offers_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_option`
--
ALTER TABLE `mt_option`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=313;

--
-- AUTO_INCREMENT for table `mt_order`
--
ALTER TABLE `mt_order`
  MODIFY `order_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_order_delivery_address`
--
ALTER TABLE `mt_order_delivery_address`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_order_details`
--
ALTER TABLE `mt_order_details`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_order_history`
--
ALTER TABLE `mt_order_history`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_order_sms`
--
ALTER TABLE `mt_order_sms`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_order_status`
--
ALTER TABLE `mt_order_status`
  MODIFY `stats_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `mt_packages`
--
ALTER TABLE `mt_packages`
  MODIFY `package_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_package_trans`
--
ALTER TABLE `mt_package_trans`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_payment_order`
--
ALTER TABLE `mt_payment_order`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_payment_provider`
--
ALTER TABLE `mt_payment_provider`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_paypal_checkout`
--
ALTER TABLE `mt_paypal_checkout`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_paypal_payment`
--
ALTER TABLE `mt_paypal_payment`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_rating`
--
ALTER TABLE `mt_rating`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_rating_meaning`
--
ALTER TABLE `mt_rating_meaning`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mt_receive_post`
--
ALTER TABLE `mt_receive_post`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_review`
--
ALTER TABLE `mt_review`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_shipping_rate`
--
ALTER TABLE `mt_shipping_rate`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_size`
--
ALTER TABLE `mt_size`
  MODIFY `size_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mt_sms_broadcast`
--
ALTER TABLE `mt_sms_broadcast`
  MODIFY `broadcast_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_sms_broadcast_details`
--
ALTER TABLE `mt_sms_broadcast_details`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_sms_package`
--
ALTER TABLE `mt_sms_package`
  MODIFY `sms_package_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_sms_package_trans`
--
ALTER TABLE `mt_sms_package_trans`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_sms_trans_logs`
--
ALTER TABLE `mt_sms_trans_logs`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_stripe_logger`
--
ALTER TABLE `mt_stripe_logger`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_stripe_logs`
--
ALTER TABLE `mt_stripe_logs`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_subcategory`
--
ALTER TABLE `mt_subcategory`
  MODIFY `subcat_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_subcategory_item`
--
ALTER TABLE `mt_subcategory_item`
  MODIFY `sub_item_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_voucher`
--
ALTER TABLE `mt_voucher`
  MODIFY `voucher_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_voucher_list`
--
ALTER TABLE `mt_voucher_list`
  MODIFY `voucher_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_voucher_new`
--
ALTER TABLE `mt_voucher_new`
  MODIFY `voucher_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_withdrawal`
--
ALTER TABLE `mt_withdrawal`
  MODIFY `withdrawal_id` int(14) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mt_zipcode`
--
ALTER TABLE `mt_zipcode`
  MODIFY `zipcode_id` int(14) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
