-- MySQL dump 10.13  Distrib 9.6.0, for Win64 (x86_64)
--
-- Host: localhost    Database: attendance_db
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adhoc_request`
--

DROP TABLE IF EXISTS `adhoc_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adhoc_request` (
  `adhoc_request_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `date` date NOT NULL,
  `purpose` enum('meeting','emergency') NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`adhoc_request_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `adhoc_request_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `tab1` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adhoc_request`
--

LOCK TABLES `adhoc_request` WRITE;
/*!40000 ALTER TABLE `adhoc_request` DISABLE KEYS */;
INSERT INTO `adhoc_request` VALUES (15,55,'2026-04-06','meeting','ok','2026-04-06 10:13:27','2026-04-06 10:13:27'),(17,23,'2026-04-09','emergency','SICK','2026-04-09 08:17:22','2026-04-09 08:17:22'),(18,1,'2026-04-13','meeting','official meeting','2026-04-13 08:08:52','2026-04-13 08:08:52'),(19,1,'2026-04-14','meeting','official meeting','2026-04-14 01:08:31','2026-04-14 01:08:31');
/*!40000 ALTER TABLE `adhoc_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'National Traditional Medicine Hospital','NTMH','$2y$12$/7WYcfFXsUi8CmwCO.G7JuwTKjqJZQ02.qh1fz.rsbDZiGGq2yThe','active','2026-03-23 10:52:06','2026-04-09 08:15:28');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `attendance_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `shift_type` varchar(255) DEFAULT NULL,
  `attendance_date` date DEFAULT (curdate()),
  `checkin` datetime DEFAULT CURRENT_TIMESTAMP,
  `checkin_address` varchar(300) NOT NULL,
  `checkin_status` varchar(255) DEFAULT NULL,
  `late_reason` varchar(255) DEFAULT NULL,
  `checkout` datetime DEFAULT NULL,
  `checkout_address` varchar(255) DEFAULT NULL,
  `checkout_status` enum('missing','completed') NOT NULL DEFAULT 'missing',
  `remarks` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `employee_id` (`employee_id`,`attendance_date`),
  CONSTRAINT `fk_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `tab1` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (72,1,NULL,'2026-04-14','2026-04-14 21:29:27','Jungzhi Wom Zur Lam 2 NE, ཐིམ་ཕུག, འབྲུག་ཡུལ།','Late','ok',NULL,NULL,'missing','Meeting');
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department` (
  `department_id` int NOT NULL AUTO_INCREMENT,
  `department_name` varchar(150) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES (1,'Administration','active','2026-03-23 11:09:49','2026-04-05 16:47:39'),(2,'Acupuncture','active','2026-03-23 11:09:49','2026-03-23 11:09:49'),(3,'IPD','active','2026-03-23 11:09:49','2026-03-23 11:09:49'),(4,'Jamched','active','2026-03-23 11:09:49','2026-03-23 11:09:49'),(5,'OPD','active','2026-03-23 11:09:49','2026-03-23 11:09:49'),(6,'Tsubched','active','2026-03-23 11:09:49','2026-03-23 11:09:49');
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department_hod`
--

