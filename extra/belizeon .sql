


SET foreign_key_checks = 0;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS shipments;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS n_otification;
DROP TABLE IF EXISTS key_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS methods;
DROP TABLE IF EXISTS user_keys;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;


CREATE TABLE `customers` (
  `customer_id` INT PRIMARY KEY AUTO_INCREMENT,
  `firstname` varchar(255),
  `lastname` varchar(255),
  `email` varchar(255),
  `created_at` timestamp
);


CREATE TABLE `users` (
  `user_id` INT PRIMARY KEY AUTO_INCREMENT,
  `role_id` INT,
  `firstname` varchar(255),
  `lastname` varchar(255),
  `username` varchar(255),
  `email` varchar(255),
  `address` INT,
  `phone` varchar(255),
  `age` INT,
  `password` varchar(255),
  `status` INT,
  `created_at` timestamp
);


CREATE TABLE `shipments` (
  `shipments_id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` varchar(255),
  `recipient_name` varchar(255),
  `recipient_address` varchar(255),
  `delivery_services` varchar(255),
  `tracking_number` int,
  `label_url` varchar(255),
  `shipment_date` varchar(255),
  `estimated_deliverydate` varchar(255),
  `tracking_status` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `n_otification` (
  `notification_id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  `notification_type` varchar(255),
  `sent_date` varchar(255),
  `created_at` timestamp
);



CREATE TABLE `orders` (
  `order_id` INT PRIMARY KEY AUTO_INCREMENT,
  `customer_id` INT,
  `order_date` char(255),
  'status' char (255),
  'total_amount' int,
  `created_at` timestamp
);

CREATE TABLE `games` (
  `game_id` INT PRIMARY KEY AUTO_INCREMENT,
  `brand_id` INT,
  `name` varchar(255),
  `description` varchar(255),
  `price` int,
  `status` int

);

CREATE TABLE `brands` (
  `brand_id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `status` int,
  `created_at` timestamp
);


CREATE TABLE `methods` (
  `method_id` INT PRIMARY KEY AUTO_INCREMENT,
  `method` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `permissions` (
  `permission_id` INT PRIMARY KEY AUTO_INCREMENT,
  `parent` varchar(255),
  `resource` varchar(255),
  `created_at` timestamp,
  `status` bool
);


CREATE TABLE `user_keys` (
  `key_id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `key` varchar(255),
  `expired` int,
  `created_at` timestamp,
  `status` bool
);

CREATE TABLE `key_permissions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `key_id` INT,
  `permission_id` INT,
  `method_id` INT,
  `created_at` timestamp,
  `status` bool
);

CREATE TABLE `roles` (
  `role_id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `addresses` (
  `address_id` INT PRIMARY KEY AUTO_INCREMENT,
  `address` varchar(255),
  `created_at` timestamp
);



ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

ALTER TABLE `games` ADD FOREIGN KEY (`brand_id`) REFERENCES `orders` (`orders_id`);

ALTER TABLE `users` ADD FOREIGN KEY (`address`) REFERENCES `addresses` (`address_id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

ALTER TABLE `shipment` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

ALTER TABLE `notification` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

ALTER TABLE `user_keys` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)  ON DELETE CASCADE;

ALTER TABLE `key_permissions` ADD FOREIGN KEY (`key_id`) REFERENCES `user_keys` (`key_id`) ON DELETE CASCADE;

ALTER TABLE `key_permissions` ADD FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

ALTER TABLE `key_permissions` ADD FOREIGN KEY (`method_id`) REFERENCES `methods` (`method_id`);





