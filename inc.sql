TRUNCATE TABLE `qoutes`;

TRUNCATE TABLE `qoute_details`;

ALTER TABLE `qoutes` CHANGE `net_price` `net_price` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `markup_amount` `markup_amount` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `selling` `selling` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `show_convert_currency` `show_convert_currency` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `per_person` `per_person` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `port_tax` `port_tax` FLOAT(16,2) NULL DEFAULT NULL, CHANGE `total_per_person` `total_per_person` FLOAT(16,2) NULL DEFAULT NULL;

ALTER TABLE `qoute_details` CHANGE `cost` `cost` FLOAT(16,2) NULL DEFAULT NULL;

ALTER TABLE `currency_conversion` CHANGE `value` `value` FLOAT(16,2) NULL DEFAULT NULL;

ALTER TABLE `qoute_details` ADD `supervisor_id` INT(10) NULL AFTER `cost`;
