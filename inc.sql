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

-- Add lead passenger name feild in bookings
ALTER TABLE `bookings` ADD `lead_passenger_name` VARCHAR(255) NOT NULL AFTER `quotation_no`;

-- add default curreny in supplier
ALTER TABLE `suppliers` ADD `currency_id` INT(11) NOT NULL AFTER `phone`;

-- add gross_profit feild in qoutes,qoute_logs, booking
ALTER TABLE `qoutes` ADD `gross_profit` FLOAT(16,2) NULL AFTER `selling`;
ALTER TABLE `qoute_logs` ADD `gross_profit` FLOAT(16,2) NULL AFTER `selling`;
ALTER TABLE `bookings` ADD `gross_profit` FLOAT(16,2) NULL AFTER `selling`;

-- add dinning_preferences feild in qoutes,qoute_logs, booking
ALTER TABLE `qoutes` ADD `dinning_preferences` VARCHAR(255) NOT NULL AFTER `quotation_no`;
ALTER TABLE `qoute_logs` ADD `dinning_preferences` VARCHAR(255) NOT NULL AFTER `quotation_no`;
ALTER TABLE `bookings` ADD `dinning_preferences` VARCHAR(255) NOT NULL AFTER `quotation_no`;

ALTER TABLE `qoute_details` ADD `booking_type` varchar(255) NOT NULL AFTER `booking_refrence`;
ALTER TABLE `qoute_detail_logs` ADD `booking_type` varchar(255) NOT NULL AFTER `booking_refrence`;
ALTER TABLE `booking_details` ADD `booking_type` varchar(255) NOT NULL AFTER `booking_refrence`;

INSERT INTO `booking_methods` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Supplier Own', '2021-05-31 07:26:02', '2021-05-31 07:26:02'),
(2, 'Stuba', '2021-05-31 07:26:11', '2021-05-31 07:26:11'),
(3, 'Webhotelier', '2021-05-31 07:26:18', '2021-05-31 07:26:18');

ALTER TABLE `booking_details` CHANGE `booking_type` `booking_type` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `finance_booking_details` CHANGE `booking_method` `payment_method` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `finance_booking_details` ADD `upload_to_calender` VARCHAR(255) NULL AFTER `payment_method`;

-- env google calendar id
GOOGLE_CALENDAR_ID=bcttfdmuevbf5aod6i8jhhrvm4@group.calendar.google.com

ALTER TABLE `qoute_details` CHANGE `booking_type` `booking_type` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `qoute_detail_logs` CHANGE `booking_type` `booking_type` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;

ALTER TABLE `qoutes` ADD `reference_name` VARCHAR(255) NULL AFTER `id`;
ALTER TABLE `qoute_logs` ADD `reference_name` VARCHAR(255) NULL AFTER `id`;
ALTER TABLE `bookings` ADD `reference_name` VARCHAR(255) NULL AFTER `id`;

-- // table TRUNCATE
TRUNCATE TABLE `qoutes` ;
TRUNCATE TABLE `qoute_details` ;
TRUNCATE TABLE `qoute_logs` ;
TRUNCATE TABLE `qoute_detail_logs` ;
TRUNCATE TABLE `bookings` ;
TRUNCATE TABLE `booking_details` ;
TRUNCATE TABLE `finance_booking_details` ;



-- Add columns on users brand name and currency
ALTER TABLE `users` ADD `currency_id` INT(10) NULL AFTER `is_login`, ADD `brand_name` VARCHAR(255) NULL AFTER `currency_id`;
-- ALTER TABLE `users` ADD `currency` INT NULL AFTER `password`, ADD `brand_name` VARCHAR(255) NULL AFTER `currency`, ADD PRIMARY KEY (`currency`);
-- ALTER TABLE `users` ADD CONSTRAINT `currency_id` FOREIGN KEY (`currency`) REFERENCES `currencies`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- // Add Flight ON Category
INSERT INTO `categories` (`name`, `updated_at`, `created_at`) VALUES ('Flights', '2021-06-04', '2021-06-04')

