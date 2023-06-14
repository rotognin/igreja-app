-- MySQL dump 10.13  Distrib 8.0.31, for Linux (x86_64)
--
-- Host: localhost    Database: sgc
-- ------------------------------------------------------
-- Server version	8.0.31-0ubuntu0.20.04.1

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
-- Table structure for table `acao`
--

DROP TABLE IF EXISTS `acao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acao` (
  `aca_acao` varchar(30) NOT NULL,
  `aca_descricao` varchar(100) NOT NULL,
  `aca_grupo` varchar(50) NOT NULL,
  PRIMARY KEY (`aca_acao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acao`
--

LOCK TABLES `acao` WRITE;
/*!40000 ALTER TABLE `acao` DISABLE KEYS */;
INSERT INTO `acao` VALUES ('acoes_acesso','Acesso a tela de gerenciamento de ações de usuário','SGC'),('empresas_acesso','Acesso a tela de gerenciamento de empresas','SGC'),('funil_cadastro_acesso','Acesso a tela de cadastro do funil de vendas (Leads)','COMERCIAL'),('funil_vendas_acesso','Acesso à tela de funil de vendas','COMERCIAL'),('menus_acesso','Acesso a tela de gerenciamento de menus','SGC'),('sgc01_acesso','Acesso ao relatório SGC01','SGC'),('staff_cadastro_acesso','Acesso ao cadastro de staff','COMERCIAL'),('tipo_evento_acesso','Acesso a tela de cadastro de tipos de evento','COMERCIAL'),('usuarios_acesso','Acesso a tela de gerenciamento de usuários','SGC');
/*!40000 ALTER TABLE `acao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `emp_codigo` varchar(20) NOT NULL,
  `emp_nome` varchar(100) NOT NULL,
  PRIMARY KEY (`emp_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES ('01','WIX Piracicaba'),('02','WIX São Paulo');
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funil_vendas`
--

DROP TABLE IF EXISTS `funil_vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funil_vendas` (
  `fvs_codigo` int NOT NULL AUTO_INCREMENT,
  `fvs_descricao` varchar(100) NOT NULL,
  `fvs_status` varchar(1) NOT NULL DEFAULT 'S',
  `fvs_sequencia` int NOT NULL,
  PRIMARY KEY (`fvs_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funil_vendas`
--

LOCK TABLES `funil_vendas` WRITE;
/*!40000 ALTER TABLE `funil_vendas` DISABLE KEYS */;
INSERT INTO `funil_vendas` VALUES (1,'Oportunidades','S',1),(2,'Orçamento Enviado','S',2),(3,'Degustação Agendada','S',3),(4,'Pós Degustação','S',4),(5,'Fechamento','S',5),(6,'Prospecção','S',6);
/*!40000 ALTER TABLE `funil_vendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead`
--

DROP TABLE IF EXISTS `lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead` (
  `lea_codigo` int NOT NULL AUTO_INCREMENT,
  `lea_descricao` varchar(200) NOT NULL,
  `lea_nome` varchar(200) NOT NULL,
  `usu_login_responsavel` varchar(32) NOT NULL,
  `lea_celular` varchar(100) NOT NULL,
  `lea_email` varchar(100) DEFAULT NULL,
  `tpe_evento` int DEFAULT NULL,
  `lea_qtd_convidado` int DEFAULT NULL,
  `lea_data_evento` datetime DEFAULT NULL,
  `lea_tempo_evento` int DEFAULT NULL,
  `lea_cidade` varchar(100) DEFAULT NULL,
  `fvs_codigo` int NOT NULL,
  `lea_status` varchar(1) NOT NULL,
  PRIMARY KEY (`lea_codigo`),
  KEY `lead_FK` (`usu_login_responsavel`),
  KEY `lead_FK_1` (`tpe_evento`),
  KEY `lead_FK_2` (`fvs_codigo`),
  CONSTRAINT `lead_FK` FOREIGN KEY (`usu_login_responsavel`) REFERENCES `usuario` (`usu_login`),
  CONSTRAINT `lead_FK_1` FOREIGN KEY (`tpe_evento`) REFERENCES `tipo_evento` (`tpe_codigo`),
  CONSTRAINT `lead_FK_2` FOREIGN KEY (`fvs_codigo`) REFERENCES `funil_vendas` (`fvs_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead`
--

LOCK TABLES `lead` WRITE;
/*!40000 ALTER TABLE `lead` DISABLE KEYS */;
INSERT INTO `lead` VALUES (2,'fernando e gabi','fernando fonseca','Funcoes','19996479489','fonseca.fernando@outlook.com',1,150,'0000-00-00 00:00:00',5,'Piracicaba',1,'A');
/*!40000 ALTER TABLE `lead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_programa`
--

DROP TABLE IF EXISTS `log_programa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_programa` (
  `log_codigo` bigint NOT NULL AUTO_INCREMENT,
  `prg_codigo` int NOT NULL,
  `log_datahora` datetime NOT NULL,
  `usu_login` varchar(32) NOT NULL,
  `log_ip` varchar(32) NOT NULL,
  `log_navegador` varchar(255) NOT NULL,
  PRIMARY KEY (`log_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_programa`
--

LOCK TABLES `log_programa` WRITE;
/*!40000 ALTER TABLE `log_programa` DISABLE KEYS */;
INSERT INTO `log_programa` VALUES (1,2,'2022-11-06 10:11:06','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(2,6,'2022-11-06 10:15:12','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(3,9,'2022-11-06 10:16:54','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(4,2,'2022-11-06 10:17:02','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(5,10,'2022-11-06 10:20:40','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(6,6,'2022-11-06 10:27:01','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(7,10,'2022-11-06 10:28:30','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(8,2,'2022-11-06 10:30:12','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(9,10,'2022-11-06 10:34:40','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(10,9,'2022-11-06 10:36:49','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(11,12,'2022-11-06 10:40:40','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(12,10,'2022-11-06 11:00:43','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(13,12,'2022-11-06 11:04:48','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(14,6,'2022-11-06 11:41:37','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(15,12,'2022-11-06 11:43:10','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(16,6,'2022-11-07 13:52:02','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(17,2,'2022-11-07 13:52:05','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(18,10,'2022-11-07 14:08:53','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(19,12,'2022-11-07 14:44:22','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(20,10,'2022-11-07 14:47:15','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36'),(21,12,'2022-11-07 14:47:18','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(22,10,'2022-11-07 14:57:57','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(23,12,'2022-11-07 14:57:59','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(24,10,'2022-11-07 15:35:10','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(25,9,'2022-11-07 15:35:12','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(26,2,'2022-11-07 15:35:13','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(27,6,'2022-11-07 15:35:14','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(28,12,'2022-11-07 15:46:46','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0'),(29,10,'2022-11-29 09:28:54','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(30,6,'2022-11-29 10:48:38','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(31,10,'2022-11-29 11:18:20','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(32,2,'2022-11-29 11:20:12','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(33,10,'2022-11-29 11:24:26','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(34,6,'2022-11-29 11:24:34','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(35,10,'2022-11-29 11:26:05','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(36,2,'2022-11-29 11:33:21','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(37,10,'2022-11-29 11:33:51','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(38,6,'2022-11-29 11:41:55','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(39,2,'2022-11-29 11:42:00','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(40,9,'2022-11-29 11:42:05','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(41,6,'2022-11-29 11:42:06','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(42,2,'2022-11-29 11:42:10','jonas.sala','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(43,2,'2022-11-29 17:17:42','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(44,6,'2022-11-29 17:18:36','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(45,10,'2022-11-29 17:20:31','jonas.sala','187.26.213.85','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(46,2,'2022-11-29 17:20:35','jonas.sala','187.26.213.85','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0'),(47,2,'2022-11-29 17:39:17','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(48,12,'2022-11-29 17:43:16','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(49,9,'2022-11-29 17:44:11','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(50,6,'2022-11-29 17:44:16','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(51,10,'2022-11-29 17:44:31','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(52,12,'2022-11-29 17:44:36','Funcoes','191.246.33.82','Mozilla/5.0 (Linux; Android 11; moto g(10)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Mobile Safari/537.36'),(53,10,'2022-11-29 23:01:04','deivid.borges','189.19.164.217','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36'),(54,12,'2022-11-29 23:01:11','deivid.borges','189.19.164.217','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36'),(55,10,'2022-12-14 17:43:30','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(56,12,'2022-12-14 17:44:00','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(57,9,'2022-12-14 17:44:11','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(58,6,'2022-12-14 17:44:24','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(59,10,'2022-12-14 17:44:25','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(60,12,'2022-12-14 17:44:32','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(61,10,'2022-12-14 17:44:54','Funcoes','168.121.52.218','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 Edg/108.0.1462.46'),(62,9,'2023-01-17 12:48:58','Funcoes','189.103.161.32','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'),(63,6,'2023-01-17 12:49:02','Funcoes','189.103.161.32','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'),(64,12,'2023-01-17 12:49:15','Funcoes','189.103.161.32','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36');
/*!40000 ALTER TABLE `log_programa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programa`
--

DROP TABLE IF EXISTS `programa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programa` (
  `prg_codigo` int NOT NULL AUTO_INCREMENT,
  `prg_sequencia` int NOT NULL,
  `prg_descricao` varchar(100) NOT NULL,
  `prg_url` varchar(100) NOT NULL,
  `prg_icone` varchar(100) DEFAULT NULL,
  `prg_codigo_pai` int DEFAULT NULL,
  `prg_lft` int NOT NULL,
  `prg_rgt` int NOT NULL,
  `prg_nivel` int NOT NULL,
  `prg_ativo` char(1) DEFAULT 'S',
  `aca_acao` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`prg_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programa`
--

LOCK TABLES `programa` WRITE;
/*!40000 ALTER TABLE `programa` DISABLE KEYS */;
INSERT INTO `programa` VALUES (1,10,'SGC','','fas fa-user-shield',0,13,28,0,'S',NULL),(2,10,'Usuários','/sgc/cadUsuario.php','fas fa-users',8,19,20,2,'S','usuarios_acesso'),(6,20,'Ações de usuário','/sgc/cadAcao.php','fas fa-shield-halved',8,21,22,2,'S','acoes_acesso'),(8,10,'Cadastros','','fas fa-box-archive',1,16,23,1,'S',''),(9,0,'Empresas','/sgc/cadEmpresa.php','far fa-building',8,17,18,2,'S','empresas_acesso'),(10,0,'Menus','/sgc/cadMenu.php','fas fa-bars',1,14,15,1,'S','menus_acesso'),(11,20,'Relatórios','','far fa-file-lines',1,24,27,1,'S',''),(12,10,'SGC01 - Acesso à programas','/sgc/relLogPrograma.php','fas fa-user-clock',11,25,26,2,'S','sgc01_acesso'),(13,0,'Comercial','','fas fa-user-tie',0,1,12,0,'S',''),(14,50,'Cadastros','','fas fa-box-archive',13,4,11,1,'S',''),(15,0,'Staff','/cadastro/cadStaff.php','fas fa-people-group',14,5,6,2,'S','staff_cadastro_acesso'),(16,20,'Funil de vendas','/cadastro/cadFunilVendas.php','fas fa-filter',14,7,8,2,'S','funil_cadastro_acesso'),(17,30,'Tipos de evento','/cadastro/cadTipoEvento.php','fas fa-calendar-plus',14,9,10,2,'S','tipo_evento_acesso'),(18,0,'Funil de vendas','/comercial/comLeadFunilVendas.php','fas fa-filter',13,2,3,1,'S','funil_vendas_acesso');
/*!40000 ALTER TABLE `programa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `stf_codigo` int NOT NULL AUTO_INCREMENT,
  `stf_descricao` varchar(100) NOT NULL,
  `stf_50` decimal(10,0) NOT NULL,
  `stf_100` decimal(10,0) NOT NULL,
  `stf_150` decimal(10,0) NOT NULL,
  `stf_200` decimal(10,0) NOT NULL,
  `stf_250` decimal(10,0) NOT NULL,
  `stf_300` decimal(10,0) NOT NULL,
  `stf_350` decimal(10,0) NOT NULL,
  `stf_400` decimal(10,0) NOT NULL,
  `stf_450` decimal(10,0) NOT NULL,
  `stf_500` decimal(10,0) NOT NULL,
  PRIMARY KEY (`stf_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,'Chefe de bar',1,1,1,1,1,1,1,1,1,1),(2,'Bartenders',1,2,3,4,4,4,5,6,6,7),(3,'Barback',0,1,1,1,1,1,2,2,2,2),(4,'Garçom',1,1,1,1,2,2,2,2,2,3),(5,'Copeiro',0,1,1,1,1,1,1,1,1,1);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_evento`
--

DROP TABLE IF EXISTS `tipo_evento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_evento` (
  `tpe_codigo` int NOT NULL AUTO_INCREMENT,
  `tpe_descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`tpe_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_evento`
--

LOCK TABLES `tipo_evento` WRITE;
/*!40000 ALTER TABLE `tipo_evento` DISABLE KEYS */;
INSERT INTO `tipo_evento` VALUES (1,'Casamento'),(2,'Aniversário'),(3,'Formatura'),(4,'Encontro Social');
/*!40000 ALTER TABLE `tipo_evento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `usu_login` varchar(32) NOT NULL,
  `usu_senha` varchar(255) DEFAULT NULL,
  `usu_nome` varchar(50) NOT NULL,
  `usu_email` varchar(50) DEFAULT NULL,
  `usu_ramal` varchar(10) DEFAULT NULL,
  `usu_celular` varchar(20) DEFAULT NULL,
  `usu_ativo` char(1) DEFAULT 'S',
  `usu_celular_whatsapp` char(1) DEFAULT NULL,
  PRIMARY KEY (`usu_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES ('Funcoes','$2y$10$xXA.m0l/Dlfiuc5/LVi6meUMghlgEpmZY.I50LDyOH.2ygVoRPhq6','Funcoes','suporte@Funcoes.com.br','','','S','N'),('deivid.borges','$2y$10$6q9sH5qx4X2D6TJHn8nKwegMwXI.LjkDn//ulyatC16JANdT48G3m','Deivid Borges ','wix@wixbartenders.com.br','','','S','N'),('jonas.sala','$2y$10$4TD.P/IyR6xQVbzEyQbOVudTVtHF6plcETVZUpGMtc3y.PqFknbSi','Jonas Sala Silva','jonas@Funcoes.com.br','777','(19) 99706-0612','S','S');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_acao`
--

DROP TABLE IF EXISTS `usuario_acao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_acao` (
  `usu_login` varchar(32) NOT NULL,
  `aca_acao` varchar(30) NOT NULL,
  PRIMARY KEY (`usu_login`,`aca_acao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_acao`
--

LOCK TABLES `usuario_acao` WRITE;
/*!40000 ALTER TABLE `usuario_acao` DISABLE KEYS */;
INSERT INTO `usuario_acao` VALUES ('Funcoes','acoes_acesso'),('Funcoes','empresas_acesso'),('Funcoes','funil_cadastro_acesso'),('Funcoes','funil_vendas_acesso'),('Funcoes','menu_gravar'),('Funcoes','menus_acesso'),('Funcoes','sgc01_acesso'),('Funcoes','staff_cadastro_acesso'),('Funcoes','tipo_evento_acesso'),('Funcoes','usuarios_acesso'),('deivid.borges','acoes_acesso'),('deivid.borges','empresas_acesso'),('deivid.borges','funil_cadastro_acesso'),('deivid.borges','funil_vendas_acesso'),('deivid.borges','menu_gravar'),('deivid.borges','menus_acesso'),('deivid.borges','sgc01_acesso'),('deivid.borges','staff_cadastro_acesso'),('deivid.borges','tipo_evento_acesso'),('deivid.borges','usuarios_acesso'),('jonas.sala','acoes_acesso'),('jonas.sala','empresas_acesso'),('jonas.sala','funil_cadastro_acesso'),('jonas.sala','funil_vendas_acesso'),('jonas.sala','menu_gravar'),('jonas.sala','menus_acesso'),('jonas.sala','sgc01_acesso'),('jonas.sala','staff_cadastro_acesso'),('jonas.sala','tipo_evento_acesso'),('jonas.sala','usuarios_acesso');
/*!40000 ALTER TABLE `usuario_acao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_empresa`
--

DROP TABLE IF EXISTS `usuario_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_empresa` (
  `usu_login` varchar(32) NOT NULL,
  `emp_codigo` varchar(10) NOT NULL,
  `emp_padrao` char(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_empresa`
--

LOCK TABLES `usuario_empresa` WRITE;
/*!40000 ALTER TABLE `usuario_empresa` DISABLE KEYS */;
INSERT INTO `usuario_empresa` VALUES ('Funcoes','01','S'),('jonas.sala','01','S'),('jonas.sala','02','N'),('Funcoes','02','N'),('deivid.borges','01','S'),('deivid.borges','02','N');
/*!40000 ALTER TABLE `usuario_empresa` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-01-17 12:49:23
