
INSERT INTO `roles` (`role_id`, `name`, `created_at`) VALUES
 (NULL, 'customer', current_timestamp()),
(NULL, 'employee', current_timestamp());

INSERT INTO `games` (`product_id`, `name`, `description`, `price`, `created_at`) VALUES
(NULL, 'God of War ', 'good', 1500, CURRENT_TIMESTAMP()),
(NULL, 'Genshin Impact ', 'verygood', 250, CURRENT_TIMESTAMP()),
(NULL, 'Honkai Star Rail', 'poor', 50, CURRENT_TIMESTAMP()),
(NULL, 'Apex Legends', 'good', 1200, CURRENT_TIMESTAMP());

INSERT INTO `brands` (`name`, `status`, `created_at`)
VALUES
('PC', 1, NOW()),
('XBOX', 1, NOW()),
('PS4', 1, NOW()),

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `status`, `total_amount`, `created_at`) 
VALUES 
  (NULL, 1, NOW(), 'good', 1000, NOW()),
  (NULL, 2, NOW(), 'good', 2000, NOW());

  INSERT INTO `users` (`role_id`, `firstname`, `lastname`, `username`, `email`, `address`, `phone`, `age`, `password`, `status`, `created_at`) VALUES 
 (1,1, 'MIGIES', 'VAIRES', 'VAIRESMIG', 'VIEREES@gmail.com', '23 MASD', '', 41, '4543433', '34', current_timestamp()),


INSERT INTO `shipments` (`shipment_id`, `order_id`, `recipient_name`, `recipient_address`, `delivery_service`, `tracking_number`, `label_url`, `shipment_date`, `estimated_delivery_date`, `tracking_status`, `created_at`)
VALUES
  (1, 1, 'John Doe', '123 Main St', 'Standard Shipping', '123456789', 'http://example.com/label', NOW(), '2023-09-15', 'Shipped', NOW()),
  (2, 2, 'Jane Smith', '456 Elm St', 'Express Shipping', '987654321', 'http://example.com/label2', NOW(), '2023-09-12', 'In Transit', NOW());

INSERT INTO `n_otification` (`notification_id`, `order_id`, `notification_type`, `sent_date`, `created_at`) 
VALUES 
  (NULL, 1, 'message', '2023-09-10', NOW()),
  (NULL, 2, 'email', '2023-09-11', NOW());


INSERT INTO `addresses` (`address`, `created_at`) VALUES
  ('123 Main St', current_timestamp()),
  ('456 Elm St', current_timestamp());

  INSERT INTO `methods` (`method_id`, `method`, `created_at`)
VALUES
(NULL, 'GET', current_timestamp()),
(NULL, 'POST', current_timestamp()),
(NULL, 'PUT', current_timestamp()),
(NULL, 'DELETE', current_timestamp());

INSERT INTO `permissions` (`permissions_id`, `parent`, `resource`, `created_at`, `status`)
VALUES
(NULL, 'users', '/', current_timestamp(), 1),
(NULL, 'users', 'employees', current_timestamp(), 1),
(NULL, 'users', 'customers', current_timestamp(), 1),
(NULL, 'products', '/', current_timestamp(), 1);

INSERT INTO `user_keys` (`key_id`, `user_id`, `key`, `expired`, `created_at`, `status`)
VALUES
(NULL, 1, 'awt_@353!LhJ!2e,+?R%2/3245e23445234', 0, current_timestamp(), 1),


INSERT INTO `key_permissions` (`id`, `key_id`, `permission_id`, `method_id`, `created_at`, `status`)
VALUES
-- user with key 1(user 1) can perform GET requests for everything
(NULL, 1, 1, 1, current_timestamp(), 1),
(NULL, 1, 2, 1, current_timestamp(), 1),
(NULL, 1, 3, 1, current_timestamp(), 1),
(NULL, 1, 4, 1, current_timestamp(), 1),
-- user with key 1(user1) can perform POST, PUT, and DELETE requests for users
(NULL, 1, 1, 2, current_timestamp(), 1),
(NULL, 1, 1, 3, current_timestamp(), 1),
(NULL, 1, 1, 4, current_timestamp(), 1),
-- user with key 1(user 1) can perform POST, PUT and DELETE requests for order
(NULL, 1, 4, 2, current_timestamp(), 1),
(NULL, 1, 4, 3, current_timestamp(), 1),
(NULL, 1, 4, 4, current_timestamp(), 1),
-- users with keys 2 and 3 (the customers) can only perform GET requests for order
(NULL, 2, 4, 1, current_timestamp(), 1),
(NULL, 3, 4, 1, current_timestamp(), 1);