-- Add suppliers for flights Easyjet, Jet2 , British Airways
INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `currency_id`, `updated_at`, `created_at`) VALUES (NULL, ' Easyjet', '', '', '', '2021-06-04', '2021-06-04'), (NULL, 'Jet2', '', '', '', '2021-06-04', '2021-06-04'), (NULL, 'British Airways', '', '', '', '2021-06-04', '2021-06-04')

-- //change Supplier Column
ALTER TABLE `suppliers` CHANGE `email` `email` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `suppliers` CHANGE `phone` `phone` INT(20) NULL;
ALTER TABLE `suppliers` CHANGE `currency_id` `currency_id` INT(11) NULL;
ALTER TABLE `suppliers` ADD FOREIGN KEY (`currency_id`) REFERENCES `currencies`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- INSERT INTO `supplier_categories` (`id`, `supplier_id`, `category_id`, `updated_at`, `created_at`) VALUES (NULL, '7', '8', '2021-06-04', '2021-06-04'), (NULL, '8', '8', '2021-06-04', '2021-06-04'), (NULL, '9', '8', '2021-06-04', '2021-06-04')
CREATE TABLE `lara_unforge`.`zoho_credentials` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `code` VARCHAR(255) NULL , `access_token` VARCHAR(255) NULL , `refresh_token` VARCHAR(255) NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

-- //relation suppliers
ALTER TABLE `supplier_categories` ADD FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `supplier_categories` DROP FOREIGN KEY `supplier_categories_ibfk_2`; ALTER TABLE `supplier_categories` ADD FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `supplier_products` ADD FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `supplier_products` ADD FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- //users relation
ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `users` ADD FOREIGN KEY (`currency_id`) REFERENCES `currencies`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- //quote relation

