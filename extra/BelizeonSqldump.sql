-- MySQL dump 10.13  Distrib 8.0.34, for Linux (x86_64)
--
-- Host: 192.168.182.121    Database: belizeonDB
-- ------------------------------------------------------
-- Server version	8.0.34

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;







--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `brand_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'PC',1,'2023-10-18 08:49:15'),(2,'XBOX',1,'2023-10-18 08:49:15'),(3,'PS4',1,'2023-10-18 08:49:15');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;




--
-- Table structure for table `addresses`
--


LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (1,'123 Main St','2023-10-18 08:48:25'),(2,'456 Elm St','2023-10-18 08:48:25');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `address_id` int NOT NULL AUTO_INCREMENT,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
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
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipments`
--

LOCK TABLES `shipments` WRITE;
/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
INSERT INTO `shipments` VALUES (1, 1, 'John Doe', '123 Main St', 'Standard Shipping', '123456789', 'http://example.com/label', 'october 18', '2023-09-15', 'Shipped', '2023-10-18 08:48:06')(2, 2, 'Jane Smith', '456 Elm St', 'Express Shipping', '987654321', 'http://example.com/label2', 'october 18', '2023-09-12', 'In Transit','2023-10-18 08:48:06');
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `n_otification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `n_otification` (
  `notification_id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  `notification_type` varchar(255),
  `sent_date` varchar(255),
  `created_at` timestamp
  PRIMARY KEY (`notification_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `notification_iljk_1` FOREIGN KEY (`order_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table ``
--

LOCK TABLES `n_otification` WRITE;
/*!40000 ALTER TABLE `n_otification` DISABLE KEYS */;
INSERT INTO `n_otification` VALUES (2, 'message', '2023-10-18 08:48:06'), (2,'email','2023-10-18 08:48:06');
/*!40000 ALTER TABLE `n_otification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `key_permissions`
--

DROP TABLE IF EXISTS `key_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `key_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key_id` int DEFAULT NULL,
  `permission_id` int DEFAULT NULL,
  `method_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `key_id` (`key_id`),
  KEY `permission_id` (`permission_id`),
  KEY `method_id` (`method_id`),
  CONSTRAINT `key_permissions_ibfk_1` FOREIGN KEY (`key_id`) REFERENCES `user_keys` (`key_id`) ON DELETE CASCADE,
  CONSTRAINT `key_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`),
  CONSTRAINT `key_permissions_ibfk_3` FOREIGN KEY (`method_id`) REFERENCES `methods` (`method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `key_permissions`
--

LOCK TABLES `key_permissions` WRITE;
/*!40000 ALTER TABLE `key_permissions` DISABLE KEYS */;
INSERT INTO `key_permissions` VALUES (1,1,1,1,'2023-10-18 08:6:54',1),(2,1,2,1,'2023-10-18 08:36:54',1);
/*!40000 ALTER TABLE `key_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS ``;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `games` (
  `game_id` INT PRIMARY KEY AUTO_INCREMENT,
  `brand_id` INT,
  `name` varchar(255),
  `description` varchar(255),
  `price` int,
  `status` int
  PRIMARY KEY (`game_id`),
  KEY `brand_id` (`brand_id`),
  KEY `brand` (`brand`),
  CONSTRAINT `games_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `orders` (`orders_id`),
  CONSTRAINT `games_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `gamess` DISABLE KEYS */;
INSERT INTO `games` VALUES (1, 'God of War ', 'good', 1500, '2023-10-18 08:27:01' ),(2, 'Genshin Impact ', 'verygood', 250, '2023-10-18 08:27:01'),(3, 'Honkai Star Rail', 'poor', 50, '2023-10-18 08:27:01'),(4, 'Apex Legends', 'good', 1200, '2023-10-18 08:27:01');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `methods`
--

DROP TABLE IF EXISTS `methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `methods` (
  `method_id` int NOT NULL AUTO_INCREMENT,
  `method` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `methods`
--

LOCK TABLES `methods` WRITE;
/*!40000 ALTER TABLE `methods` DISABLE KEYS */;
INSERT INTO `methods` VALUES (1,'GET','2023-10-18 08:36:36'),(2,'POST','2023-10-18 08:36:36'),(3,'PUT','2023-10-18 08:36:36'),(4,'DELETE','2023-10-18 08:36:36');
/*!40000 ALTER TABLE `methods` ENABLE KEYS */;


UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` INT PRIMARY KEY AUTO_INCREMENT,
  `customer_id` INT,
  `order_date` char(255),
  'status' char (255),
  'total_amount' int,
  `created_at` timestamp
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES ((NULL, 1, '2023-09-10', 'good', 1000, '' ), (NULL, 2, '2023-09-10', 'good', 2000, NOW()););
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'employee','2023-10-18 06:38:55'),(2,'customer','2023-10-18 06:23:06');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_keys`
--

DROP TABLE IF EXISTS `user_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_keys` (
  `key_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `expired` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`key_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_keys`
--

LOCK TABLES `user_keys` WRITE;
/*!40000 ALTER TABLE `user_keys` DISABLE KEYS */;
INSERT INTO `user_keys` VALUES (1,1,'awt_@353!LhJ!2e,+?R%2/3245e23445234',0,'2023-10-18 08:18:55',1);
/*!40000 ALTER TABLE `user_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` int DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` int(66) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `role_id` (`role_id`),
  KEY `address` (`address`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`address`) REFERENCES `addresses` (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'MIGIES', 'VAIRES', 'VAIRESMIG', 'VIEREES@gmail.com', '23 MASD', '', 41, '4543433', '34','2023-10-18 06:23:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-10-18  21:16:43
