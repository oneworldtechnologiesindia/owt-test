CREATE TABLE `purchase_enquiry` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `customer_id` int(11) DEFAULT NULL,
 `product_id` int(11) NOT NULL,
 `enquiry_description` longtext CHARACTER SET utf8,
 `enquiry_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `status` tinyint(1) NOT NULL DEFAULT '1',
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci


CREATE TABLE `product` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `brand_id` int(11) NOT NULL,
 `type_id` int(11) NOT NULL,
 `category_id` int(11) NOT NULL,
 `product_name` varchar(1000) NOT NULL,
 `connections` varchar(500) DEFAULT NULL,
 `execution` varchar(500) DEFAULT NULL,
 `remark` text CHARACTER SET utf8,
 `retail` decimal(15,4) DEFAULT NULL,
 `url` varchar(1000) DEFAULT NULL,
 `created_at` datetime NOT NULL,
 `updated_at` datetime DEFAULT NULL,
 `deleted_at` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4

CREATE TABLE `appointment_dealer` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `customer_id` int(11) DEFAULT NULL,
 `dealer_id` int(11) DEFAULT NULL,
 `brand_id` int(11) DEFAULT NULL,
 `product_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `appo_date` date DEFAULT NULL,
 `appo_time` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `note` longtext CHARACTER SET utf8,
 `status` tinyint(1) NOT NULL DEFAULT '1',
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

/* 09-08-2022 */
ALTER TABLE `users` CHANGE `name` `first_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `users` ADD `last_name` VARCHAR(255) NULL DEFAULT NULL AFTER `first_name`;

ALTER TABLE `users`  ADD `street` VARCHAR(255) NULL DEFAULT NULL  AFTER `vat_number`,  ADD `house_number` VARCHAR(255) NULL DEFAULT NULL  AFTER `street`,  ADD `zipcode` VARCHAR(255) NULL DEFAULT NULL  AFTER `house_number`,  ADD `country` VARCHAR(255) NULL DEFAULT NULL  AFTER `zipcode`,  ADD `city` VARCHAR(255) NULL DEFAULT NULL  AFTER `country`;

ALTER TABLE `users`  ADD `bank_name` VARCHAR(255) NULL DEFAULT NULL  AFTER `city`,  ADD `iban` VARCHAR(255) NULL DEFAULT NULL  AFTER `bank_name`,  ADD `bic` VARCHAR(255) NULL DEFAULT NULL  AFTER `iban`;

ALTER TABLE `users` ADD `company_logo` TEXT NULL DEFAULT NULL AFTER `company_name`;

CREATE TABLE `purchase_enquiry`.`offer_purchase_enquiry` ( 
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`customer_id` INT(11) NOT NULL ,  
`dealer_id` INT(11) NOT NULL ,  
`customer_enquiry_id` INT(11) NOT NULL ,  
`product_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`offer_description` LONGTEXT NULL DEFAULT NULL ,  
`status` TINYINT(1) NOT NULL DEFAULT '1' ,  
`is_new` TINYINT(1) NOT NULL DEFAULT '1' ,  
`created_at` TIMESTAMP NULL DEFAULT NULL ,  
`updated_at` TIMESTAMP NULL DEFAULT NULL , 
`deleted_at` TIMESTAMP NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

/* 13-08-2022 */