ALTER TABLE `qoute_details` ADD FOREIGN KEY (`qoute_id`) REFERENCES `qoutes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `qoute_details` ADD FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- //log_no
ALTER TABLE `qoute_detail_logs` ADD FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- quote log_no

ALTER TABLE `qoute_logs` ADD FOREIGN KEY (`qoute_id`) REFERENCES `qoutes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- booking
ALTER TABLE `booking_details` ADD FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- //user supervisor_id relation
ALTER TABLE `users` ADD FOREIGN KEY (`supervisor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

TRUNCATE TABLE `bookings`;
TRUNCATE TABLE `booking_details`;
TRUNCATE TABLE `finance_booking_details`

ALTER TABLE `qoutes` ADD `qoute_to_booking_status` INT(10) NOT NULL DEFAULT '0' AFTER `is_email_send`;
ALTER TABLE `qoutes` ADD `qoute_to_booking_date` DATE NULL AFTER `is_email_send`;
ALTER TABLE `bookings` ADD `qoute_to_booking_date` DATE NULL AFTER `is_email_send`;

CREATE TABLE `booking_logs` (
  `id` int(11) NOT NULL,
  `booking_id` int(10) DEFAULT NULL,
  `reference_name` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `dinning_preferences` varchar(255) NOT NULL,
  `lead_passenger_name` varchar(255) NOT NULL,
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
  `gross_profit` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `created_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `booking_logs` ADD PRIMARY KEY( `id`);
ALTER TABLE `booking_logs` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `booking_logs` CHANGE `qoute_to_booking_date` `created_date` DATE NULL DEFAULT NULL;
ALTER TABLE `booking_logs` ADD `log_no` INT NULL DEFAULT '0' AFTER `booking_id`;

CREATE TABLE `booking_detail_logs` (
  `id` int(10) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `booking_id` int(10) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `row` int(10) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `actual_cost` float(16,2) DEFAULT NULL,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `qoute_invoice` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `booking_detail_logs` ADD `log_no` INT(10) NOT NULL DEFAULT '0' AFTER `booking_id`;
ALTER TABLE `booking_detail_logs` ADD PRIMARY KEY( `id`);
ALTER TABLE `booking_detail_logs` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `booking_logs` ADD `user_id` INT NULL AFTER `created_date`;

CREATE TABLE `lara_unforge`.`finance_booking_detail_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `booking_detail_id` INT(10) NULL , `row` INT(10) NULL , `deposit_amount` FLOAT(16,2) NULL , `paid_date` DATE NULL , `payment_method` VARCHAR(255) NULL , `upload_to_calender` VARCHAR(255) NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP NULL , `deposit_due_date` DATE NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `finance_booking_detail_logs` ADD `log_no` INT(10) NOT NULL DEFAULT '0' AFTER `booking_detail_id`;

-- //templates
CREATE TABLE `lara_unforge`.`templates` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `title` VARCHAR(255) NOT NULL , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `templates` CHANGE `user_id` `user_id` INT(11) UNSIGNED NULL;
ALTER TABLE `templates` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE `template_details` (
  `id` int(11) NOT NULL,
  `template_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `booking_method_id` int(10) UNSIGNED DEFAULT NULL,
  `booked_by_id` int(10) UNSIGNED DEFAULT NULL,
  `booking_reference` varchar(255) DEFAULT NULL,
  `booking_type` enum('refundable','nonrefundable') DEFAULT 'refundable',
  `comments` varchar(255) DEFAULT NULL,
  `currency_id` int(10) UNSIGNED DEFAULT NULL,
  `estimated_cost` double DEFAULT NULL,
  `currency_conversion` double DEFAULT NULL,
  `sage` enum('0','1') DEFAULT '0',
  `supervisor_id` int(10) UNSIGNED DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `template_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `template_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `template_details` CHANGE `template_id` `template_id` INT(10) NOT NULL;
ALTER TABLE `template_details` ADD `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `booking_due_date`, ADD `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
ALTER TABLE `templates` ADD `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
ALTER TABLE `templates` ADD `status` ENUM('active','inactive') NULL DEFAULT 'active' AFTER `title`;

ALTER TABLE `templates` ADD `season_id` INT UNSIGNED NOT NULL AFTER `user_id`;
ALTER TABLE `templates` ADD FOREIGN KEY (`season_id`) REFERENCES `seasons`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Flight Booked (booking table)
ALTER TABLE `bookings` ADD `flight_booked` VARCHAR(255) NULL AFTER `updated_at`;
ALTER TABLE `bookings` ADD `fb_person` INT(10) NULL AFTER `flight_booked`, ADD `fb_last_date` DATE NULL AFTER `fb_person`, ADD `fb_airline_name_id` INT(10) NULL AFTER `fb_last_date`, ADD `fb_payment_method_id` INT(10) NULL AFTER `fb_airline_name_id`, ADD `fb_booking_date` DATE NULL AFTER `fb_payment_method_id`, ADD `fb_airline_ref_no` VARCHAR(255) NULL AFTER `fb_booking_date`, ADD `flight_booking_details` TEXT NULL AFTER `fb_airline_ref_no`;

-- Transfer Info (booking table)
ALTER TABLE `bookings` ADD `asked_for_transfer_details` VARCHAR(5) NULL AFTER `flight_booking_details`, ADD `aft_person` INT(10) NULL AFTER `asked_for_transfer_details`, ADD `aft_last_date` DATE NULL AFTER `aft_person`, ADD `transfer_details` TEXT NULL AFTER `aft_last_date`;

-- Transfers Organised (booking table)
ALTER TABLE `bookings` ADD `transfer_organised` VARCHAR(5) NULL AFTER `transfer_details`, ADD `to_person` INT(10) NULL AFTER `transfer_organised`, ADD `to_last_date` DATE NULL AFTER `to_person`, ADD `transfer_organised_details` TEXT NULL AFTER `to_last_date`;

-- Itinerary Finalised (booking table)
ALTER TABLE `bookings` ADD `itinerary_finalised` VARCHAR(5) NULL AFTER `transfer_organised_details`, ADD `itf_person` INT(10) NULL AFTER `itinerary_finalised`, ADD `itf_last_date` DATE NULL AFTER `itf_person`, ADD `itinerary_finalised_details` TEXT NULL AFTER `itf_last_date`, ADD `itf_current_date` DATE NULL AFTER `itinerary_finalised_details`;

-- Travel Document Prepared (booking table)
ALTER TABLE `bookings` ADD `document_prepare` VARCHAR(5) NULL AFTER `itf_current_date`, ADD `dp_person` INT(10) NULL AFTER `document_prepare`, ADD `dp_last_date` DATE NULL AFTER `dp_person`, ADD `tdp_current_date` DATE NULL AFTER `dp_last_date`;

-- Travel Document Sent (booking table)
ALTER TABLE `bookings` ADD `documents_sent` VARCHAR(5) NULL AFTER `tdp_current_date`, ADD `ds_person` INT(10) NULL AFTER `documents_sent`, ADD `ds_last_date` DATE NULL AFTER `ds_person`, ADD `documents_sent_details` TEXT NULL AFTER `ds_last_date`, ADD `tds_current_date` DATE NULL AFTER `documents_sent_details`;

-- App login Sent (booking table)
ALTER TABLE `bookings` ADD `electronic_copy_sent` VARCHAR(5) NULL AFTER `tds_current_date`, ADD `aps_person` INT(10) NULL AFTER `electronic_copy_sent`, ADD `aps_last_date` DATE NULL AFTER `aps_person`, ADD `electronic_copy_details` TEXT NULL AFTER `aps_last_date`;

-- //pax name

ALTER TABLE `qoutes` ADD `pax_name` VARCHAR(300) NULL AFTER `lead_passenger_name`;
ALTER TABLE `qoute_logs` ADD `pax_name` VARCHAR(255) NULL AFTER `reference_name`;
ALTER TABLE `bookings` ADD `pax_name` VARCHAR(255) NULL AFTER `lead_passenger_name`;
ALTER TABLE `booking_logs` ADD `pax_name` VARCHAR(255) NOT NULL AFTER `lead_passenger_name`;
-- add product feild in qoute_details
ALTER TABLE `qoute_details` ADD `product` INT(10) NULL AFTER `supplier`;

-- add product feild in qoute_detail_logs
ALTER TABLE `qoute_detail_logs` ADD `product` INT(10) NULL AFTER `supplier`;

-- add product feild in booking_details
ALTER TABLE `booking_details` ADD `product` INT(10) NULL AFTER `supplier`;

-- add product feild in booking_detail_logs
ALTER TABLE `booking_detail_logs` ADD `product` INT(10) NULL AFTER `supplier`;

TRUNCATE TABLE `booking_logs`;
TRUNCATE TABLE `booking_detail_logs`;
TRUNCATE TABLE `finance_booking_detail_logs`

-- add additional_date in finance_booking_details
ALTER TABLE `finance_booking_details` ADD `additional_date` INT(10) NULL AFTER `upload_to_calender`;

-- add additional_date in finance_booking_detail_logs
ALTER TABLE `finance_booking_detail_logs` ADD `additional_date` INT(10) NULL AFTER `upload_to_calender`;
 

CREATE TABLE `lara_unforge`.`all_currencies` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NULL , `code` VARCHAR(10) NULL , `isObsolete` VARCHAR(10) NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


ALTER TABLE `all_currencies` ADD `flag` VARCHAR(255) NOT NULL AFTER `isObsolete`;

ALTER TABLE `currencies` CHANGE `symbol` `symbol` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;

ALTER TABLE `currencies` ADD `isObsolete` VARCHAR(10) NULL AFTER `code`, ADD `flag` TEXT NULL AFTER `isObsolete`;

ALTER TABLE `currencies` ADD `status` TINYINT NOT NULL DEFAULT '1' AFTER `flag`;

-- truncate all tables qoutes to book
TRUNCATE TABLE `qoutes` ;
TRUNCATE TABLE `qoute_details` ;
TRUNCATE TABLE `qoute_logs` ;
TRUNCATE TABLE `qoute_detail_logs` ;
TRUNCATE TABLE `bookings` ;
TRUNCATE TABLE `booking_details` ;
TRUNCATE TABLE `finance_booking_details` ;
TRUNCATE TABLE `booking_logs` ;
TRUNCATE TABLE `booking_detail_logs` ;
TRUNCATE TABLE `finance_booking_detail_logs` ;