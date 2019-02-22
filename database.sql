SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE `db_surferlist` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE `db_surferlist`;

CREATE TABLE `db_prefix_address` (
  `address_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `company` varchar(32) NOT NULL,
  `company_id` varchar(32) NOT NULL,
  `tax_id` varchar(32) NOT NULL,
  `address_1` varchar(128) NOT NULL,
  `address_2` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_affiliate` (
  `affiliate_id` int(11) UNSIGNED NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `company` varchar(32) NOT NULL,
  `website` varchar(255) NOT NULL,
  `address_1` varchar(128) NOT NULL,
  `address_2` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `code` varchar(64) NOT NULL,
  `commission` decimal(4,2) NOT NULL DEFAULT '0.00',
  `tax` varchar(64) NOT NULL,
  `payment` varchar(6) NOT NULL,
  `cheque` varchar(100) NOT NULL,
  `paypal` varchar(64) NOT NULL,
  `bank_name` varchar(64) NOT NULL,
  `bank_branch_number` varchar(64) NOT NULL,
  `bank_swift_code` varchar(64) NOT NULL,
  `bank_account_name` varchar(64) NOT NULL,
  `bank_account_number` varchar(64) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_affiliate_transaction` (
  `affiliate_transaction_id` int(11) UNSIGNED NOT NULL,
  `affiliate_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_attribute` (
  `attribute_id` int(11) NOT NULL,
  `attribute_group_id` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_attribute_description` (
  `attribute_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_attribute_group` (
  `attribute_group_id` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_attribute_group_description` (
  `attribute_group_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_banner` (
  `banner_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_banner_image` (
  `banner_image_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_banner_image_description` (
  `banner_image_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_category` (
  `category_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `top` tinyint(1) NOT NULL,
  `column` int(3) NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_category_description` (
  `category_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_category_filter` (
  `category_id` int(11) NOT NULL,
  `filter_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_category_path` (
  `category_id` int(11) NOT NULL,
  `path_id` int(11) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_category_to_layout` (
  `category_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_category_to_store` (
  `category_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_country` (
  `country_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `iso_code_2` varchar(2) NOT NULL,
  `iso_code_3` varchar(3) NOT NULL,
  `address_format` text NOT NULL,
  `postcode_required` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_coupon` (
  `coupon_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `code` varchar(10) NOT NULL,
  `type` char(1) NOT NULL,
  `discount` decimal(15,4) NOT NULL,
  `logged` tinyint(1) NOT NULL,
  `shipping` tinyint(1) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `uses_total` int(11) NOT NULL,
  `uses_customer` varchar(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_coupon_category` (
  `coupon_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_coupon_history` (
  `coupon_history_id` int(11) UNSIGNED NOT NULL,
  `coupon_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_coupon_product` (
  `coupon_product_id` int(11) UNSIGNED NOT NULL,
  `coupon_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_currency` (
  `currency_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `code` varchar(3) NOT NULL,
  `symbol_left` varchar(12) NOT NULL,
  `symbol_right` varchar(12) NOT NULL,
  `decimal_place` char(1) NOT NULL,
  `value` float(15,8) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer` (
  `customer_id` int(11) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `cart` text,
  `wishlist` text,
  `newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `address_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `customer_group_id` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `member_enabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_ban_ip` (
  `customer_ban_ip_id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_field` (
  `customer_id` int(11) UNSIGNED NOT NULL,
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `custom_field_value_id` int(11) UNSIGNED NOT NULL,
  `name` int(128) NOT NULL,
  `value` text NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_group` (
  `customer_group_id` int(11) NOT NULL,
  `member_group_default_id` int(11) NOT NULL,
  `approval` int(1) NOT NULL,
  `company_id_display` int(1) NOT NULL,
  `company_id_required` int(1) NOT NULL,
  `tax_id_display` int(1) NOT NULL,
  `tax_id_required` int(1) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_group_description` (
  `customer_group_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '1',
  `name` varchar(32) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_history` (
  `customer_history_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_ip` (
  `customer_ip_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(40) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_login` (
  `customer_login_id` int(11) UNSIGNED NOT NULL,
  `email` varchar(96) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `total` int(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_login_social` (
  `customer_login_social_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `provider` varchar(32) NOT NULL DEFAULT '',
  `token` varchar(255) NOT NULL DEFAULT '',
  `token_expires` datetime NOT NULL,
  `login_count` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_member_account` (
  `member_account_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `member_account_name` varchar(255) NOT NULL,
  `member_account_description` text NOT NULL,
  `member_account_image` varchar(255) NOT NULL,
  `member_account_banner` varchar(255) NOT NULL,
  `member_group_id` int(11) NOT NULL DEFAULT '1',
  `member_city` varchar(255) NOT NULL,
  `member_zone_id` int(11) NOT NULL,
  `member_country_id` int(11) NOT NULL,
  `member_custom_field_01` varchar(255) NOT NULL,
  `member_custom_field_02` varchar(255) NOT NULL,
  `member_custom_field_03` varchar(255) NOT NULL,
  `member_custom_field_04` varchar(255) NOT NULL,
  `member_custom_field_05` varchar(255) NOT NULL,
  `member_custom_field_06` varchar(255) NOT NULL,
  `member_tag` text NOT NULL,
  `member_directory_images` varchar(255) NOT NULL,
  `member_directory_downloads` varchar(255) NOT NULL,
  `member_paypal_account` varchar(96) NOT NULL,
  `member_max_products` int(11) NOT NULL,
  `member_commission_rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `viewed` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_member_group` (
  `member_group_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `member_group_name` varchar(255) NOT NULL,
  `member_group_description` text NOT NULL,
  `member_group_image` varchar(255) NOT NULL,
  `url_alias_enabled` int(1) NOT NULL DEFAULT '0',
  `sort_enabled` int(1) NOT NULL DEFAULT '0',
  `auto_renew_enabled` int(1) NOT NULL DEFAULT '0',
  `banner_enabled` int(1) NOT NULL DEFAULT '0',
  `inventory_enabled` int(1) NOT NULL DEFAULT '0',
  `option_enabled` int(1) NOT NULL DEFAULT '0',
  `attribute_enabled` int(1) NOT NULL DEFAULT '0',
  `special_enabled` int(1) NOT NULL DEFAULT '0',
  `discount_enabled` int(1) NOT NULL DEFAULT '0',
  `reward_enabled` int(1) NOT NULL DEFAULT '0',
  `design_enabled` int(1) NOT NULL DEFAULT '0',
  `download_enabled` int(1) NOT NULL DEFAULT '0',
  `related_enabled` int(1) NOT NULL DEFAULT '0',
  `import_enabled` int(1) NOT NULL DEFAULT '0',
  `commission_enabled` int(1) NOT NULL DEFAULT '0',
  `tax_enabled` int(1) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_notify` (
  `customer_notify_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `email_contact` tinyint(1) NOT NULL DEFAULT '1',
  `email_post` tinyint(1) NOT NULL DEFAULT '0',
  `email_discuss` tinyint(1) NOT NULL DEFAULT '1',
  `email_review` tinyint(1) NOT NULL DEFAULT '1',
  `email_flag` tinyint(1) NOT NULL DEFAULT '1',
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_online` (
  `ip` varchar(40) NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `url` text NOT NULL,
  `referer` text NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_reward` (
  `customer_reward_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `points` int(8) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_transaction` (
  `customer_transaction_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_customer_verification` (
  `customer_id` int(11) UNSIGNED NOT NULL,
  `verification_code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_custom_field` (
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` text NOT NULL,
  `required` tinyint(1) NOT NULL,
  `location` varchar(32) NOT NULL,
  `position` int(3) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_custom_field_description` (
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_custom_field_to_customer_group` (
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `customer_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_custom_field_value` (
  `custom_field_value_id` int(11) UNSIGNED NOT NULL,
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_custom_field_value_description` (
  `custom_field_value_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) NOT NULL,
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_download` (
  `download_id` int(11) UNSIGNED NOT NULL,
  `filename` varchar(128) NOT NULL,
  `mask` varchar(128) NOT NULL,
  `remaining` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `member_customer_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_download_description` (
  `download_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_extension` (
  `extension_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_filter` (
  `filter_id` int(11) NOT NULL,
  `filter_group_id` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_filter_description` (
  `filter_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `filter_group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_filter_group` (
  `filter_group_id` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_filter_group_description` (
  `filter_group_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_geo_zone` (
  `geo_zone_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_information` (
  `information_id` int(11) NOT NULL,
  `bottom` int(1) NOT NULL DEFAULT '0',
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `viewed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_information_description` (
  `information_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_information_to_layout` (
  `information_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_information_to_store` (
  `information_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_language` (
  `language_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `code` varchar(5) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `image` varchar(64) NOT NULL,
  `directory` varchar(32) NOT NULL,
  `filename` varchar(64) NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_layout` (
  `layout_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_layout_route` (
  `layout_route_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `route` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_length_class` (
  `length_class_id` int(11) NOT NULL,
  `value` decimal(15,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_length_class_description` (
  `length_class_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `unit` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_manufacturer` (
  `manufacturer_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(3) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `viewed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_manufacturer_description` (
  `manufacturer_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_manufacturer_to_category` (
  `manufacturer_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_manufacturer_to_store` (
  `manufacturer_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_option` (
  `option_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_option_description` (
  `option_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_option_value` (
  `option_value_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_option_value_description` (
  `option_value_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order` (
  `order_id` int(11) UNSIGNED NOT NULL,
  `order_no` varchar(26) DEFAULT NULL,
  `invoice_no` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `invoice_prefix` varchar(26) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `store_name` varchar(64) NOT NULL,
  `store_url` varchar(255) NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `customer_group_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL,
  `payment_firstname` varchar(32) NOT NULL,
  `payment_lastname` varchar(32) NOT NULL,
  `payment_company` varchar(32) NOT NULL,
  `payment_company_id` varchar(32) NOT NULL,
  `payment_tax_id` varchar(32) NOT NULL,
  `payment_address_1` varchar(128) NOT NULL,
  `payment_address_2` varchar(128) NOT NULL,
  `payment_city` varchar(128) NOT NULL,
  `payment_postcode` varchar(10) NOT NULL,
  `payment_country` varchar(128) NOT NULL,
  `payment_country_id` int(11) NOT NULL,
  `payment_zone` varchar(128) NOT NULL,
  `payment_zone_id` int(11) NOT NULL,
  `payment_address_format` text NOT NULL,
  `payment_method` varchar(256) NOT NULL,
  `payment_code` varchar(128) NOT NULL,
  `shipping_firstname` varchar(32) NOT NULL,
  `shipping_lastname` varchar(32) NOT NULL,
  `shipping_company` varchar(32) NOT NULL,
  `shipping_address_1` varchar(128) NOT NULL,
  `shipping_address_2` varchar(128) NOT NULL,
  `shipping_city` varchar(128) NOT NULL,
  `shipping_postcode` varchar(10) NOT NULL,
  `shipping_country` varchar(128) NOT NULL,
  `shipping_country_id` int(11) NOT NULL,
  `shipping_zone` varchar(128) NOT NULL,
  `shipping_zone_id` int(11) NOT NULL,
  `shipping_address_format` text NOT NULL,
  `shipping_method` varchar(256) NOT NULL,
  `shipping_code` varchar(128) NOT NULL,
  `comment` text NOT NULL,
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `order_status_id` int(11) NOT NULL DEFAULT '0',
  `affiliate_id` int(11) UNSIGNED NOT NULL,
  `commission` decimal(15,4) NOT NULL,
  `language_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `currency_value` decimal(15,8) NOT NULL DEFAULT '1.00000000',
  `ip` varchar(40) NOT NULL,
  `forwarded_ip` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `accept_language` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_download` (
  `order_download_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `order_product_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `mask` varchar(128) NOT NULL,
  `remaining` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_field` (
  `order_id` int(11) UNSIGNED NOT NULL,
  `custom_field_id` int(11) UNSIGNED NOT NULL,
  `custom_field_value_id` int(11) UNSIGNED NOT NULL,
  `name` int(128) NOT NULL,
  `value` text NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_fraud` (
  `order_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `country_match` varchar(3) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `high_risk_country` varchar(3) NOT NULL,
  `distance` int(11) NOT NULL,
  `ip_region` varchar(255) NOT NULL,
  `ip_city` varchar(255) NOT NULL,
  `ip_latitude` decimal(10,6) NOT NULL,
  `ip_longitude` decimal(10,6) NOT NULL,
  `ip_isp` varchar(255) NOT NULL,
  `ip_org` varchar(255) NOT NULL,
  `ip_asnum` int(11) NOT NULL,
  `ip_user_type` varchar(255) NOT NULL,
  `ip_country_confidence` varchar(3) NOT NULL,
  `ip_region_confidence` varchar(3) NOT NULL,
  `ip_city_confidence` varchar(3) NOT NULL,
  `ip_postal_confidence` varchar(3) NOT NULL,
  `ip_postal_code` varchar(10) NOT NULL,
  `ip_accuracy_radius` int(11) NOT NULL,
  `ip_net_speed_cell` varchar(255) NOT NULL,
  `ip_metro_code` int(3) NOT NULL,
  `ip_area_code` int(3) NOT NULL,
  `ip_time_zone` varchar(255) NOT NULL,
  `ip_region_name` varchar(255) NOT NULL,
  `ip_domain` varchar(255) NOT NULL,
  `ip_country_name` varchar(255) NOT NULL,
  `ip_continent_code` varchar(2) NOT NULL,
  `ip_corporate_proxy` varchar(3) NOT NULL,
  `anonymous_proxy` varchar(3) NOT NULL,
  `proxy_score` int(3) NOT NULL,
  `is_trans_proxy` varchar(3) NOT NULL,
  `free_mail` varchar(3) NOT NULL,
  `carder_email` varchar(3) NOT NULL,
  `high_risk_username` varchar(3) NOT NULL,
  `high_risk_password` varchar(3) NOT NULL,
  `bin_match` varchar(10) NOT NULL,
  `bin_country` varchar(2) NOT NULL,
  `bin_name_match` varchar(3) NOT NULL,
  `bin_name` varchar(255) NOT NULL,
  `bin_phone_match` varchar(3) NOT NULL,
  `bin_phone` varchar(32) NOT NULL,
  `customer_phone_in_billing_location` varchar(8) NOT NULL,
  `ship_forward` varchar(3) NOT NULL,
  `city_postal_match` varchar(3) NOT NULL,
  `ship_city_postal_match` varchar(3) NOT NULL,
  `score` decimal(10,5) NOT NULL,
  `explanation` text NOT NULL,
  `risk_score` decimal(10,5) NOT NULL,
  `queries_remaining` int(11) NOT NULL,
  `maxmind_id` varchar(8) NOT NULL,
  `error` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_history` (
  `order_history_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `order_status_id` int(5) NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `member_customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_option` (
  `order_option_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `order_product_id` int(11) UNSIGNED NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_value_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_product` (
  `order_product_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `model` varchar(64) NOT NULL,
  `quantity` int(4) NOT NULL,
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `commission` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `reward` int(8) NOT NULL,
  `member_customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_status` (
  `order_status_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_total` (
  `order_total_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_order_voucher` (
  `order_voucher_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `voucher_id` int(11) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `from_name` varchar(64) NOT NULL,
  `from_email` varchar(96) NOT NULL,
  `to_name` varchar(64) NOT NULL,
  `to_email` varchar(96) NOT NULL,
  `voucher_theme_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `amount` decimal(15,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `model` varchar(128) NOT NULL,
  `size` varchar(128) DEFAULT NULL,
  `sku` varchar(64) NOT NULL,
  `upc` varchar(64) NOT NULL,
  `ean` varchar(64) NOT NULL,
  `jan` varchar(64) NOT NULL,
  `isbn` varchar(64) NOT NULL,
  `mpn` varchar(64) NOT NULL,
  `location` varchar(128) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `stock_status_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `shipping` tinyint(1) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `points` int(8) NOT NULL DEFAULT '0',
  `tax_class_id` int(11) NOT NULL,
  `date_available` datetime NOT NULL,
  `date_expiration` datetime NOT NULL,
  `year` year(4) NOT NULL,
  `weight` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `weight_class_id` int(11) NOT NULL DEFAULT '0',
  `length` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `width` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `height` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `length_class_id` int(11) NOT NULL DEFAULT '0',
  `subtract` tinyint(1) NOT NULL DEFAULT '1',
  `minimum` int(11) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viewed` int(11) NOT NULL DEFAULT '0',
  `member_customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_approved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_attribute` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_description` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) NOT NULL,
  `tag` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_discount` (
  `product_discount_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_filter` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `filter_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_image` (
  `product_image_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_member` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `member_account_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_option` (
  `product_option_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_value` text NOT NULL,
  `required` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_option_value` (
  `product_option_value_id` int(11) UNSIGNED NOT NULL,
  `product_option_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_value_id` int(11) NOT NULL,
  `quantity` int(3) NOT NULL,
  `subtract` tinyint(1) NOT NULL,
  `price` decimal(15,4) NOT NULL,
  `price_prefix` varchar(1) NOT NULL,
  `points` int(8) NOT NULL,
  `points_prefix` varchar(1) NOT NULL,
  `weight` decimal(15,8) NOT NULL,
  `weight_prefix` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_related` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `related_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_retired` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `model` varchar(64) NOT NULL,
  `size` varchar(64) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `upc` varchar(64) NOT NULL,
  `ean` varchar(64) NOT NULL,
  `jan` varchar(64) NOT NULL,
  `isbn` varchar(64) NOT NULL,
  `mpn` varchar(64) NOT NULL,
  `location` varchar(128) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `stock_status_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `shipping` tinyint(1) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `points` int(8) NOT NULL DEFAULT '0',
  `tax_class_id` int(11) NOT NULL,
  `date_available` datetime NOT NULL,
  `date_expiration` datetime NOT NULL,
  `year` year(4) NOT NULL,
  `weight` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `weight_class_id` int(11) NOT NULL DEFAULT '0',
  `length` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `width` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `height` decimal(15,8) NOT NULL DEFAULT '0.00000000',
  `length_class_id` int(11) NOT NULL DEFAULT '0',
  `subtract` tinyint(1) NOT NULL DEFAULT '1',
  `minimum` int(11) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `viewed` int(11) NOT NULL DEFAULT '0',
  `member_customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `member_approved` tinyint(1) NOT NULL DEFAULT '0',
  `date_retired` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_reward` (
  `product_reward_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `customer_group_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `points` int(8) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_shipping` (
  `product_shipping_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `geo_zone_id` int(11) NOT NULL,
  `first` decimal(15,4) NOT NULL,
  `additional` decimal(15,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_special` (
  `product_special_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_to_category` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_to_download` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `download_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_to_layout` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_product_to_store` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_question` (
  `question_id` int(11) UNSIGNED NOT NULL,
  `member_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `product_id` int(11) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_question_retired` (
  `question_id` int(11) UNSIGNED NOT NULL,
  `member_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `product_id` int(11) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `date_retired` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_return` (
  `return_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `product` varchar(255) NOT NULL,
  `model` varchar(64) NOT NULL,
  `quantity` int(4) NOT NULL,
  `opened` tinyint(1) NOT NULL,
  `return_reason_id` int(11) NOT NULL,
  `return_action_id` int(11) NOT NULL,
  `return_status_id` int(11) NOT NULL,
  `comment` text,
  `date_ordered` date NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_return_action` (
  `return_action_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_return_history` (
  `return_history_id` int(11) UNSIGNED NOT NULL,
  `return_id` int(11) UNSIGNED NOT NULL,
  `return_status_id` int(11) NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_return_reason` (
  `return_reason_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_return_status` (
  `return_status_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_review` (
  `review_id` int(11) UNSIGNED NOT NULL,
  `member_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `order_product_id` int(11) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `rating` int(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_security_ban_list` (
  `ban_list_id` int(11) UNSIGNED NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_security_lockout` (
  `lockout_id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL DEFAULT '',
  `start_time` datetime NOT NULL,
  `expire_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_security_log` (
  `log_id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `host` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `referrer` varchar(500) NOT NULL DEFAULT '',
  `url` varchar(500) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `used_memory` bigint(11) NOT NULL,
  `added` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `changed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_setting` (
  `setting_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `group` varchar(32) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `serialized` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_stock_status` (
  `stock_status_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_store` (
  `store_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ssl` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_tax_class` (
  `tax_class_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_tax_rate` (
  `tax_rate_id` int(11) NOT NULL,
  `geo_zone_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `rate` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `type` char(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_tax_rate_to_customer_group` (
  `tax_rate_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_tax_rule` (
  `tax_rule_id` int(11) NOT NULL,
  `tax_class_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `based` varchar(10) NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_url_alias` (
  `url_alias_id` int(11) UNSIGNED NOT NULL,
  `query` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_user` (
  `user_id` int(11) NOT NULL,
  `user_group_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `code` varchar(40) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_user_group` (
  `user_group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `permission` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_user_login` (
  `user_login_id` int(11) NOT NULL,
  `username` varchar(96) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `total` int(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_voucher` (
  `voucher_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `from_name` varchar(64) NOT NULL,
  `from_email` varchar(96) NOT NULL,
  `to_name` varchar(64) NOT NULL,
  `to_email` varchar(96) NOT NULL,
  `voucher_theme_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_voucher_history` (
  `voucher_history_id` int(11) UNSIGNED NOT NULL,
  `voucher_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_voucher_theme` (
  `voucher_theme_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_voucher_theme_description` (
  `voucher_theme_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_weight_class` (
  `weight_class_id` int(11) NOT NULL,
  `value` decimal(15,8) NOT NULL DEFAULT '0.00000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_weight_class_description` (
  `weight_class_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `unit` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_zone` (
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `code` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `db_prefix_zone_to_geo_zone` (
  `zone_to_geo_zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL DEFAULT '0',
  `geo_zone_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `db_prefix_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `tax_id` (`tax_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `zone_id` (`zone_id`);

ALTER TABLE `db_prefix_affiliate`
  ADD PRIMARY KEY (`affiliate_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `zone_id` (`zone_id`);

ALTER TABLE `db_prefix_affiliate_transaction`
  ADD PRIMARY KEY (`affiliate_transaction_id`),
  ADD KEY `affiliate_id` (`affiliate_id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `db_prefix_attribute`
  ADD PRIMARY KEY (`attribute_id`),
  ADD KEY `attribute_group_id` (`attribute_group_id`);

ALTER TABLE `db_prefix_attribute_description`
  ADD PRIMARY KEY (`attribute_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_attribute_group`
  ADD PRIMARY KEY (`attribute_group_id`);

ALTER TABLE `db_prefix_attribute_group_description`
  ADD PRIMARY KEY (`attribute_group_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_banner`
  ADD PRIMARY KEY (`banner_id`);

ALTER TABLE `db_prefix_banner_image`
  ADD PRIMARY KEY (`banner_image_id`),
  ADD KEY `banner_id` (`banner_id`);

ALTER TABLE `db_prefix_banner_image_description`
  ADD PRIMARY KEY (`banner_image_id`,`language_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `banner_id` (`banner_id`);

ALTER TABLE `db_prefix_category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_id` (`parent_id`);

ALTER TABLE `db_prefix_category_description`
  ADD PRIMARY KEY (`category_id`,`language_id`),
  ADD KEY `name` (`name`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_category_filter`
  ADD PRIMARY KEY (`category_id`,`filter_id`),
  ADD KEY `filter_id` (`filter_id`);

ALTER TABLE `db_prefix_category_path`
  ADD PRIMARY KEY (`category_id`,`path_id`),
  ADD KEY `path_id` (`path_id`);

ALTER TABLE `db_prefix_category_to_layout`
  ADD PRIMARY KEY (`category_id`,`store_id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `layout_id` (`layout_id`);

ALTER TABLE `db_prefix_category_to_store`
  ADD PRIMARY KEY (`category_id`,`store_id`),
  ADD KEY `store_id` (`store_id`);

ALTER TABLE `db_prefix_country`
  ADD PRIMARY KEY (`country_id`);

ALTER TABLE `db_prefix_coupon`
  ADD PRIMARY KEY (`coupon_id`);

ALTER TABLE `db_prefix_coupon_category`
  ADD PRIMARY KEY (`coupon_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `db_prefix_coupon_history`
  ADD PRIMARY KEY (`coupon_history_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_coupon_product`
  ADD PRIMARY KEY (`coupon_product_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `db_prefix_currency`
  ADD PRIMARY KEY (`currency_id`);

ALTER TABLE `db_prefix_customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`) USING BTREE,
  ADD KEY `store_id` (`store_id`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_customer_ban_ip`
  ADD PRIMARY KEY (`customer_ban_ip_id`),
  ADD KEY `ip` (`ip`);

ALTER TABLE `db_prefix_customer_field`
  ADD PRIMARY KEY (`customer_id`,`custom_field_id`,`custom_field_value_id`),
  ADD KEY `custom_field_id` (`custom_field_id`),
  ADD KEY `custom_field_value_id` (`custom_field_value_id`);

ALTER TABLE `db_prefix_customer_group`
  ADD PRIMARY KEY (`customer_group_id`),
  ADD KEY `member_group_default_id` (`member_group_default_id`);

ALTER TABLE `db_prefix_customer_group_description`
  ADD PRIMARY KEY (`customer_group_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_customer_history`
  ADD PRIMARY KEY (`customer_history_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_customer_ip`
  ADD PRIMARY KEY (`customer_ip_id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_customer_login`
  ADD PRIMARY KEY (`customer_login_id`),
  ADD KEY `email` (`email`),
  ADD KEY `ip` (`ip`);

ALTER TABLE `db_prefix_customer_login_social`
  ADD PRIMARY KEY (`customer_login_social_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`customer_id`),
  ADD KEY `provider` (`provider`);

ALTER TABLE `db_prefix_customer_member_account`
  ADD PRIMARY KEY (`member_account_id`),
  ADD KEY `member_zone_id` (`member_zone_id`,`member_country_id`),
  ADD KEY `member_group_id` (`member_group_id`),
  ADD KEY `member_country_id` (`member_country_id`),
  ADD KEY `customer_id` (`customer_id`) USING BTREE;

ALTER TABLE `db_prefix_customer_member_group`
  ADD PRIMARY KEY (`member_group_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_customer_notify`
  ADD PRIMARY KEY (`customer_notify_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_customer_online`
  ADD PRIMARY KEY (`ip`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_customer_reward`
  ADD PRIMARY KEY (`customer_reward_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `db_prefix_customer_transaction`
  ADD PRIMARY KEY (`customer_transaction_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `db_prefix_customer_verification`
  ADD UNIQUE KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_custom_field`
  ADD PRIMARY KEY (`custom_field_id`);

ALTER TABLE `db_prefix_custom_field_description`
  ADD PRIMARY KEY (`custom_field_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_custom_field_to_customer_group`
  ADD PRIMARY KEY (`custom_field_id`,`customer_group_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_custom_field_value`
  ADD PRIMARY KEY (`custom_field_value_id`),
  ADD KEY `custom_field_id` (`custom_field_id`);

ALTER TABLE `db_prefix_custom_field_value_description`
  ADD PRIMARY KEY (`custom_field_value_id`,`language_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `custom_field_id` (`custom_field_id`);

ALTER TABLE `db_prefix_download`
  ADD PRIMARY KEY (`download_id`),
  ADD KEY `member_customer_id` (`member_customer_id`);

ALTER TABLE `db_prefix_download_description`
  ADD PRIMARY KEY (`download_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_extension`
  ADD PRIMARY KEY (`extension_id`);

ALTER TABLE `db_prefix_filter`
  ADD PRIMARY KEY (`filter_id`),
  ADD KEY `filter_group_id` (`filter_group_id`);

ALTER TABLE `db_prefix_filter_description`
  ADD PRIMARY KEY (`filter_id`,`language_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `filter_group_id` (`filter_group_id`);

ALTER TABLE `db_prefix_filter_group`
  ADD PRIMARY KEY (`filter_group_id`);

ALTER TABLE `db_prefix_filter_group_description`
  ADD PRIMARY KEY (`filter_group_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_geo_zone`
  ADD PRIMARY KEY (`geo_zone_id`);

ALTER TABLE `db_prefix_information`
  ADD PRIMARY KEY (`information_id`);

ALTER TABLE `db_prefix_information_description`
  ADD PRIMARY KEY (`information_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_information_to_layout`
  ADD PRIMARY KEY (`information_id`,`store_id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `layout_id` (`layout_id`);

ALTER TABLE `db_prefix_information_to_store`
  ADD PRIMARY KEY (`information_id`,`store_id`),
  ADD KEY `store_id` (`store_id`);

ALTER TABLE `db_prefix_language`
  ADD PRIMARY KEY (`language_id`),
  ADD KEY `name` (`name`);

ALTER TABLE `db_prefix_layout`
  ADD PRIMARY KEY (`layout_id`);

ALTER TABLE `db_prefix_layout_route`
  ADD PRIMARY KEY (`layout_route_id`),
  ADD KEY `layout_id` (`layout_id`),
  ADD KEY `store_id` (`store_id`);

ALTER TABLE `db_prefix_length_class`
  ADD PRIMARY KEY (`length_class_id`);

ALTER TABLE `db_prefix_length_class_description`
  ADD PRIMARY KEY (`length_class_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_manufacturer`
  ADD PRIMARY KEY (`manufacturer_id`);

ALTER TABLE `db_prefix_manufacturer_description`
  ADD PRIMARY KEY (`manufacturer_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_manufacturer_to_category`
  ADD PRIMARY KEY (`manufacturer_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `db_prefix_manufacturer_to_store`
  ADD PRIMARY KEY (`manufacturer_id`,`store_id`),
  ADD KEY `store_id` (`store_id`);

ALTER TABLE `db_prefix_option`
  ADD PRIMARY KEY (`option_id`);

ALTER TABLE `db_prefix_option_description`
  ADD PRIMARY KEY (`option_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_option_value`
  ADD PRIMARY KEY (`option_value_id`),
  ADD KEY `option_id` (`option_id`);

ALTER TABLE `db_prefix_option_value_description`
  ADD PRIMARY KEY (`option_value_id`,`language_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `option_id` (`option_id`);

ALTER TABLE `db_prefix_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `customer_group_id` (`customer_group_id`),
  ADD KEY `payment_company_id` (`payment_company_id`),
  ADD KEY `payment_tax_id` (`payment_tax_id`),
  ADD KEY `payment_country_id` (`payment_country_id`),
  ADD KEY `payment_zone_id` (`payment_zone_id`),
  ADD KEY `shipping_country_id` (`shipping_country_id`),
  ADD KEY `shipping_zone_id` (`shipping_zone_id`),
  ADD KEY `order_status_id` (`order_status_id`),
  ADD KEY `affiliate_id` (`affiliate_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `order_no` (`order_no`);

ALTER TABLE `db_prefix_order_download`
  ADD PRIMARY KEY (`order_download_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_product_id` (`order_product_id`);

ALTER TABLE `db_prefix_order_field`
  ADD PRIMARY KEY (`order_id`,`custom_field_id`,`custom_field_value_id`),
  ADD KEY `custom_field_id` (`custom_field_id`),
  ADD KEY `custom_field_value_id` (`custom_field_value_id`);

ALTER TABLE `db_prefix_order_fraud`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `maxmind_id` (`maxmind_id`);

ALTER TABLE `db_prefix_order_history`
  ADD PRIMARY KEY (`order_history_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_status_id` (`order_status_id`),
  ADD KEY `member_customer_id` (`member_customer_id`);

ALTER TABLE `db_prefix_order_option`
  ADD PRIMARY KEY (`order_option_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_product_id` (`order_product_id`),
  ADD KEY `product_option_id` (`product_option_id`),
  ADD KEY `product_option_value_id` (`product_option_value_id`);

ALTER TABLE `db_prefix_order_product`
  ADD PRIMARY KEY (`order_product_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `member_customer_id` (`member_customer_id`);

ALTER TABLE `db_prefix_order_status`
  ADD PRIMARY KEY (`order_status_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_order_total`
  ADD PRIMARY KEY (`order_total_id`),
  ADD KEY `idx_orders_total_orders_id` (`order_id`);

ALTER TABLE `db_prefix_order_voucher`
  ADD PRIMARY KEY (`order_voucher_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `voucher_theme_id` (`voucher_theme_id`);

ALTER TABLE `db_prefix_product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `stock_status_id` (`stock_status_id`),
  ADD KEY `manufacturer_id` (`manufacturer_id`),
  ADD KEY `tax_class_id` (`tax_class_id`),
  ADD KEY `weight_class_id` (`weight_class_id`),
  ADD KEY `length_class_id` (`length_class_id`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `member_customer_id` (`member_customer_id`);

ALTER TABLE `db_prefix_product_attribute`
  ADD PRIMARY KEY (`product_id`,`attribute_id`,`language_id`),
  ADD KEY `attribute_id` (`attribute_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_product_description`
  ADD PRIMARY KEY (`product_id`,`language_id`),
  ADD KEY `name` (`name`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_product_discount`
  ADD PRIMARY KEY (`product_discount_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_product_filter`
  ADD PRIMARY KEY (`product_id`,`filter_id`),
  ADD KEY `filter_id` (`filter_id`);

ALTER TABLE `db_prefix_product_image`
  ADD PRIMARY KEY (`product_image_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `db_prefix_product_member`
  ADD PRIMARY KEY (`product_id`,`member_account_id`),
  ADD KEY `member_account_id` (`member_account_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `db_prefix_product_option`
  ADD PRIMARY KEY (`product_option_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `option_id` (`option_id`);

ALTER TABLE `db_prefix_product_option_value`
  ADD PRIMARY KEY (`product_option_value_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_option_id` (`product_option_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `option_value_id` (`option_value_id`);

ALTER TABLE `db_prefix_product_related`
  ADD PRIMARY KEY (`product_id`,`related_id`),
  ADD KEY `related_id` (`related_id`);

ALTER TABLE `db_prefix_product_retired`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `manufacturer_id` (`manufacturer_id`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `member_customer_id` (`member_customer_id`),
  ADD KEY `keyword` (`keyword`);

ALTER TABLE `db_prefix_product_reward`
  ADD PRIMARY KEY (`product_reward_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_product_shipping`
  ADD PRIMARY KEY (`product_shipping_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `geo_zone_id` (`geo_zone_id`);

ALTER TABLE `db_prefix_product_special`
  ADD PRIMARY KEY (`product_special_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_product_to_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `db_prefix_product_to_download`
  ADD PRIMARY KEY (`product_id`,`download_id`),
  ADD KEY `download_id` (`download_id`);

ALTER TABLE `db_prefix_product_to_layout`
  ADD PRIMARY KEY (`product_id`,`store_id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `layout_id` (`layout_id`);

ALTER TABLE `db_prefix_product_to_store`
  ADD PRIMARY KEY (`product_id`,`store_id`),
  ADD KEY `store_id` (`store_id`);

ALTER TABLE `db_prefix_question`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `parent_id` (`parent_id`);

ALTER TABLE `db_prefix_question_retired`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `parent_id` (`parent_id`);

ALTER TABLE `db_prefix_return`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `return_reason_id` (`return_reason_id`),
  ADD KEY `return_action_id` (`return_action_id`),
  ADD KEY `return_status_id` (`return_status_id`);

ALTER TABLE `db_prefix_return_action`
  ADD PRIMARY KEY (`return_action_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_return_history`
  ADD PRIMARY KEY (`return_history_id`),
  ADD KEY `return_id` (`return_id`),
  ADD KEY `return_status_id` (`return_status_id`);

ALTER TABLE `db_prefix_return_reason`
  ADD PRIMARY KEY (`return_reason_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_return_status`
  ADD PRIMARY KEY (`return_status_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_review`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_index` (`member_id`,`customer_id`,`order_product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `order_product_id` (`order_product_id`);

ALTER TABLE `db_prefix_security_ban_list`
  ADD PRIMARY KEY (`ban_list_id`);

ALTER TABLE `db_prefix_security_lockout`
  ADD PRIMARY KEY (`lockout_id`);

ALTER TABLE `db_prefix_security_log`
  ADD PRIMARY KEY (`log_id`);

ALTER TABLE `db_prefix_setting`
  ADD PRIMARY KEY (`setting_id`),
  ADD KEY `store_id` (`store_id`);

ALTER TABLE `db_prefix_stock_status`
  ADD PRIMARY KEY (`stock_status_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_store`
  ADD PRIMARY KEY (`store_id`);

ALTER TABLE `db_prefix_tax_class`
  ADD PRIMARY KEY (`tax_class_id`);

ALTER TABLE `db_prefix_tax_rate`
  ADD PRIMARY KEY (`tax_rate_id`),
  ADD KEY `geo_zone_id` (`geo_zone_id`);

ALTER TABLE `db_prefix_tax_rate_to_customer_group`
  ADD PRIMARY KEY (`tax_rate_id`,`customer_group_id`),
  ADD KEY `customer_group_id` (`customer_group_id`);

ALTER TABLE `db_prefix_tax_rule`
  ADD PRIMARY KEY (`tax_rule_id`),
  ADD KEY `tax_class_id` (`tax_class_id`),
  ADD KEY `tax_rate_id` (`tax_rate_id`);

ALTER TABLE `db_prefix_url_alias`
  ADD PRIMARY KEY (`url_alias_id`),
  ADD KEY `query` (`query`),
  ADD KEY `keyword` (`keyword`);

ALTER TABLE `db_prefix_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE,
  ADD KEY `user_group_id` (`user_group_id`);

ALTER TABLE `db_prefix_user_group`
  ADD PRIMARY KEY (`user_group_id`);

ALTER TABLE `db_prefix_user_login`
  ADD PRIMARY KEY (`user_login_id`),
  ADD KEY `email` (`username`),
  ADD KEY `ip` (`ip`);

ALTER TABLE `db_prefix_voucher`
  ADD PRIMARY KEY (`voucher_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `voucher_theme_id` (`voucher_theme_id`);

ALTER TABLE `db_prefix_voucher_history`
  ADD PRIMARY KEY (`voucher_history_id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `order_id` (`order_id`);

ALTER TABLE `db_prefix_voucher_theme`
  ADD PRIMARY KEY (`voucher_theme_id`);

ALTER TABLE `db_prefix_voucher_theme_description`
  ADD PRIMARY KEY (`voucher_theme_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_weight_class`
  ADD PRIMARY KEY (`weight_class_id`);

ALTER TABLE `db_prefix_weight_class_description`
  ADD PRIMARY KEY (`weight_class_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

ALTER TABLE `db_prefix_zone`
  ADD PRIMARY KEY (`zone_id`),
  ADD KEY `country_id` (`country_id`);

ALTER TABLE `db_prefix_zone_to_geo_zone`
  ADD PRIMARY KEY (`zone_to_geo_zone_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `geo_zone_id` (`geo_zone_id`);


ALTER TABLE `db_prefix_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_affiliate`
  MODIFY `affiliate_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_affiliate_transaction`
  MODIFY `affiliate_transaction_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_attribute`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_attribute_group`
  MODIFY `attribute_group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_banner`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_banner_image`
  MODIFY `banner_image_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_coupon`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_coupon_history`
  MODIFY `coupon_history_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_coupon_product`
  MODIFY `coupon_product_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_currency`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_ban_ip`
  MODIFY `customer_ban_ip_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_group`
  MODIFY `customer_group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_history`
  MODIFY `customer_history_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_ip`
  MODIFY `customer_ip_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_login`
  MODIFY `customer_login_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_login_social`
  MODIFY `customer_login_social_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_member_account`
  MODIFY `member_account_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_member_group`
  MODIFY `member_group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_notify`
  MODIFY `customer_notify_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_reward`
  MODIFY `customer_reward_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_customer_transaction`
  MODIFY `customer_transaction_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_custom_field`
  MODIFY `custom_field_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_custom_field_value`
  MODIFY `custom_field_value_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_download`
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_extension`
  MODIFY `extension_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_filter`
  MODIFY `filter_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_filter_group`
  MODIFY `filter_group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_geo_zone`
  MODIFY `geo_zone_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_information`
  MODIFY `information_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_language`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_layout`
  MODIFY `layout_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_layout_route`
  MODIFY `layout_route_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_length_class`
  MODIFY `length_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_length_class_description`
  MODIFY `length_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_manufacturer`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_manufacturer_description`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_option`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_option_value`
  MODIFY `option_value_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_download`
  MODIFY `order_download_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_history`
  MODIFY `order_history_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_option`
  MODIFY `order_option_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_product`
  MODIFY `order_product_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_status`
  MODIFY `order_status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_total`
  MODIFY `order_total_id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_order_voucher`
  MODIFY `order_voucher_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_discount`
  MODIFY `product_discount_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_image`
  MODIFY `product_image_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_option`
  MODIFY `product_option_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_option_value`
  MODIFY `product_option_value_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_reward`
  MODIFY `product_reward_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_shipping`
  MODIFY `product_shipping_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_product_special`
  MODIFY `product_special_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_return`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_return_action`
  MODIFY `return_action_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_return_history`
  MODIFY `return_history_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_return_reason`
  MODIFY `return_reason_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_return_status`
  MODIFY `return_status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_security_ban_list`
  MODIFY `ban_list_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_security_lockout`
  MODIFY `lockout_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_security_log`
  MODIFY `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_setting`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_stock_status`
  MODIFY `stock_status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_store`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_tax_class`
  MODIFY `tax_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_tax_rate`
  MODIFY `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_tax_rule`
  MODIFY `tax_rule_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_url_alias`
  MODIFY `url_alias_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_user_group`
  MODIFY `user_group_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_user_login`
  MODIFY `user_login_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_voucher`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_voucher_history`
  MODIFY `voucher_history_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_voucher_theme`
  MODIFY `voucher_theme_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_weight_class`
  MODIFY `weight_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_weight_class_description`
  MODIFY `weight_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_zone`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_prefix_zone_to_geo_zone`
  MODIFY `zone_to_geo_zone_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