CREATE TABLE `purchase_enquiry`.`offer_details` ( 
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `offer_id` INT(11) NOT NULL , 
    `product_id` INT(11) NOT NULL ,
    `offer_amount` DECIMAL(10,4) NULL DEFAULT NULL , 
    `offer_status` INT NOT NULL DEFAULT '1' , 
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

/*30-08-2022*/
CREATE TABLE `product_connections` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `connection_name` varchar(250) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

CREATE TABLE `product_executions` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `execution_name` varchar(250) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

/*01-09-2022*/
ALTER TABLE `purchase_enquiry`
DROP COLUMN `product_id`

ALTER TABLE `purchase_enquiry`
DROP COLUMN `enquiry_type`

CREATE TABLE `purchase_enquiry_products` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `customer_enquiry_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `connection_ids` varchar(250) DEFAULT NULL,
        `execution_ids` varchar(250) DEFAULT NULL,
        `attribute_ids` varchar(250) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

ALTER TABLE `product_attributes` ADD `connection_id` int NULL DEFAULT NULL AFTER `dealer_id`

ALTER TABLE `product_attributes` ADD `execution_id` int NULL DEFAULT NULL AFTER `connection_id`

/*08-11-2022*/

ALTER TABLE `users`  ADD `contract_startdate` DATE NULL DEFAULT NULL AFTER `role_type`,  ADD `contract_enddate` DATE NULL DEFAULT NULL AFTER `contract_startdate`,  ADD `contract_canceldate` DATE NULL DEFAULT NULL  AFTER `contract_enddate`;

/*10-11-2022*/

ALTER TABLE `purchase_enquiry_products`  ADD `qty` int(11) NOT NULL DEFAULT 1 AFTER `product_id`;

ALTER TABLE `users`  ADD `birth_date` DATE NULL DEFAULT NULL AFTER `country`;

/*15-11-2022*/
ALTER TABLE `users`  ADD `salutation` varchar(50) DEFAULT NULL AFTER `last_name`;


/*17-11-2022*/
ALTER TABLE `appointment_dealer` ADD `reschedule_appo_date` DATE NULL DEFAULT NULL AFTER `appo_time`
ALTER TABLE `appointment_dealer` ADD `reschedule_appo_time` TIME NULL DEFAULT NULL AFTER `reschedule_appo_date`

/*22-11-2022*/
ALTER TABLE `dealer_purchase_enquiry` ADD `deleted_at` DATETIME NULL DEFAULT NULL AFTER `for_brand_id`

/*24-11-2022*/
ALTER TABLE `dealer_purchase_enquiry` ADD `for_product_id` VARCHAR(250) AFTER `for_brand_id`

CREATE TABLE `calendar_events` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `title` varchar(250) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `datetime` TIMESTAMP NOT NULL,
        `category` varchar(250) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci


/*26-11-2022*/
CREATE TABLE `orders` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `dealer_id` int(11) NOT NULL,
        `enquiry_id` int(11) NOT NULL,
        `offer_id` int(11) NOT NULL,
        `status` int(11) NOT NULL,
        `amount` FLOAT(20,2) NOT NULL,
        `shipping_company` varchar(250) NULL DEFAULT NULL,
        `tracking_number` varchar(250) NULL DEFAULT NULL,
        `cancel_proof` varchar(250) NULL DEFAULT NULL,
        `invoice_number` varchar(250) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

/*07-12-2022*/
ALTER TABLE `orders` ADD `payment_method` int(11) AFTER `amount`

/*16-12-2022*/
ALTER TABLE `users` ADD `status_level` int(11) DEFAULT '0' AFTER `status`
ALTER TABLE `users` ADD `turnover` FLOAT(20,2) DEFAULT '0' AFTER `password`

/*10-01-2023*/
CREATE TABLE `documents` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(250) NOT NULL,
        `description` text NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci

ALTER TABLE `users` ADD `display_id` varchar(250) NOT NULL UNIQUE AFTER `id`

/*28-01-2023*/
ALTER TABLE `documents` ADD `german_title` VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `description`;
/*28-01-2023*/
ALTER TABLE `documents` ADD `german_description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `german_title`;
/*13-05-2023*/
ALTER TABLE `appointment_dealer` ADD `appo_type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0=Appointment/1=Zoom-Meeting' AFTER `note`;
ALTER TABLE `packages` ADD `is_zoom_meeting` TINYINT(1) NOT NULL DEFAULT '0' AFTER `plan_currency`;
ALTER TABLE `appointment_dealer` ADD `zoom_met_join_url` TEXT NULL DEFAULT NULL AFTER `appo_type`, ADD `zoom_meeting_json` TEXT NULL DEFAULT NULL AFTER `zoom_met_join_url`;