DROP TABLE IF EXISTS `department_hod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_hod` (
  `department_id` int NOT NULL,
  `employee_id` int NOT NULL,
  PRIMARY KEY (`department_id`,`employee_id`),
  KEY `fk_dept_hod_employee` (`employee_id`),
  CONSTRAINT `fk_dept_hod_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dept_hod_employee` FOREIGN KEY (`employee_id`) REFERENCES `tab1` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department_hod`
--

LOCK TABLES `department_hod` WRITE;
/*!40000 ALTER TABLE `department_hod` DISABLE KEYS */;
INSERT INTO `department_hod` VALUES (4,2),(2,4),(5,5),(6,6),(3,7),(1,60);
/*!40000 ALTER TABLE `department_hod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_bindings`
--

DROP TABLE IF EXISTS `device_bindings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_bindings` (
  `device_binding_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned DEFAULT NULL,
  `device_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bind_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`device_binding_id`),
  KEY `device_bindings_employee_id_index` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_bindings`
--

LOCK TABLES `device_bindings` WRITE;
/*!40000 ALTER TABLE `device_bindings` DISABLE KEYS */;
INSERT INTO `device_bindings` VALUES (58,55,'75bda557-98fd-4ed0-88bf-43cea9df6c1e','2026-04-14 06:19:17','2026-04-14 06:19:17','2026-04-14 06:19:17'),(59,1,'6b38d906-ded9-43aa-a8c4-e6089e76c905','2026-04-14 09:17:14','2026-04-14 09:17:14','2026-04-14 09:17:14');
/*!40000 ALTER TABLE `device_bindings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_application`
--

DROP TABLE IF EXISTS `leave_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_application` (
  `application_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `leave_type_id` int NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total_days` decimal(4,1) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `HoD_status` enum('pending','forwarded','rejected') DEFAULT 'pending',
  `HoD_action_by` int DEFAULT NULL,
  `HoD_action_at` timestamp NULL DEFAULT NULL,
  `HoD_note` varchar(255) DEFAULT NULL,
  `medical_superintendent_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `medical_superintendent_action_by` int DEFAULT NULL,
  `medical_superintendent_action_at` timestamp NULL DEFAULT NULL,
  `medical_superintendent_note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  KEY `fk_leave_employee` (`employee_id`),
  KEY `fk_leave_type` (`leave_type_id`),
  KEY `fk_hod_action_by` (`HoD_action_by`),
  KEY `fk_ms_action_by` (`medical_superintendent_action_by`),
  CONSTRAINT `fk_hod_action_by` FOREIGN KEY (`HoD_action_by`) REFERENCES `tab1` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_employee` FOREIGN KEY (`employee_id`) REFERENCES `tab1` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_type` (`leave_type_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ms_action_by` FOREIGN KEY (`medical_superintendent_action_by`) REFERENCES `tab1` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_application`
--

LOCK TABLES `leave_application` WRITE;
/*!40000 ALTER TABLE `leave_application` DISABLE KEYS */;
INSERT INTO `leave_application` VALUES (25,23,1,'2026-04-09','2026-04-11',1.0,'personal','2026-04-09 14:04:38','pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-09 08:04:38','2026-04-09 08:04:38'),(26,38,1,'2026-04-13','2026-04-15',1.0,'Mm','2026-04-13 03:24:41',NULL,NULL,NULL,NULL,'approved',1,'2026-04-13 12:46:23',NULL,'2026-04-12 21:24:41','2026-04-13 12:46:23'),(27,55,1,'2026-04-14','2026-04-17',3.0,'personal','2026-04-14 07:18:44','forwarded',2,'2026-04-14 07:20:02',NULL,'approved',1,'2026-04-14 07:21:49',NULL,'2026-04-14 01:18:44','2026-04-14 07:21:49');
/*!40000 ALTER TABLE `leave_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_balance`
--

DROP TABLE IF EXISTS `leave_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_balance` (
  `leave_balance_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `leave_type_id` int NOT NULL,
  `year` year NOT NULL,
  `max_leave_per_year` decimal(5,1) NOT NULL,
  `used_leave` decimal(5,1) DEFAULT '0.0',
  `remaining_leave` decimal(5,1) GENERATED ALWAYS AS ((`max_leave_per_year` - `used_leave`)) STORED,
  PRIMARY KEY (`leave_balance_id`),
  UNIQUE KEY `employee_id` (`employee_id`,`leave_type_id`,`year`),
  KEY `fk_lb_leave_type` (`leave_type_id`),
  CONSTRAINT `fk_lb_employee` FOREIGN KEY (`employee_id`) REFERENCES `tab1` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lb_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_type` (`leave_type_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=535 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_balance`
--

LOCK TABLES `leave_balance` WRITE;
/*!40000 ALTER TABLE `leave_balance` DISABLE KEYS */;
INSERT INTO `leave_balance` (`leave_balance_id`, `employee_id`, `leave_type_id`, `year`, `max_leave_per_year`, `used_leave`) VALUES (1,1,6,2026,0.0,0.0),(2,1,5,2026,0.0,0.0),(3,1,4,2026,0.0,0.0),(4,1,3,2026,0.0,0.0),(5,1,2,2026,0.0,0.0),(6,1,1,2026,21.0,0.0),(7,38,6,2026,0.0,0.0),(8,38,5,2026,0.0,0.0),(9,38,4,2026,0.0,0.0),(10,38,3,2026,0.0,0.0),(11,38,2,2026,0.0,0.0),(12,38,1,2026,21.0,0.0),(13,60,6,2026,0.0,0.0),(14,60,5,2026,0.0,0.0),(15,60,4,2026,0.0,0.0),(16,60,3,2026,0.0,0.0),(17,60,2,2026,0.0,0.0),(18,60,1,2026,21.0,0.0),(19,61,6,2026,0.0,0.0),(20,61,5,2026,0.0,0.0),(21,61,4,2026,0.0,0.0),(22,61,3,2026,0.0,0.0),(23,61,2,2026,0.0,0.0),(24,61,1,2026,21.0,0.0),(25,62,6,2026,0.0,0.0),(26,62,5,2026,0.0,0.0),(27,62,4,2026,0.0,0.0),(28,62,3,2026,0.0,0.0),(29,62,2,2026,0.0,0.0),(30,62,1,2026,21.0,0.0),(31,63,6,2026,0.0,0.0),(32,63,5,2026,0.0,0.0),(33,63,4,2026,0.0,0.0),(34,63,3,2026,0.0,0.0),(35,63,2,2026,0.0,0.0),(36,63,1,2026,21.0,0.0),(37,64,6,2026,0.0,0.0),(38,64,5,2026,0.0,0.0),(39,64,4,2026,0.0,0.0),(40,64,3,2026,0.0,0.0),(41,64,2,2026,0.0,0.0),(42,64,1,2026,21.0,0.0),(43,65,6,2026,0.0,0.0),(44,65,5,2026,0.0,0.0),(45,65,4,2026,0.0,0.0),(46,65,3,2026,0.0,0.0),(47,65,2,2026,0.0,0.0),(48,65,1,2026,21.0,0.0),(49,66,6,2026,0.0,0.0),(50,66,5,2026,0.0,0.0),(51,66,4,2026,0.0,0.0),(52,66,3,2026,0.0,0.0),(53,66,2,2026,0.0,0.0),(54,66,1,2026,21.0,0.0),(55,67,6,2026,0.0,0.0),(56,67,5,2026,0.0,0.0),(57,67,4,2026,0.0,0.0),(58,67,3,2026,0.0,0.0),(59,67,2,2026,0.0,0.0),(60,67,1,2026,21.0,0.0),(61,68,6,2026,0.0,0.0),(62,68,5,2026,0.0,0.0),(63,68,4,2026,0.0,0.0),(64,68,3,2026,0.0,0.0),(65,68,2,2026,0.0,0.0),(66,68,1,2026,21.0,0.0),(67,69,6,2026,0.0,0.0),(68,69,5,2026,0.0,0.0),(69,69,4,2026,0.0,0.0),(70,69,3,2026,0.0,0.0),(71,69,2,2026,0.0,0.0),(72,69,1,2026,21.0,0.0),(73,70,6,2026,0.0,0.0),(74,70,5,2026,0.0,0.0),(75,70,4,2026,0.0,0.0),(76,70,3,2026,0.0,0.0),(77,70,2,2026,0.0,0.0),(78,70,1,2026,21.0,0.0),(79,71,6,2026,0.0,0.0),(80,71,5,2026,0.0,0.0),(81,71,4,2026,0.0,0.0),(82,71,3,2026,0.0,0.0),(83,71,2,2026,0.0,0.0),(84,71,1,2026,21.0,0.0),(85,72,6,2026,0.0,0.0),(86,72,5,2026,0.0,0.0),(87,72,4,2026,0.0,0.0),(88,72,3,2026,0.0,0.0),(89,72,2,2026,0.0,0.0),(90,72,1,2026,21.0,0.0),(91,73,6,2026,0.0,0.0),(92,73,5,2026,0.0,0.0),(93,73,4,2026,0.0,0.0),(94,73,3,2026,0.0,0.0),(95,73,2,2026,0.0,0.0),(96,73,1,2026,21.0,0.0),(97,74,6,2026,0.0,0.0),(98,74,5,2026,0.0,0.0),(99,74,4,2026,0.0,0.0),(100,74,3,2026,0.0,0.0),(101,74,2,2026,0.0,0.0),(102,74,1,2026,21.0,0.0),(103,75,6,2026,0.0,0.0),(104,75,5,2026,0.0,0.0),(105,75,4,2026,0.0,0.0),(106,75,3,2026,0.0,0.0),(107,75,2,2026,0.0,0.0),(108,75,1,2026,21.0,0.0),(109,77,6,2026,0.0,0.0),(110,77,5,2026,0.0,0.0),(111,77,4,2026,0.0,0.0),(112,77,3,2026,0.0,0.0),(113,77,2,2026,0.0,0.0),(114,77,1,2026,21.0,0.0),(115,80,6,2026,0.0,0.0),(116,80,5,2026,0.0,0.0),(117,80,4,2026,0.0,0.0),(118,80,3,2026,0.0,0.0),(119,80,2,2026,0.0,0.0),(120,80,1,2026,21.0,0.0),(121,81,6,2026,0.0,0.0),(122,81,5,2026,0.0,0.0),(123,81,4,2026,0.0,0.0),(124,81,3,2026,0.0,0.0),(125,81,2,2026,0.0,0.0),(126,81,1,2026,21.0,0.0),(127,82,6,2026,0.0,0.0),(128,82,5,2026,0.0,0.0),(129,82,4,2026,0.0,0.0),(130,82,3,2026,0.0,0.0),(131,82,2,2026,0.0,0.0),(132,82,1,2026,21.0,0.0),(133,83,6,2026,0.0,0.0),(134,83,5,2026,0.0,0.0),(135,83,4,2026,0.0,0.0),(136,83,3,2026,0.0,0.0),(137,83,2,2026,0.0,0.0),(138,83,1,2026,21.0,0.0),(139,84,6,2026,0.0,0.0),(140,84,5,2026,0.0,0.0),(141,84,4,2026,0.0,0.0),(142,84,3,2026,0.0,0.0),(143,84,2,2026,0.0,0.0),(144,84,1,2026,21.0,0.0),(145,85,6,2026,0.0,0.0),(146,85,5,2026,0.0,0.0),(147,85,4,2026,0.0,0.0),(148,85,3,2026,0.0,0.0),(149,85,2,2026,0.0,0.0),(150,85,1,2026,21.0,0.0),(151,86,6,2026,0.0,0.0),(152,86,5,2026,0.0,0.0),(153,86,4,2026,0.0,0.0),(154,86,3,2026,0.0,0.0),(155,86,2,2026,0.0,0.0),(156,86,1,2026,21.0,0.0),(157,87,6,2026,0.0,0.0),(158,87,5,2026,0.0,0.0),(159,87,4,2026,0.0,0.0),(160,87,3,2026,0.0,0.0),(161,87,2,2026,0.0,0.0),(162,87,1,2026,21.0,0.0),(163,88,6,2026,0.0,0.0),(164,88,5,2026,0.0,0.0),(165,88,4,2026,0.0,0.0),(166,88,3,2026,0.0,0.0),(167,88,2,2026,0.0,0.0),(168,88,1,2026,21.0,0.0),(169,89,6,2026,0.0,0.0),(170,89,5,2026,0.0,0.0),(171,89,4,2026,0.0,0.0),(172,89,3,2026,0.0,0.0),(173,89,2,2026,0.0,0.0),(174,89,1,2026,21.0,0.0),(175,2,6,2026,0.0,0.0),(176,2,5,2026,0.0,0.0),(177,2,4,2026,0.0,0.0),(178,2,3,2026,0.0,0.0),(179,2,2,2026,0.0,0.0),(180,2,1,2026,21.0,0.0),(181,3,6,2026,0.0,0.0),(182,3,5,2026,0.0,0.0),(183,3,4,2026,0.0,0.0),(184,3,3,2026,0.0,0.0),(185,3,2,2026,0.0,0.0),(186,3,1,2026,21.0,0.0),(187,4,6,2026,0.0,0.0),(188,4,5,2026,0.0,0.0),(189,4,4,2026,0.0,0.0),(190,4,3,2026,0.0,0.0),(191,4,2,2026,0.0,0.0),(192,4,1,2026,21.0,0.0),(193,10,6,2026,0.0,0.0),(194,10,5,2026,0.0,0.0),(195,10,4,2026,0.0,0.0),(196,10,3,2026,0.0,0.0),(197,10,2,2026,0.0,0.0),(198,10,1,2026,21.0,0.0),(199,18,6,2026,0.0,0.0),(200,18,5,2026,0.0,0.0),(201,18,4,2026,0.0,0.0),(202,18,3,2026,0.0,0.0),(203,18,2,2026,0.0,0.0),(204,18,1,2026,21.0,0.0),(205,43,6,2026,0.0,0.0),(206,43,5,2026,0.0,0.0),(207,43,4,2026,0.0,0.0),(208,43,3,2026,0.0,0.0),(209,43,2,2026,0.0,0.0),(210,43,1,2026,21.0,0.0),(211,48,6,2026,0.0,0.0),(212,48,5,2026,0.0,0.0),(213,48,4,2026,0.0,0.0),(214,48,3,2026,0.0,0.0),(215,48,2,2026,0.0,0.0),(216,48,1,2026,21.0,0.0),(217,7,6,2026,0.0,0.0),(218,7,5,2026,0.0,0.0),(219,7,4,2026,0.0,0.0),(220,7,3,2026,0.0,0.0),(221,7,2,2026,0.0,0.0),(222,7,1,2026,21.0,0.0),(223,12,6,2026,0.0,0.0),(224,12,5,2026,0.0,0.0),(225,12,4,2026,0.0,0.0),(226,12,3,2026,0.0,0.0),(227,12,2,2026,0.0,0.0),(228,12,1,2026,21.0,0.0),(229,22,6,2026,0.0,0.0),(230,22,5,2026,0.0,0.0),(231,22,4,2026,0.0,0.0),(232,22,3,2026,0.0,0.0),(233,22,2,2026,0.0,0.0),(234,22,1,2026,21.0,0.0),(235,27,6,2026,0.0,0.0),(236,27,5,2026,0.0,0.0),(237,27,4,2026,0.0,0.0),(238,27,3,2026,0.0,0.0),(239,27,2,2026,0.0,0.0),(240,27,1,2026,21.0,0.0),(241,31,6,2026,0.0,0.0),(242,31,5,2026,0.0,0.0),(243,31,4,2026,0.0,0.0),(244,31,3,2026,0.0,0.0),(245,31,2,2026,0.0,0.0),(246,31,1,2026,21.0,0.0),(247,34,6,2026,0.0,0.0),(248,34,5,2026,0.0,0.0),(249,34,4,2026,0.0,0.0),(250,34,3,2026,0.0,0.0),(251,34,2,2026,0.0,0.0),(252,34,1,2026,21.0,0.0),(253,35,6,2026,0.0,0.0),(254,35,5,2026,0.0,0.0),(255,35,4,2026,0.0,0.0),(256,35,3,2026,0.0,0.0),(257,35,2,2026,0.0,0.0),(258,35,1,2026,21.0,0.0),(259,36,6,2026,0.0,0.0),(260,36,5,2026,0.0,0.0),(261,36,4,2026,0.0,0.0),(262,36,3,2026,0.0,0.0),(263,36,2,2026,0.0,0.0),(264,36,1,2026,21.0,0.0),(265,39,6,2026,0.0,0.0),(266,39,5,2026,0.0,0.0),(267,39,4,2026,0.0,0.0),(268,39,3,2026,0.0,0.0),(269,39,2,2026,0.0,0.0),(270,39,1,2026,21.0,0.0),(271,50,6,2026,0.0,0.0),(272,50,5,2026,0.0,0.0),(273,50,4,2026,0.0,0.0),(274,50,3,2026,0.0,0.0),(275,50,2,2026,0.0,0.0),(276,50,1,2026,21.0,0.0),(277,76,6,2026,0.0,0.0),(278,76,5,2026,0.0,0.0),(279,76,4,2026,0.0,0.0),(280,76,3,2026,0.0,0.0),(281,76,2,2026,0.0,0.0),(282,76,1,2026,21.0,0.0),(283,78,6,2026,0.0,0.0),(284,78,5,2026,0.0,0.0),(285,78,4,2026,0.0,0.0),(286,78,3,2026,0.0,0.0),(287,78,2,2026,0.0,0.0),(288,78,1,2026,21.0,0.0),(289,79,6,2026,0.0,0.0),(290,79,5,2026,0.0,0.0),(291,79,4,2026,0.0,0.0),(292,79,3,2026,0.0,0.0),(293,79,2,2026,0.0,0.0),(294,79,1,2026,21.0,0.0),(295,16,6,2026,0.0,0.0),(296,16,5,2026,0.0,0.0),(297,16,4,2026,0.0,0.0),(298,16,3,2026,0.0,0.0),(299,16,2,2026,0.0,0.0),(300,16,1,2026,21.0,0.0),(301,17,6,2026,0.0,0.0),(302,17,5,2026,0.0,0.0),(303,17,4,2026,0.0,0.0),(304,17,3,2026,0.0,0.0),(305,17,2,2026,0.0,0.0),(306,17,1,2026,21.0,0.0),(307,21,6,2026,0.0,0.0),(308,21,5,2026,0.0,0.0),(309,21,4,2026,0.0,0.0),(310,21,3,2026,0.0,0.0),(311,21,2,2026,0.0,0.0),(312,21,1,2026,21.0,0.0),(313,24,6,2026,0.0,0.0),(314,24,5,2026,0.0,0.0),(315,24,4,2026,0.0,0.0),(316,24,3,2026,0.0,0.0),(317,24,2,2026,0.0,0.0),(318,24,1,2026,21.0,0.0),(319,33,6,2026,0.0,0.0),(320,33,5,2026,0.0,0.0),(321,33,4,2026,0.0,0.0),(322,33,3,2026,0.0,0.0),(323,33,2,2026,0.0,0.0),(324,33,1,2026,21.0,0.0),(325,41,6,2026,0.0,0.0),(326,41,5,2026,0.0,0.0),(327,41,4,2026,0.0,0.0),(328,41,3,2026,0.0,0.0),(329,41,2,2026,0.0,0.0),(330,41,1,2026,21.0,0.0),(331,42,6,2026,0.0,0.0),(332,42,5,2026,0.0,0.0),(333,42,4,2026,0.0,0.0),(334,42,3,2026,0.0,0.0),(335,42,2,2026,0.0,0.0),(336,42,1,2026,21.0,0.0),(337,44,6,2026,0.0,0.0),(338,44,5,2026,0.0,0.0),(339,44,4,2026,0.0,0.0),(340,44,3,2026,0.0,0.0),(341,44,2,2026,0.0,0.0),(342,44,1,2026,21.0,0.0),(343,51,6,2026,0.0,0.0),(344,51,5,2026,0.0,0.0),(345,51,4,2026,0.0,0.0),(346,51,3,2026,0.0,0.0),(347,51,2,2026,0.0,0.0),(348,51,1,2026,21.0,0.0),(349,52,6,2026,0.0,0.0),(350,52,5,2026,0.0,0.0),(351,52,4,2026,0.0,0.0),(352,52,3,2026,0.0,0.0),(353,52,2,2026,0.0,0.0),(354,52,1,2026,21.0,0.0),(355,53,6,2026,0.0,0.0),(356,53,5,2026,0.0,0.0),(357,53,4,2026,0.0,0.0),(358,53,3,2026,0.0,0.0),(359,53,2,2026,0.0,0.0),(360,53,1,2026,21.0,0.0),(361,55,6,2026,0.0,0.0),(362,55,5,2026,0.0,0.0),(363,55,4,2026,0.0,0.0),(364,55,3,2026,0.0,0.0),(365,55,2,2026,0.0,0.0),(366,55,1,2026,21.0,0.0),(367,56,6,2026,0.0,0.0),(368,56,5,2026,0.0,0.0),(369,56,4,2026,0.0,0.0),(370,56,3,2026,0.0,0.0),(371,56,2,2026,0.0,0.0),(372,56,1,2026,21.0,0.0),(373,57,6,2026,0.0,0.0),(374,57,5,2026,0.0,0.0),(375,57,4,2026,0.0,0.0),(376,57,3,2026,0.0,0.0),(377,57,2,2026,0.0,0.0),(378,57,1,2026,21.0,0.0),(379,58,6,2026,0.0,0.0),(380,58,5,2026,0.0,0.0),(381,58,4,2026,0.0,0.0),(382,58,3,2026,0.0,0.0),(383,58,2,2026,0.0,0.0),(384,58,1,2026,21.0,0.0),(385,5,6,2026,0.0,0.0),(386,5,5,2026,0.0,0.0),(387,5,4,2026,0.0,0.0),(388,5,3,2026,0.0,0.0),(389,5,2,2026,0.0,0.0),(390,5,1,2026,21.0,0.0),(391,11,6,2026,0.0,0.0),(392,11,5,2026,0.0,0.0),(393,11,4,2026,0.0,0.0),(394,11,3,2026,0.0,0.0),(395,11,2,2026,0.0,0.0),(396,11,1,2026,21.0,0.0),(397,13,6,2026,0.0,0.0),(398,13,5,2026,0.0,0.0),(399,13,4,2026,0.0,0.0),(400,13,3,2026,0.0,0.0),(401,13,2,2026,0.0,0.0),(402,13,1,2026,21.0,0.0),(403,14,6,2026,0.0,0.0),(404,14,5,2026,0.0,0.0),(405,14,4,2026,0.0,0.0),(406,14,3,2026,0.0,0.0),(407,14,2,2026,0.0,0.0),(408,14,1,2026,21.0,0.0),(409,15,6,2026,0.0,0.0),(410,15,5,2026,0.0,0.0),(411,15,4,2026,0.0,0.0),(412,15,3,2026,0.0,0.0),(413,15,2,2026,0.0,0.0),(414,15,1,2026,21.0,0.0),(415,19,6,2026,0.0,0.0),(416,19,5,2026,0.0,0.0),(417,19,4,2026,0.0,0.0),(418,19,3,2026,0.0,0.0),(419,19,2,2026,0.0,0.0),(420,19,1,2026,21.0,0.0),(421,25,6,2026,0.0,0.0),(422,25,5,2026,0.0,0.0),(423,25,4,2026,0.0,0.0),(424,25,3,2026,0.0,0.0),(425,25,2,2026,0.0,0.0),(426,25,1,2026,21.0,0.0),(427,26,6,2026,0.0,0.0),(428,26,5,2026,0.0,0.0),(429,26,4,2026,0.0,0.0),(430,26,3,2026,0.0,0.0),(431,26,2,2026,0.0,0.0),(432,26,1,2026,21.0,0.0),(433,29,6,2026,0.0,0.0),(434,29,5,2026,0.0,0.0),(435,29,4,2026,0.0,0.0),(436,29,3,2026,0.0,0.0),(437,29,2,2026,0.0,0.0),(438,29,1,2026,21.0,0.0),(439,32,6,2026,0.0,0.0),(440,32,5,2026,0.0,0.0),(441,32,4,2026,0.0,0.0),(442,32,3,2026,0.0,0.0),(443,32,2,2026,0.0,0.0),(444,32,1,2026,21.0,0.0),(445,37,6,2026,0.0,0.0),(446,37,5,2026,0.0,0.0),(447,37,4,2026,0.0,0.0),(448,37,3,2026,0.0,0.0),(449,37,2,2026,0.0,0.0),(450,37,1,2026,21.0,0.0),(451,40,6,2026,0.0,0.0),(452,40,5,2026,0.0,0.0),(453,40,4,2026,0.0,0.0),(454,40,3,2026,0.0,0.0),(455,40,2,2026,0.0,0.0),(456,40,1,2026,21.0,0.0),(457,46,6,2026,0.0,0.0),(458,46,5,2026,0.0,0.0),(459,46,4,2026,0.0,0.0),(460,46,3,2026,0.0,0.0),(461,46,2,2026,0.0,0.0),(462,46,1,2026,21.0,0.0),(463,49,6,2026,0.0,0.0),(464,49,5,2026,0.0,0.0),(465,49,4,2026,0.0,0.0),(466,49,3,2026,0.0,0.0),(467,49,2,2026,0.0,0.0),(468,49,1,2026,21.0,0.0),(469,54,6,2026,0.0,0.0),(470,54,5,2026,0.0,0.0),(471,54,4,2026,0.0,0.0),(472,54,3,2026,0.0,0.0),(473,54,2,2026,0.0,0.0),(474,54,1,2026,21.0,0.0),(475,6,6,2026,0.0,0.0),(476,6,5,2026,0.0,0.0),(477,6,4,2026,0.0,0.0),(478,6,3,2026,0.0,0.0),(479,6,2,2026,0.0,0.0),(480,6,1,2026,21.0,0.0),(481,8,6,2026,0.0,0.0),(482,8,5,2026,0.0,0.0),(483,8,4,2026,0.0,0.0),(484,8,3,2026,0.0,0.0),(485,8,2,2026,0.0,0.0),(486,8,1,2026,21.0,0.0),(487,9,6,2026,0.0,0.0),(488,9,5,2026,0.0,0.0),(489,9,4,2026,0.0,0.0),(490,9,3,2026,0.0,0.0),(491,9,2,2026,0.0,0.0),(492,9,1,2026,21.0,0.0),(493,20,6,2026,0.0,0.0),(494,20,5,2026,0.0,0.0),(495,20,4,2026,0.0,0.0),(496,20,3,2026,0.0,0.0),(497,20,2,2026,0.0,0.0),(498,20,1,2026,21.0,0.0),(499,23,6,2026,0.0,0.0),(500,23,5,2026,0.0,0.0),(501,23,4,2026,0.0,0.0),(502,23,3,2026,0.0,0.0),(503,23,2,2026,0.0,0.0),(504,23,1,2026,21.0,0.0),(505,28,6,2026,0.0,0.0),(506,28,5,2026,0.0,0.0),(507,28,4,2026,0.0,0.0),(508,28,3,2026,0.0,0.0),(509,28,2,2026,0.0,0.0),(510,28,1,2026,21.0,0.0),(511,30,6,2026,0.0,0.0),(512,30,5,2026,0.0,0.0),(513,30,4,2026,0.0,0.0),(514,30,3,2026,0.0,0.0),(515,30,2,2026,0.0,0.0),(516,30,1,2026,21.0,0.0),(517,45,6,2026,0.0,0.0),(518,45,5,2026,0.0,0.0),(519,45,4,2026,0.0,0.0),(520,45,3,2026,0.0,0.0),(521,45,2,2026,0.0,0.0),(522,45,1,2026,21.0,0.0),(523,47,6,2026,0.0,0.0),(524,47,5,2026,0.0,0.0),(525,47,4,2026,0.0,0.0),(526,47,3,2026,0.0,0.0),(527,47,2,2026,0.0,0.0),(528,47,1,2026,21.0,0.0),(529,59,6,2026,0.0,0.0),(530,59,5,2026,0.0,0.0),(531,59,4,2026,0.0,0.0),(532,59,3,2026,0.0,0.0),(533,59,2,2026,0.0,0.0),(534,59,1,2026,21.0,0.0);
/*!40000 ALTER TABLE `leave_balance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_type`
--

DROP TABLE IF EXISTS `leave_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_type` (
  `leave_type_id` int NOT NULL AUTO_INCREMENT,
  `leave_name` varchar(100) NOT NULL,
  `leave_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `max_per_year` int NOT NULL DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`leave_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_type`
--

LOCK TABLES `leave_type` WRITE;
/*!40000 ALTER TABLE `leave_type` DISABLE KEYS */;
INSERT INTO `leave_type` VALUES (1,'Annual leave','AL','yearly vacation leave',21,'active','2026-03-23 14:17:54','2026-04-04 11:50:18'),(2,'Bereavement leave','BL','family death leave',0,'active','2026-03-23 14:17:54','2026-03-25 22:59:14'),(3,'Medical leave','ML','Medical condition leave',0,'active','2026-03-23 14:17:54','2026-03-25 22:57:57'),(4,'Maternity leave','MTL','child birth leave for mother',0,'active','2026-03-23 14:17:54','2026-03-23 14:17:54'),(5,'Paternity leave','PL','childbirth leave for father',0,'active','2026-03-23 14:17:54','2026-03-23 14:17:54'),(6,'Others','OTH','other special leave',0,'active','2026-03-23 14:17:54','2026-03-23 14:17:54');
/*!40000 ALTER TABLE `leave_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_24_032951_make_shift_type_nullable_in_attendance_table',2),(5,'2026_03_24_033217_make_checkin_status_nullable_in_attendance_table',3),(6,'2026_03_24_033435_make_checkout_fields_nullable_in_attendance_table',4),(7,'2026_03_24_034809_remove_default_checkout_timestamp_from_attendance_table',5),(9,'2026_03_24_130000_make_leave_action_columns_nullable',6),(10,'2026_03_24_200000_add_profile_picture_to_users_table',7),(11,'2026_03_26_031926_create_leave_types_table',8),(12,'2026_03_26_999999_add_office_order_pdf_to_tour_records',9),(13,'2026_04_04_000001_update_checkout_status_enum_in_attendance_table',10),(14,'2026_04_06_000000_create_adhoc_requests_table',10),(15,'2026_04_07_000000_create_audit_logs_table',11),(16,'2026_04_07_000001_fix_audit_logs_ints',12),(17,'2026_04_07_000000_restore_device_bindings_table',13),(18,'2026_04_07_000002_rename_device_bindings_id_to_device_binding_id',14);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission` (
  `permission_id` int NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(150) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission`
--

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` VALUES (1,'Apply leave','active','2026-03-23 14:41:39','2026-04-05 14:56:40'),(2,'Approve leave','active','2026-03-23 14:41:39','2026-03-23 14:41:39'),(3,'Forward leave','active','2026-03-23 14:41:39','2026-03-23 14:41:39'),(4,'Reject leave','active','2026-03-23 14:41:39','2026-03-23 14:41:39');
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Medical Superintendent','active','2026-03-23 11:02:16','2026-03-23 11:02:16'),(2,'HoD','active','2026-03-23 11:02:16','2026-04-05 14:36:39'),(3,'Employee','active','2026-03-23 11:02:16','2026-04-05 14:56:33');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_role_permission_permission` (`permission_id`),
  CONSTRAINT `fk_role_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES (2,1);
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0nArMlZGRIzyPGI5CvXdMWRmlGzjsbTHOxh7GpuI',3,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','eyJfdG9rZW4iOiI3Z2tqOW1LSHB4OXoyNUZGWmVQclpUamttVWJoUGt4TkdJNXBocTRqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvZGV2aWNlLWJpbmRpbmdzIiwicm91dGUiOiJhZG1pbi5kZXZpY2VfYmluZGluZ3MifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJhZG1pbl9sb2dnZWRfaW4iOnRydWUsImFkbWluX2lkIjoxLCJhZG1pbl91c2VyIjoiTlRNSCIsImFkbWluX25hbWUiOiJOYXRpb25hbCBUcmFkaXRpb25hbCBNZWRpY2luZSBIb3NwaXRhbCIsImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjozLCJlaWQiOiIyMDExMDczNDkiLCJ1c2VybmFtZSI6IjIwMTEwNzM0OSJ9',1776006896),('2urxSoTczKl0ZmiOED296KoexOwhBYcPfhloLFv1',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJtQWpyM0FqUks0WmtUVXFZR2VuY1FzSEVPdE91NmZ5SGg3OHh2NzJDIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1775925380),('3gJyRARrleRdWSO5xomCYRJNNYmvMxn7vEpLjM0D',NULL,'192.168.1.5','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJwcUw2SVpoRDlydE82Qjc5QUpSY1dDZGhweTVaT2J5R0FaUTMzbjVYIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1776006071),('4SeS9WBAERelnhrFrsbd0uOvbGPkGga3N6vrwmDB',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiI4SmhjcEp6a3VhcVd4clgzYUlaemh0cGFyTUNBazl4VFliQUk0dlNJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925424),('7qKG0tnEPk7Q5SFW7PizPoaiPnDXWsps7TRxyfqM',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','eyJfdG9rZW4iOiJXaFY3QURobjhQUXhURmluMEpGMkxUQ3VkVkhIV2lkZ1BpOUxYVkpSIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluLWxvZ2luIiwicm91dGUiOiJhZG1pbi5sb2dpbiJ9fQ==',1775930575),('8tl023Mfp8zL6pPWb8hXHxbTI8yTHeaZIWWmR2L6',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJVQzlzcWFyTVBjYlRRWUIxbjNwN1FhbTZsc1czd2t0YlhzemVuM3VLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925356),('Aj2EQ1y5O7w9F1zE9o1tSNvmLxpx1LT0VywAKfqh',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','eyJfdG9rZW4iOiJXcUVBdXhKZk5WNUhKQUVmd0NnR25sekVwYUlvazBoVmRTUHQxUFlGIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbi1sb2dpbiIsInJvdXRlIjoiYWRtaW4ubG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1775925648),('AzDBwVpCYUA44JpKN0P0EazKHfAYGL5iZgGVXxhB',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJFVU5SaGhaUm9mTEg4VUNjRFd2Y3RBZ3RyNG81NElJOHpiUjZUZVpEIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2FkbWluLWxvZ2luIiwicm91dGUiOiJhZG1pbi5sb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925439),('d3KeJF0X6O4lrl7ZkVZqH5IvT1211BKQlupOStF9',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJTVjFQZUxnd0EzOFpaUzloZzdzRnBwSHN3bGRsNmd6TkVnR3JoSEd1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1776006913),('EIg6a1L5BNtQBQvjojcqr8ObMGjVlKizybHx3ZtK',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiI4Q2JxeHl3cHFHVGhTQmhqZ1J6UUNSVmNrQVFZY3d6UkNHdVc2cjB4IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTkyLjE2OC4xLjc6ODAwMFwvZGFzaGJvYXJkIn0sIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xOTIuMTY4LjEuNzo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925424),('EodgoY2IZMsBGcWWu58idKtwNIFLoKWHPsp83QKH',4,'192.168.1.5','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJzSWRRZ3lIM29sbEZRbjFIQ21jTVNKeTRwTWhXa1cwMExseDh0WlJrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo0LCJlaWQiOiIyMDExMDczNTEiLCJ1c2VybmFtZSI6IjIwMTEwNzM1MSJ9',1776006071),('gsGienjU6A45VJouyqMZwrSuJkCapto6jlNygOrn',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','eyJfdG9rZW4iOiJEUlQ0VzBWcEhwMFdDbkQ4ZUlGbjhubjV2WkpDMzVrTmp0R1cxcXU0IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluLWxvZ2luIiwicm91dGUiOiJhZG1pbi5sb2dpbiJ9fQ==',1775927615),('hFSay8gG1oikJ1koLdjUFymnPs8kWNSK4yKjW83f',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ6SUY5Qlc4ZG12OHU3OTJIUDAweXMxZHpyQVJtUzBuam10M25kazNLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDAiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1775925576),('J5tKnl8ODveIZGT8gOpMIXJoys0BjxBJlxPgc5iW',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ0cXlhUHNJMDVYbUwzdnh0ZXU2c3NzUEVKTEg3bGtZaU5UOWUzUGtPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925576),('jp2OM9XoOKQicq10aXszU9fhmbVHGGV6q9d4pHNq',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ5bVN2UTJCbGpUQW9kMW80VVFwdUM3aTkzWHJpdkdYRmZPZmRuQ0N3IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTkyLjE2OC4xLjc6ODAwMFwvZGFzaGJvYXJkIn0sIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xOTIuMTY4LjEuNzo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925354),('k3ZM2t6WwPVJPc11mfawV6iC9w7V3yeHZpgDImxf',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ6MUtTWTJSaGx5dnEzSmpyc3VhR0dzbnA3a29qMFgxeks4YXlhUWVaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925390),('KpccxwBzTu7de9PE2PrNXBKv3FEgoFItZRG8ZLYT',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJuSnhGY3JaR2NCM0FGSTRvcHdMMDdXT1RxeGdrY1JOa2RlTXhUNG02IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1775925611),('lv90NRtwmcPBLPD1JP5uyRii7rS7rmVIjaFpPm8Q',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJjQk9lWmhoSjV5alliUnd3cUFZZjlrRnJqc2VPeGFXRXRFaG9seFFpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925632),('nKwdMHY9b0ixBXQMkuBMGrsbwmf2LnfnmRwhtKqI',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJsdkx4Y3BTYXNsbDU2ZmVwTjc3OVNVWUJKeWlmVHBBMElFam45eno0IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1775925764),('OEEvAgtlPIPnLfhi3G9Ne3251YXVihm2Q0zrMBok',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ3WE1QSlpnRkFzdnRaWjV6djhtSjdPTnRYbU5MTmJBN3A3Y2VOMW5RIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925739),('qtqEJCza2cqCRBIwf1tHvdUegIl4bmBuzQoYF5gb',4,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJPOVVjSXMzZzhOTlFUekpySjRYMEtON2oyTEhNZXdkYWRwb2RTTG50IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo0LCJlaWQiOiIyMDExMDczNTEiLCJ1c2VybmFtZSI6IjIwMTEwNzM1MSJ9',1776005842),('uHmN8ycraqYo4pBlGywzLufFmXcaIozqV4j17y4g',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJYYkxBNmg4anN4a090UGlCVmxOYjRVNFNBM3N3MW9sSWFBVlJ0V2RBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2FkbWluLWxvZ2luIiwicm91dGUiOiJhZG1pbi5sb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925636),('uN8Md5dVUIgpJkXfYn55Khnr9eRlpiWYCy9a7FgM',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJNWk9NU0JES1NIUEZCSmFhZmF3UWpVRzQ0QVVpUjhZZmNBbm9uS2FSIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1775925414),('VUS3HtKtYsg6rDt43uq9rJ4EAQbxZyUKsQEbLVRO',NULL,'192.168.1.6','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJCelU4T3JBQ2kyQTFGRVA5R1dWNEVxc3R1V0FCb2o0VDRFZVZkZXB0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguMS43OjgwMDBcL2FkbWluLWxvZ2luIiwicm91dGUiOiJhZG1pbi5sb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1775925427),('WKhumHhUM1212SH9tfbUEa7nSFYJyGNVPIoPi36h',4,'192.168.1.2','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJMWWU0VFNueHlXcVNIZ1pLMUgzUVZ2SDZ5NmowMTBlUldEMFJxNHhZIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTkyLjE2OC4xLjc6ODAwMFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjQsImVpZCI6IjIwMTEwNzM1MSIsInVzZXJuYW1lIjoiMjAxMTA3MzUxIn0=',1775929232),('YwaZXcpembKJwMJbHQLtHwdcMrIDndA36FSlFuqE',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0','eyJfdG9rZW4iOiIwWXlzVk1GZ0tadG1rWDMzR25Pc1dMT2VKRnNmNk9oeGJQZG1KOTl6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbi1sb2dpbiIsInJvdXRlIjoiYWRtaW4ubG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1776005490);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab1`
--

DROP TABLE IF EXISTS `tab1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tab1` (
  `employee_id` int NOT NULL AUTO_INCREMENT,
  `eid` varchar(50) DEFAULT NULL,
  `employee_name` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `department_id` int NOT NULL,
  `role_id` int NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `eid` (`eid`),
  KEY `department_id` (`department_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `tab1_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`),
  CONSTRAINT `tab1_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab1`
--

LOCK TABLES `tab1` WRITE;
/*!40000 ALTER TABLE `tab1` DISABLE KEYS */;
INSERT INTO `tab1` VALUES (1,'9511044','Drg.Karma Gaylek','9511044','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Medical superintendent',1,1,'Active','2026-03-23 12:07:45','2026-04-14 07:10:34'),(2,'9901030','Drg.Ugyen Wangchuk','9901030','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',4,2,'Active','2026-03-23 12:07:45','2026-04-06 08:53:42'),(3,'9901033','Drg.Dorji Uden','9901033','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',2,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(4,'2001092','Drg.Tharpala','2001092','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',2,2,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(5,'2001089','Drg.Tandin Phurba','2001089','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',5,2,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(6,'201001147','Drg.Karma Ugyen','201001147','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',6,2,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(7,'20130101261','Drg.Singye Wangmo','20130101261','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',3,2,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(8,'20150105063','Drg.Ngawang Namgay','20150105063','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(9,'20150105038','Drg.Deki Choden','20150105038','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(10,'20170107859','Drg.Dorji Gyeltshen','20170107859','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',2,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(11,'9901031','Drg.Tashi Wangchuk','9901031','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(12,'20150105043','Drg.Yeshi Choden','20150105043','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',5,3,'Active','2026-03-23 12:07:45','2026-03-30 15:44:06'),(13,'200601130','Drg.Chogyel Dorji','200601130','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(14,'20200116169','Drg.Phubzam','20200116169','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(15,'202201920528','Drg.Pema Wangda','202201920528','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Drungtsho',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(16,'200206052','Nima Dorji','200206052','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(17,'200206055','Tshering Peldon','200206055','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(18,'200311038','Phub Dem','200311038','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',2,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(19,'200507292','Sherab Tharchen','200507292','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',1,3,'Active','2026-03-23 12:07:45','2026-03-28 15:30:02'),(20,'200507294','Laxmi Das Rai','200507294','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(21,'201107348','Pema Lhadon','201107348','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(22,'201107351','Pema Tshomo','201107351','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(23,'9705004','Dhendup Zangpo','9705004','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(24,'201103008','Sangay','201103008','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(25,'20120700817','Pema Dema','20120700817','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(26,'20120700819','Kinley Wangchuk','20120700819','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(27,'20130802688','Dorjee Pemo','20130802688','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(28,'20130802569','Jigme Dorji','20130802569','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(29,'20130802690','Sherab Zangmo','20130802690','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-28 15:30:37'),(30,'20130802573','Yangchen Lham','20130802573','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(31,'20151106005','Tshering Phuntsho','20151106005','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(32,'20140804702','Jamyang Choeda','20140804702','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',1,3,'Active','2026-03-23 12:07:45','2026-03-28 15:31:09'),(33,'20151105953','Passang Bidha','20151105953','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(34,'20140804703','Chimi Lhamo','20140804703','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(35,'201103001','Tenzin Dorji','201103001','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(36,'20170108264','Ugyen','20170108264','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(37,'9201094','Ugyen Phuntsho','9201094','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-28 15:40:52'),(38,'200507295','Tshering Wangchuk','200507295','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(39,'20151105967','Dechen Dema','20151105967','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(40,'20190112651','Chador Wangmo','20190112651','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(41,'201107350','Jinpa Zangmo','201107350','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(42,'201103003','Sonam Dorji','201103003','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(43,'20190112653','Namgay Choden','20190112653','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',2,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(44,'20190112655','Kezang Dorji','20190112655','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(45,'20180110785','Dhan Bahadur Gurung','20180110785','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(46,'20120700823','Phub Wangmo','20120700823','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(47,'20200116900','Karma Dema','20200116900','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(48,'20120700821','Choki Wangchuk','20120700821','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',2,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(49,'20140804706','Pema Tobgay','20140804706','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',5,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(50,'202401926327','Chimi Wangmo','202401926327','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(51,'202101918212','Tashi Wangchuk','202101918212','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(52,'20200116897','Tshering Peldon','20200116897','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(53,'20200116896','Sangay Dema','20200116896','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(54,'202409928666','Kinzang','202409928666','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-28 15:41:11'),(55,'201107349','Dorji Phuntsho','201107349','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-28 17:43:33'),(56,'200807308','Leki wangdi','200807308','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(57,'201107345','Tshering Pem','201107345','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(58,'202201920899','Kinga Wangmo','202201920899','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',4,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(59,'202301923079','Tshewang Peldon','202301923079','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','sMenpa',6,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(60,'9107014','Dorji Wangmo Lhaki','9107014','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','ADM Assistant',1,2,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(61,'200407138','Tashi Yangzom','200407138','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Technician',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(62,'201003029','Ugyen Wangdi','201003029','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Technician',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(63,'201107157','Gyem Lhamo','201107157','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Medical Record Technician',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(64,'200307001','Thinley Jamtsho','200307001','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','ADM Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(65,'201107144','Lhachey Wangmo','201107144','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Store keeper',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(66,'20200116120','Biran Gurung','20200116120','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Therapy Aide',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(67,'201008005','Ugyen Dorji','201008005','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','PhysiotherapyAide',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(68,'200901774','Tashi Wangmo','200901774','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Front Desk Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(69,'200204092','Kezang Choden','200204092','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Front Desk Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(70,'2009067','Pema Yangtsho','2009067','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Driver',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(71,'200601143','Sangay Wangmo','200601143','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Front Desk Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(72,'10905001754','Neten Zangmo','10905001754','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Helper/Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(73,'11512001930','Tsheltrim Zangmo','11512001930','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Gardener',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(74,'10716000340','Tshering Yuden','10716000340','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Helper/Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(75,'11510002457','Gyalmo','11510002457','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Helper/Assistant',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(76,'10708000781','Rinchen Zangmo','10708000781','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Ward Girl',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(77,'10202000522','Choki Zangmo','10202000522','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Washer-man',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(78,'11104000319','Sonam Choden','11104000319','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Cook/Baker',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(79,'11512000082','Choki','11512000082','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Cook/Baker',3,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(80,'12002000463','Ugyen Dorji','12002000463','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Security Guard',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(81,'11504000575','Lobzang Dema','11504000575','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Wet Cleaner',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(82,'10601000322','Karma Wangmo','10601000322','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Cldearner(Wet/Dry)',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(83,'10701001124','Pema Dema','10701001124','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Wet Cleaner',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(84,'10715001339','Rinchen Wangmo','10715001339','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','WetCleaner',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(85,'11105004178','Tshering Peldon','11105004178','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Wet Cleaner',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(86,'10207002262','Dawa Gyelmo','10207002262','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Ward Girl',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(87,'200903030','Karma Dorji','200903030','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','Front Desk Assistant',1,3,'Inactive','2026-03-23 12:07:45','2026-04-14 08:08:37'),(88,'20240192698','Namgay Zam','20240192698','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','PhysiotherapyAide',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(89,'202405927820','Tashi Gyaley','202405927820','$2y$12$diTPo04DaVXmR6RWdvCEhuskFAiwpmI2JaN4BfV/aWeDPcCndxEeu','PhysiotherapyAide',1,3,'Active','2026-03-23 12:07:45','2026-03-23 17:18:44'),(92,'20170107864','Drg.Nim Dorji','20170107864','$2y$12$P/eqB5wCdt/cM0/TIe5PNuKMRhKjl6o84lctFOXS6KLDtBm9B3e/S','Drungtsho',5,3,'Active','2026-04-02 05:45:51','2026-04-02 06:25:44'),(93,'202401926317','Kezang Norbu','202401926317','$2y$12$2c4ZU55HCndcA41Msj2.l.xZ4c6cF8.5PrvIgZlfSFc2QmZh2j542','sMenpa',5,3,'Active','2026-04-02 05:48:24','2026-04-02 05:48:24');
/*!40000 ALTER TABLE `tab1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tour_records`
--

DROP TABLE IF EXISTS `tour_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tour_records` (
  `tour_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `department_id` int NOT NULL,
  `place` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `purpose` text,
  `office_order_pdf` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tour_id`),
  KEY `employee_id` (`employee_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `tour_records_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `tab1` (`employee_id`),
  CONSTRAINT `tour_records_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tour_records`
--

LOCK TABLES `tour_records` WRITE;
/*!40000 ALTER TABLE `tour_records` DISABLE KEYS */;
INSERT INTO `tour_records` VALUES (18,1,1,'thimphu','2026-04-08','2026-04-11','official meeting','office_orders/office_order_1_1775668088.pdf','2026-04-08 17:08:09','2026-04-08 17:08:09'),(19,55,4,'Paro','2026-04-08','2026-04-11','official meeting','office_orders/office_order_55_1775668427.pdf','2026-04-08 17:13:47','2026-04-08 17:13:47'),(20,23,6,'Paro','2026-04-09','2026-04-14','official meeting',NULL,'2026-04-09 14:16:53','2026-04-09 14:16:53'),(21,1,1,'Paro','2026-04-15','2026-04-18','official meeting','office_orders/office_order_1_1776152138.pdf','2026-04-14 07:35:39','2026-04-14 07:35:39');
/*!40000 ALTER TABLE `tour_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com',NULL,'2026-03-23 03:49:50','$2y$12$nAktX7FILzSl6mdBx/MBnufRJQvb6nMcQe4dQhnUWJSXbiPu9wOqS','mKqXbE2C6P','2026-03-23 03:49:51','2026-03-25 22:48:57'),(2,'Drg.Ugyen Wangchuk','9901030@alms.local',NULL,NULL,'$2y$12$b9PKRHknCe2cJMc/gscNzebSrClJh/57hlQeJVDONNrn1Nbtw1poS',NULL,'2026-03-23 10:50:27','2026-03-23 10:50:27'),(3,'Dorji Phuntsho','201107349@alms.local','3_1774374947.jpg',NULL,'$2y$12$6KHxmpNva4N.YKm.AM/zRu6s3zbGUe8woMIJWhNMGFLCy7ZXHJLwq','vCEXiM6NLNrSSNksmYmq4vM8cr4bt1x1Zeag1LRTgUfY9fOch99c92Rjl9fW','2026-03-23 21:21:10','2026-03-23 21:21:10'),(4,'Pema Tshomo','201107351@alms.local',NULL,NULL,'$2y$12$qWHa6m4Y6mCOfWMLFn7lzervAqvlBVtYfkXR9tosOdBzzCF2/rrEG',NULL,'2026-03-23 21:31:11','2026-03-23 21:31:11'),(5,'Dhendup Zangpo','9705004@alms.local',NULL,NULL,'$2y$12$KttfNaYov8YvUZfM/N6rMe0Hb8mGFDvkokUdu7EZhRbT8DmN60cFO',NULL,'2026-03-23 22:57:32','2026-03-23 22:57:32'),(6,'Drg.Karma Gaylek','9511044@alms.local',NULL,NULL,'$2y$12$zVfQ/wbvw1sMW15t2T4bq.1S/hzJ9M/c6gt/TofW0JTqZlTI2HKIG',NULL,'2026-03-24 02:43:09','2026-03-24 02:43:09'),(7,'Drg.Tandin Phurba','2001089@alms.local',NULL,NULL,'$2y$12$Li5dhy/s.aT60BTpNpUePOqZC5XfSnqJQUf3K5M15DnOHwl9LXapa',NULL,'2026-03-28 09:37:44','2026-03-28 09:37:44'),(8,'Drg.Yeshi Choden','20150105043@alms.local',NULL,NULL,'$2y$12$X52Cy4Tn8X.sT7tiFjg6A.C0ANyIjSe.AmPgt5fC85g8PcDe49JAK',NULL,'2026-03-29 22:16:49','2026-03-29 22:16:49'),(9,'Drg.Nima Dorji','20170107864@alms.local',NULL,NULL,'$2y$12$XaMrI/IizWMZX.9oB/9JM.hq7pD0opS2ABNWBv9E8SYjXKLnk1Bfa',NULL,'2026-04-01 23:29:23','2026-04-01 23:29:23'),(10,'Jamyang Choeda','20140804702@alms.local',NULL,NULL,'$2y$12$U1yVFFoXVTtX18KjkYl0s.9.scFw2dmw/3YU8Y213kaHQGBkNrRGK',NULL,'2026-04-06 02:59:34','2026-04-06 02:59:34'),(11,'Drg.Tharpala','2001092@alms.local',NULL,NULL,'$2y$12$gBbQH1A0A2OgeoV3cU24V.jMch28lA4p0yLFK/9HH0yGaTqyeczvW',NULL,'2026-04-08 05:05:52','2026-04-08 05:05:52'),(12,'Kezang Dorji','20190112655@alms.local',NULL,NULL,'$2y$12$N.hnMT/A/Te5vpz/wHd.n.ffoRICSakFePVGBWwAua3e4luwJYzli',NULL,'2026-04-08 21:17:44','2026-04-08 21:17:44'),(13,'Tshering Wangchuk','200507295@alms.local',NULL,NULL,'$2y$12$7.B6CJhFgEs4iQY0XQkbM.ds.XuxgvXcqmCMsEC9Wiv5gHAXN1JwO',NULL,'2026-04-12 21:22:59','2026-04-12 21:22:59'),(14,'Dorji Wangmo Lhaki','9107014@alms.local',NULL,NULL,'$2y$12$I2HZ85ta4gEab74PtKfRaeBs7XM0j6zDeio.1M6mYcY1rCpPy8kcW',NULL,'2026-04-14 01:48:50','2026-04-14 01:48:50');
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

-- Dump completed on 2026-04-14 22:00:02
