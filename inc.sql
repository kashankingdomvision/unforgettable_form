TRUNCATE TABLE `qoutes`;

TRUNCATE TABLE `qoute_details`;

ALTER TABLE `qoutes` CHANGE `net_price` `net_price` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `markup_amount` `markup_amount` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `selling` `selling` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `show_convert_currency` `show_convert_currency` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `per_person` `per_person` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `port_tax` `port_tax` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `total_per_person` `total_per_person` FLOAT(16,2) NULL DEFAULT NULL;

ALTER TABLE `qoute_details` CHANGE `cost` `cost` FLOAT(16,2) NULL DEFAULT NULL;

ALTER TABLE `currency_conversion` CHANGE `value` `value` FLOAT(16,2) NULL DEFAULT NULL;

ALTER TABLE `qoute_details` ADD `supervisor_id` INT(10) NULL AFTER `cost`;

ALTER TABLE `qoute_details` ADD `added_in_sage` TINYINT NULL DEFAULT '0' AFTER `supervisor_id`;

ALTER TABLE `qoute_details` ADD `qoute_base_currency` FLOAT(16,2) NULL AFTER `added_in_sage`;



CREATE TABLE `qoute_logs` (
  `id` int(11) NOT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `type_of_holidays` varchar(255) DEFAULT NULL,
  `sale_person` varchar(255) DEFAULT NULL,
  `season_id` int(10) DEFAULT NULL,
  `agency_booking` tinyint(1) DEFAULT NULL,
  `agency_name` varchar(255) DEFAULT NULL,
  `agency_contact_no` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `convert_currency` varchar(255) DEFAULT NULL,
  `group_no` int(10) DEFAULT NULL,
  `net_price` float(16,2) DEFAULT NULL,
  `markup_amount` float(16,2) DEFAULT NULL,
  `selling` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `qoute_logs` ADD `log_no` INT(10) NULL DEFAULT '0' AFTER `is_email_send`;

ALTER TABLE `qoute_logs` ADD `qoute_id` INT(10) NULL AFTER `id`;



ALTER TABLE `qoutes` ADD `quotation_no` VARCHAR(255) NULL AFTER `ref_no`;
ALTER TABLE `qoute_logs` ADD `quotation_no` VARCHAR(255) NULL AFTER `ref_no`;




TRUNCATE TABLE `qoutes`;
TRUNCATE TABLE `qoute_details`;


TRUNCATE TABLE `qoute_logs`;
TRUNCATE TABLE `qoute_detail_logs`;


-- ALTER TABLE `qoutes` ADD `created_date` DATE NULL AFTER `is_email_send`;
ALTER TABLE `qoute_logs` ADD `created_date` DATE NULL AFTER `log_no`;

ALTER TABLE `qoute_logs` ADD `user_id` INT(10) NULL AFTER `created_date`;

ALTER TABLE `qoute_details` ADD `qoute_invoice` VARCHAR(255) NULL AFTER `qoute_base_currency`;

ALTER TABLE `qoute_detail_logs` ADD `qoute_invoice` VARCHAR(255) NULL AFTER `log_no`;


CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `type_of_holidays` varchar(255) DEFAULT NULL,
  `sale_person` varchar(255) DEFAULT NULL,
  `season_id` int(10) DEFAULT NULL,
  `agency_booking` tinyint(1) DEFAULT NULL,
  `agency_name` varchar(255) DEFAULT NULL,
  `agency_contact_no` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `convert_currency` varchar(255) DEFAULT NULL,
  `group_no` int(10) DEFAULT NULL,
  `net_price` float(16,2) DEFAULT NULL,
  `markup_amount` float(16,2) DEFAULT NULL,
  `selling` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `booking_details` (
  `id` int(10) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add lead passenger name feild in qoutes,qoute_logs
ALTER TABLE `qoutes` ADD `lead_passenger_name` VARCHAR(255) NOT NULL AFTER `quotation_no`;
ALTER TABLE `qoute_logs` ADD `lead_passenger_name` VARCHAR(255) NOT NULL AFTER `quotation_no`;










