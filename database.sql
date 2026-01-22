-- MariaDB dump 10.19  Distrib 10.6.15-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: u709343455_d21dji21ji
-- ------------------------------------------------------
-- Server version	10.6.15-MariaDB-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admlogin`
--

DROP TABLE IF EXISTS `admlogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admlogin` (
                            `email` varchar(255) NOT NULL,
                            `senha` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admlogin`
--

/*!40000 ALTER TABLE `admlogin` DISABLE KEYS */;
/*!40000 ALTER TABLE `admlogin` ENABLE KEYS */;

--
-- Table structure for table `app`
--

DROP TABLE IF EXISTS `app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app` (
                       `ID` int(11) NOT NULL AUTO_INCREMENT,
                       `token` varchar(255) NOT NULL,
                       `depositos` varchar(255) NOT NULL,
                       `saques` varchar(255) NOT NULL,
                       `usuarios` varchar(255) NOT NULL,
                       `xmeta` int(11) NOT NULL DEFAULT 10,
                       `faturamento_total` float NOT NULL DEFAULT 0,
                       `cadastros` varchar(255) NOT NULL,
                       `saques_valor` float NOT NULL DEFAULT 0,
                       `deposito_min` float NOT NULL DEFAULT 0,
                       `saques_min` float NOT NULL DEFAULT 0,
                       `aposta_max` float NOT NULL DEFAULT 0,
                       `dificuldade_jogo` varchar(255) NOT NULL,
                       `aposta_min` float NOT NULL DEFAULT 0,
                       `rollover_saque` varchar(255) NOT NULL,
                       `taxa_saque` float NOT NULL DEFAULT 0,
                       `google_ads_tag` varchar(255) DEFAULT NULL,
                       `facebook_ads_tag` varchar(255) DEFAULT NULL,
                       `cpa` float DEFAULT 0,
                       `deposito_min_cpa` float DEFAULT 0,
                       `revenue_share_falso` varchar(255) DEFAULT NULL,
                       `max_saque_cpa` float DEFAULT 0,
                       `max_por_saque_cpa` float DEFAULT 0,
                       `revenue_share` float DEFAULT 0,
                       `chance_afiliado` float DEFAULT 0,
                       `telegram_link` varchar(255) NOT NULL,
                       `saques_max` float NOT NULL DEFAULT 0,
                       `min_por_saque_cpa` float NOT NULL DEFAULT 0,
                       `coin_value` float DEFAULT 0,
                       PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app`
--

/*!40000 ALTER TABLE `app` DISABLE KEYS */;
INSERT INTO `app` VALUES (1,'','100','','',10,0,'',0,10,100,10,'facil',1,'3000',10,'','',20,25,'75',10000,1000,0,0,'',1000,100,0.01);
/*!40000 ALTER TABLE `app` ENABLE KEYS */;

--
-- Table structure for table `appconfig`
--

DROP TABLE IF EXISTS `appconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appconfig` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `is_adm` int(11) NOT NULL DEFAULT 0,
                             `nome` varchar(255) DEFAULT NULL,
                             `email` varchar(255) NOT NULL,
                             `senha` varchar(255) NOT NULL,
                             `cpf` varchar(255) DEFAULT NULL,
                             `telefone` varchar(255) NOT NULL,
                             `saldo` float NOT NULL DEFAULT 0,
                             `linkafiliado` varchar(255) DEFAULT '',
                             `depositou` float NOT NULL DEFAULT 0,
                             `lead_aff` int(11) DEFAULT 0,
                             `leads_ativos` int(11) NOT NULL DEFAULT 0,
                             `rollover` float NOT NULL DEFAULT 0,
                             `plano` int(11) NOT NULL DEFAULT 20,
                             `bloc` int(11) NOT NULL DEFAULT 0,
                             `sacou` float NOT NULL DEFAULT 0,
                             `indicados` int(11) NOT NULL DEFAULT 0,
                             `saldo_comissao` float NOT NULL DEFAULT 0,
                             `percas` float NOT NULL DEFAULT 0,
                             `ganhos` float NOT NULL DEFAULT 0,
                             `cpa` float NOT NULL DEFAULT 0,
                             `cpafake` float NOT NULL DEFAULT 0,
                             `jogo_demo` int(11) NOT NULL DEFAULT 2,
                             `comissaofake` float NOT NULL DEFAULT 0,
                             `saldo_cpa` float DEFAULT 0,
                             `saldo_rev` float NOT NULL DEFAULT 0,
                             `primeiro_deposito` float DEFAULT 0,
                             `status_primeiro_deposito` int(11) DEFAULT 0,
                             `data_cadastro` timestamp NULL DEFAULT current_timestamp(),
                             `afiliado` int(11) DEFAULT 0,
                             `cont_cpa` float NOT NULL DEFAULT 0,
                             `sacou_saldo` float NOT NULL DEFAULT 0,
                             `pix_gerado` int(11) NOT NULL DEFAULT 0,
                             `revenue_share` int(11) NOT NULL DEFAULT 0,
                             `rollover_total` float DEFAULT 0,
                             `bonus` float DEFAULT 0,
                             `jogou_fake` int(11) DEFAULT 0,
                             `origem` varchar(100) DEFAULT NULL,
                             `dificuldade` varchar(255) DEFAULT 'nenhuma',
                             `saldo_fake` int(11) NOT NULL DEFAULT 0,
                             `xmeta_ind` int(11) NOT NULL DEFAULT 0,
                             `coin_ind` float NOT NULL DEFAULT 0,
                             PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appconfig`
--

/*!40000 ALTER TABLE `appconfig` DISABLE KEYS */;
INSERT INTO `appconfig` VALUES (1,1,NULL,'edito.desenvolvedor@gmail.com','$2y$10$ZLTen5JKnOYUBckIRGcBjuNpzmkkklbfuTfl9QSdsAt82oUWgdolC',NULL,'(64) 99266-2244',0,'https://innovationsbet.com/cadastrar/?aff=1',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-17 19:23:18',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(2,0,NULL,'Lucimarnunes722@gmail.com','$2y$10$KZKS4ifAPycrAzZw6Be0eOeCRy3FiD6id65YcBzK595FU7DSmINNu',NULL,'(64) 99617-6886',0,'https://innovationsbet.com/cadastrar/?aff=2',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-17 19:52:30',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(3,0,NULL,'pollymartins2184@gmail.com','$2y$10$vkoOkO94yADn0UkskCeyEuLlw83l6WOORUXjSMAbwP34bAPTHkdxm',NULL,'(64) 99236-6044',57,'https://innovationsbet.com/cadastrar/?aff=3',25,0,0,732,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,'2024-01-17 19:59:56',0,0,0,0,0,750,0,0,NULL,'nenhuma',0,0,0),(4,0,NULL,'Encapsuladorede05@gmail.com','$2y$10$jkdfFnvrErfxGzfRqvxZuuTonw9jErn2RCI.0q7nld8D7q5WmzAgm',NULL,'(64) 99214-7849',0,'https://innovationsbet.com/cadastrar/?aff=4',25,0,0,725,0,0,0,0,0,25,0,0,0,0,0,0,0,0,0,'2024-01-17 22:49:58',0,0,0,0,0,750,0,0,NULL,'nenhuma',0,0,0),(5,0,NULL,'Rodriguesjojo1705@gmail.com','$2y$10$pZVY3h9EqDXOxBbVzNH7wu3Lf6liqoFE3gVlLiywpht4zNWNp7oqe',NULL,'(42) 99842-2694',0,'https://innovationsbet.com/cadastrar/?aff=5',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-19 22:21:05',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(6,0,NULL,'ziul_marques@outlook.it','$2y$10$20mKk1RYmCCus45bhUd88Ocu3bu0HfcRfonGy0e0aTMJvNfMpz3Sm',NULL,'(81) 98467-3368',0,'https://innovationsbet.com/cadastrar/?aff=6',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-19 23:42:57',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(7,0,NULL,'sddsdsa@gmail.com','$2y$10$1Xkgjzh4vrp2u/rp1AyU7eTae8HFCZcsAxhEwNho8nwqXTSt5s1Ye',NULL,'(64) 99288-2233',0,'https://innovationsbet.com/cadastrar/?aff=7',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-20 03:15:52',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(8,0,NULL,'tayllana40@gmail.com','$2y$10$iXX1f9ZCWQI8ar8F.7NCeekUmfpZteqaL9m8VTOviK6mPSeMkSeku',NULL,'(21) 99204-7363',0,'https://innovationsbet.com/cadastrar/?aff=8',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-20 13:58:41',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(9,0,NULL,'diojsjsskjsjsjdhdhs@gmai221l.com wwq','$2y$10$2Kl9ZgwOo6Kp/sHGfk4EReGiA8mcXSkKIIpXhl4.ydd428D.qyyxe',NULL,'(99) 94707-2115',0,'https://innovationsbet.com/cadastrar/?aff=9',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-20 14:09:10',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(10,0,NULL,'Daviluisdearaújomendes@gmail.com.br','$2y$10$aPwpDQsEolqwokU9bm1YB.ANWEoAJIspwE5zvHl/7/sHE0HJmpWxC',NULL,'(99) 35844-5234',0,'https://innovationsbet.com/cadastrar/?aff=10',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-20 18:16:47',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(11,0,NULL,'diojsjsskjsjsjdhdhs@gmaiaeea.com qqaa','$2y$10$bP2Ed2xEl8O.y5CPoRXpheOTMaYl3EblbHJkRU4QHPG7wI.c9np8.',NULL,'(99) 94707-2115',0,'https://innovationsbet.com/cadastrar/?aff=11',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-20 21:33:43',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(12,0,NULL,'lucianodias342@gmail.com','$2y$10$DZ7iG2aHzzQUB4OfDHap6uVNIA2SLwXCXLZNS6fBKW2r2X5H0Dexa',NULL,'(27) 99793-3092',0,'https://innovationsbet.com/cadastrar/?aff=12',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-21 00:13:39',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(13,0,NULL,'r.rafasouza13@gmail.com','$2y$10$tdJVztSjkgvAGOvuzMw9FOGfvppScmGGqx0r0hPUoCQvzEReaj9yq',NULL,'(11) 98316-2397',0,'https://innovationsbet.com/cadastrar/?aff=13',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-21 15:29:25',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(14,0,NULL,'ccirilo192@gmail.com','$2y$10$YXFH.zTcfKBRsgcmc6wkrOVUyxIIEv0Of/EKsKpq7bDX1saOtWcQu',NULL,'(21) 96878-3716',0,'https://innovationsbet.com/cadastrar/?aff=14',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-21 21:43:42',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(15,0,NULL,'alissonebs@gmail.com','$2y$10$3yaG./NiMJIWY4vVQrFNEeNrkPkzrWklS2Yw.QtbAUhaMqQSNtYaq',NULL,'(86) 99468-4370',0,'https://innovationsbet.com/cadastrar/?aff=15',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-22 17:44:55',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(16,0,NULL,'emillykarczmarski@gmail.com','$2y$10$8sPidwprWqwFt6G9hVicwOpkKHss09oj3Ha.UytAPAnyb5Y5mkaA6',NULL,'(66) 99696-8323',0,'https://innovationsbet.com/cadastrar/?aff=16',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 00:46:16',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(17,0,NULL,'kellycris0899@gmail.com','$2y$10$Wpa67YV2jAu3eYnHz6rPXe8JO4.ZUtIB4m2AemsE7AZ7GTCCcSu86',NULL,'(62) 99502-5692',0,'https://innovationsbet.com/cadastrar/?aff=17',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-23 00:46:23',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(18,0,NULL,'marinasebastiany2018@gmail.com','$2y$10$lrG7XuveY6HFMz5iC7dyfuI.rHNxbLv77MfYqPORwOQlzt3oKkY1q',NULL,'(62) 99246-8813',0,'https://innovationsbet.com/cadastrar/?aff=18',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 00:47:15',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(19,0,NULL,'alvesgarciasantosanaluiza18@gmail.com','$2y$10$hX2vRRJI9awusLlj7CCD2.51mAdRYvDZj1xCzFQifBIes1ih592vi',NULL,'(62) 99559-8343',0,'https://innovationsbet.com/cadastrar/?aff=19',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 00:47:48',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(20,0,NULL,'Uebester.filho@ba.estudante.senai.br','$2y$10$sSDHhJfuIDWADe3eP7c1X.VxSpXHB6xa3dBpNyJ1eN2AlyzQPkuoy',NULL,'(71) 98392-1695',0,'https://innovationsbet.com/cadastrar/?aff=20',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 00:48:11',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(21,0,NULL,'gabigoltavares5535@gmail.com','$2y$10$6nVgt88fxY2CIc0ffIcjsusoQVdvHGinT/wOftwQtrtAGczgxN7Se',NULL,'(85) 99435-7214',0,'https://innovationsbet.com/cadastrar/?aff=21',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 00:48:21',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(22,0,NULL,'Rodrigoalve999@gmail.com ','$2y$10$AUFOoXS7qKeN43jMEYaiH.fw0.rM.r8On8QumP/w3pHL4ZGj1lZnG',NULL,'(62) 99404-9025',0,'https://innovationsbet.com/cadastrar/?aff=22',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 00:48:47',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(23,0,NULL,'luizhenriquesouza0110@gmail.com','$2y$10$EtcpmXiLAP9SsdBadVqx/e8Bq295lv3/5KioFLejtqAfcF/NAsTS.',NULL,'(77) 99950-6717',0,'https://innovationsbet.com/cadastrar/?aff=23',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 01:08:09',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(24,0,NULL,'ailtonmachado926@gmail.com','$2y$10$suhr46op5UJ8/AhxwVOKBOTJXV6JIEZSeXMl4RF3m4FvkWSdr.4WC',NULL,'(62) 98230-4967',0,'https://innovationsbet.com/cadastrar/?aff=24',25,0,0,675,0,0,0,0,0,75,0,0,0,0,0,0,0,0,0,'2024-01-23 01:09:13',0,0,0,0,0,750,0,0,NULL,'nenhuma',0,0,0),(25,0,NULL,'JULIOSILVADESOUZA82@GMAIL.COM','$2y$10$lNNqkBW3jXV5hU0M6O.Pj.EYUPde9jlBBW31ammcFYwmb37ceCbsK',NULL,'(62) 99483-1790',0,'https://innovationsbet.com/cadastrar/?aff=25',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 01:15:16',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(26,0,NULL,'Ferreirarosa965@gmail.com','$2y$10$uwzNbW1o7aRK6dfnA1HcluzcgvmxfqHkEr0yZ9jPO4yfZ/pgJxnjy',NULL,'(62) 99479-4876',0,'https://innovationsbet.com/cadastrar/?aff=26',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-23 01:15:59',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(27,0,NULL,'igormarcelo606@gmail.com','$2y$10$c8YAzEvPXMUcGmcVe0BbCuQob11963lB8DKk9uNkwx2ytG7HTWc0C',NULL,'(21) 99046-5738',0,'https://innovationsbet.com/cadastrar/?aff=27',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 01:16:19',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(28,0,NULL,'Pb816929@gmail.com','$2y$10$qUoHmJq3WL6xTMzOOYNN6.uJ2BV0lLC2ZNmaz1X40AYQRNrjIILRO',NULL,'(88) 93239-7275',0,'https://innovationsbet.com/cadastrar/?aff=28',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 01:38:10',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(29,0,NULL,'dguimaraesdamata@live.com','$2y$10$4.cHqE1kwVWUsacQryyVYe1uJoaxR73UANNFYeT4aqBw7J5G/CV8C',NULL,'(62) 99302-5083',0,'https://innovationsbet.com/cadastrar/?aff=29',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 01:54:25',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(30,0,NULL,'ariangomes201620@gmail.com','$2y$10$4iodq0KRd5FSgMA3PeJnTe..WOdt1VBd89WF6bdNNuevtrEUQ35dy',NULL,'(11) 98241-1940',0,'https://innovationsbet.com/cadastrar/?aff=30',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 02:51:49',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(31,0,NULL,'marinasebastiany1@gmail.com','$2y$10$qREnquMsfid6PBJTPNIBZO19Qmmd5utObz3cqoXT59x.q6vWCoNKy',NULL,'(62) 99246-8813',0,'https://innovationsbet.com/cadastrar/?aff=31',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 02:58:58',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(32,0,NULL,'thaynogueira02@gmail.com','$2y$10$rc9x62f.R/9b9zuLvs74iOKEBxRdHOFGDFFfJQgZn1WeLj6LkO8C.',NULL,'(62) 99434-5687',0,'https://innovationsbet.com/cadastrar/?aff=32',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 03:00:22',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(33,0,NULL,'teffmonique5@gmail.com','$2y$10$.lf/QZsNHibIxVBPfIt5W.1ENZl5205R3sgkZs3ITRatiST4Eow2e',NULL,'(66) 99979-3384',0,'https://innovationsbet.com/cadastrar/?aff=33',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-23 03:07:56',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(34,0,NULL,'thamillypds.1@gmail.com','$2y$10$PIS0DgGYqlWbLK1z5XoHiOkCyjX4qX/plxCIec7WkL/ByfNE0ITBC',NULL,'(66) 99996-3462',0,'https://innovationsbet.com/cadastrar/?aff=34',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 03:14:47',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(35,0,NULL,'Wr3345715@Gmail.Com','$2y$10$z13E8oMAmTho1zXVGT6XJ.MPXs/NbJACUvqve7G5MboKpIY1rjIVy',NULL,'(62) 99258-4107',0,'https://innovationsbet.com/cadastrar/?aff=35',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 03:23:23',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(36,0,NULL,'delm.candida@gmail.com ','$2y$10$nkya5X25b4cnScAXt8eCsO16bmID9v4fYaJ9OxdohFpiqm7IuBj8q',NULL,'(62) 99315-0104',0,'https://innovationsbet.com/cadastrar/?aff=36',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 03:28:16',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(37,0,NULL,'pekenahsarahcristina1999@gmail.com','$2y$10$fJQqK29s4GOvlK/lqpo3Bu67KZcPKIIXkNPizJncBx2Z/HEGL3qFy',NULL,'(62) 99357-9304',0,'https://innovationsbet.com/cadastrar/?aff=37',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 03:45:48',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(38,0,NULL,'dssantoslima@gmail.com','$2y$10$CFpu447hzNSnAf.ra/lcb.Dm6p1Fgfjunrj2KaFx5im5NJ4yaMPf2',NULL,'(41) 98717-3822',0,'https://innovationsbet.com/cadastrar/?aff=38',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 08:27:28',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(39,0,NULL,'igoreduardodemoraesoliveira@gmail.com','$2y$10$zd9Xqs1CeKu58H1FJNh7k.x0xAIxByOk5RWdbHIvTgR9bN853kD9O',NULL,'(62) 99321-3410',0,'https://innovationsbet.com/cadastrar/?aff=39',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 09:18:28',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(40,0,NULL,'jefferssonjunio8@gmail.com','$2y$10$QFHL7neOi1saddH6BciTX.eme/ihqljm9yia8G/OcHJ84vL.gYfSW',NULL,'(31) 99664-5575',50,'https://innovationsbet.com/cadastrar/?aff=40',25,0,0,725,0,0,0,0,0,25,0,0,0,0,0,0,0,0,0,'2024-01-23 10:10:48',0,0,0,0,0,750,0,0,NULL,'nenhuma',0,0,0),(41,0,NULL,'Kellyjacqueline209@gmail.com','$2y$10$fUSCMeeXQyfSMSXLRH4fGOs5Yskjuri/6zAZIjk4Rd.r/slZJPpaa',NULL,'(62) 99444-3242',0,'https://innovationsbet.com/cadastrar/?aff=41',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 10:37:05',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(42,0,NULL,'irisvaniacastro407@gmail.com','$2y$10$WmuMv33FVl/xGB7tti09VOXe/QQemT/eudCVtqutabl6PKHo5qgIu',NULL,'(62) 99409-7939',0,'https://innovationsbet.com/cadastrar/?aff=42',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 10:48:59',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(43,0,NULL,'991747103.joelma@gmail.com','$2y$10$w1WUZU6go.5/AEtC9HA99utZ.ZF14M.0nt01walN3jq4/gsrVuCDm',NULL,'(62) 99551-9249',0,'https://innovationsbet.com/cadastrar/?aff=43',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 11:22:19',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(44,0,NULL,'angelagomesnla@gmail.com ','$2y$10$K903rOq8VBKRzFcIal4PNeZ07zu.EAuKEe/1J58LlUkfd7j36JPcu',NULL,'(66) 99923-6270',0,'https://innovationsbet.com/cadastrar/?aff=44',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 11:36:24',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(45,0,NULL,'biankasantos2010@hotmail.com','$2y$10$kUBXlQwiAEgtmPpINclrbOMX4CllGY7R4so9LgwgCoh4kXVNV2EH2',NULL,'(11) 94684-0595',0,'https://innovationsbet.com/cadastrar/?aff=45',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-23 12:29:26',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(46,0,NULL,'Wwwvanessakotikoski393@gmail.com ','$2y$10$v0QEo59GoapJ.Mion/5.B.aTRU48j9caOD.ttbwWrJIXwY4Djc0h6',NULL,'(66) 99977-8985',0,'https://innovationsbet.com/cadastrar/?aff=46',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-23 13:51:32',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(47,0,NULL,'rauliane53@gmail.com','$2y$10$G0ssoTYeF77iwQUglDq93ul0y1kFE/IOHNWupEGOvZlvqzY84u0Ka',NULL,'(62) 99369-9006',0,'https://innovationsbet.com/cadastrar/?aff=47',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 13:59:54',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(48,0,NULL,'Paulaaparecidaxaviersilva@gmail.com ','$2y$10$FD71DXvaL5ptG0AWKtozTOF2sadHfNnt2kBvhy8yKnCtMJIkpLFW6',NULL,'(62) 99129-4754',0,'https://innovationsbet.com/cadastrar/?aff=48',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 14:00:19',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(49,0,NULL,'keyladourado00@gmail.com','$2y$10$vJ/DQq8Nv3Eegio73caT3urnUvt0YWf9YN9PqhXmrmICcoemfS.zu',NULL,'(62) 98520-1333',0,'https://innovationsbet.com/cadastrar/?aff=49',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 14:03:05',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(50,0,NULL,'Dhienifertavares30@gmail.com','$2y$10$na5qYRaROU/14t8gszQXmOZ8zf.KKzODBjFoCnqNiM.uh3FN0EWR.',NULL,'(66) 99677-3580',0,'https://innovationsbet.com/cadastrar/?aff=50',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 14:13:51',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(51,0,NULL,'Radaison@080gmail.com','$2y$10$5rcAABhYAibKj4K7MMVYWe07qjSxUzdQS9G.5bdrIWR6Tr9IBSzbe',NULL,'(62) 99547-3068',0,'https://innovationsbet.com/cadastrar/?aff=51',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 14:26:33',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(52,0,NULL,'Teff4673','$2y$10$.R.LuTibY4D1Ce7WjU1Jq.g53KiTSXImQ2adANu.jcXiy6pSLD5uq',NULL,'(66) 99627-1386',0,'https://innovationsbet.com/cadastrar/?aff=52',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-23 14:44:22',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(53,0,NULL,'Thaynnarafellipe05@gmail.com','$2y$10$lyTleMO7b6GnvUH8Yzfc9Oq3XgxB2fyE/dw5/mWIZhBjuN9hgb89.',NULL,'(62) 99107-2709',0,'https://innovationsbet.com/cadastrar/?aff=53',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 14:45:06',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(54,0,NULL,'francyolliver1993@gmail.com','$2y$10$G5.wVR9IvFxauSHMUVWQz.0wDOWVTbk9ouFtjxxUoioIbWRbuUryu',NULL,'(66) 99656-9296',0,'https://innovationsbet.com/cadastrar/?aff=54',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'2024-01-23 14:46:39',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(55,0,NULL,'kodxd015@gmail.com','$2y$10$sfaP.9LvEYhOb.6lu5rVAuEtlD1G/huqiGrI9KzX9flFPTGIOvywq',NULL,'(62) 99364-0918',0,'https://innovationsbet.com/cadastrar/?aff=55',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-24 12:25:59',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0),(56,0,NULL,'antonioleitefernandes5@gmail.com','$2y$10$WnB.kvofLZDwOjTRF/8P6ORJUMi5aj5yvgeoUOKpzH8GnhTSP1Q1i',NULL,'(64) 98414-3033',0,'https://innovationsbet.com/cadastrar/?aff=56',0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,'2024-01-25 12:10:15',0,0,0,0,0,0,0,0,NULL,'nenhuma',0,0,0);
/*!40000 ALTER TABLE `appconfig` ENABLE KEYS */;

--
-- Table structure for table `bonus`
--

DROP TABLE IF EXISTS `bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bonus` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `deposito` float NOT NULL DEFAULT 0,
                         `ganho` float NOT NULL DEFAULT 0,
                         PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bonus`
--

/*!40000 ALTER TABLE `bonus` DISABLE KEYS */;
INSERT INTO `bonus` VALUES (1,25,50),(2,40,80),(3,100,200);
/*!40000 ALTER TABLE `bonus` ENABLE KEYS */;

--
-- Table structure for table `confirmar_deposito`
--

DROP TABLE IF EXISTS `confirmar_deposito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confirmar_deposito` (
                                      `ID` int(11) NOT NULL AUTO_INCREMENT,
                                      `email` varchar(255) NOT NULL,
                                      `externalreference` varchar(255) NOT NULL,
                                      `valor` float NOT NULL DEFAULT 0,
                                      `status` varchar(255) NOT NULL DEFAULT 'WAITING_FOR_APPROVAL',
                                      `data` timestamp NULL DEFAULT current_timestamp(),
                                      `bonus` float NOT NULL DEFAULT 0,
                                      PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confirmar_deposito`
--

/*!40000 ALTER TABLE `confirmar_deposito` DISABLE KEYS */;
INSERT INTO `confirmar_deposito` VALUES (1,'encapsuladorede05@gmail.com','54343247-2846-4e8e-9416-c9c0aeff3c41',25,'WAITING_FOR_APPROVAL','2024-01-18 01:39:25',0),(2,'encapsuladorede05@gmail.com','4b24b323-504b-43a6-86a5-5380c6988a8e',25,'WAITING_FOR_APPROVAL','2024-01-18 01:42:48',0),(3,'encapsuladorede05@gmail.com','6e02d2c9-b822-4bcf-a84a-6f327672a5d9',25,'PAID_OUT','2024-01-18 01:42:50',0),(4,'pollymartins2184@gmail.com','2a374cb5-ea24-4c5c-8d34-7a59684197ce',25,'PAID_OUT','2024-01-18 20:23:12',50),(5,'jefferssonjunio8@gmail.com','05f1d4eb-8555-470c-93c0-0c6e82b9447f',25,'WAITING_FOR_APPROVAL','2024-01-23 10:14:47',50),(6,'jefferssonjunio8@gmail.com','cca63945-90c3-4883-9dfe-f535ae0ec4a8',25,'PAID_OUT','2024-01-23 10:17:30',50),(7,'ailtonmachado926@gmail.com','edb15998-3962-4d58-b852-40967ca64821',25,'PAID_OUT','2024-01-25 00:45:06',50);
/*!40000 ALTER TABLE `confirmar_deposito` ENABLE KEYS */;

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game` (
                        `email` varchar(255) NOT NULL,
                        `bet` float NOT NULL DEFAULT 0,
                        `token` varchar(255) NOT NULL,
                        `started` int(11) NOT NULL DEFAULT 0,
                        `expira_em` timestamp NOT NULL DEFAULT current_timestamp(),
                        `fake_bet` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

/*!40000 ALTER TABLE `game` DISABLE KEYS */;
INSERT INTO `game` VALUES ('pollymartins2184@gmail.com',5,'953cf9abf535a1edb0dd4cecde03efb2',1,'2024-01-18 20:27:18',0),('pollymartins2184@gmail.com',10,'a5a5d508b16af0c5a9b88dec99230ecf',1,'2024-01-18 20:26:27',0),('pollymartins2184@gmail.com',1,'481f01d0204e5163e030960f8c46e373',1,'2024-01-18 20:44:23',0),('pollymartins2184@gmail.com',1,'970c00b4827bfcc93d45ef12f4c0c7b2',1,'2024-01-18 21:04:34',0);
/*!40000 ALTER TABLE `game` ENABLE KEYS */;

--
-- Table structure for table `gateway`
--

DROP TABLE IF EXISTS `gateway`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gateway` (
                           `client_id` varchar(255) DEFAULT NULL,
                           `client_secret` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateway`
--

/*!40000 ALTER TABLE `gateway` DISABLE KEYS */;
INSERT INTO `gateway` VALUES ('eduardonunesbr_1701971981418','c835d6f9e82380b26bd2d6f4d72c518d8ac2c0c6351c411e674bb18f60216541a3118212399f437882c5b9d3f89a73bb');
/*!40000 ALTER TABLE `gateway` ENABLE KEYS */;

--
-- Table structure for table `ggr`
--

DROP TABLE IF EXISTS `ggr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ggr` (
                       `ID` int(11) NOT NULL AUTO_INCREMENT,
                       `token` varchar(255) NOT NULL,
                       `ggr_taxa` float NOT NULL DEFAULT 0,
                       `data` timestamp NOT NULL DEFAULT current_timestamp(),
                       `situacao` varchar(255) NOT NULL,
                       `total_ganhos` float NOT NULL DEFAULT 0,
                       `percas_24h` float NOT NULL DEFAULT 0,
                       `percas_1m` float NOT NULL DEFAULT 0,
                       `total_percas` float NOT NULL DEFAULT 0,
                       `ggr_24h` float NOT NULL DEFAULT 0,
                       `ggr_1m` float NOT NULL DEFAULT 0,
                       `credito_ggr` float NOT NULL DEFAULT 0,
                       `debito_ggr` float NOT NULL DEFAULT 0,
                       `ggr_pago` float NOT NULL DEFAULT 0,
                       `status_ggr` varchar(255) NOT NULL,
                       `ggr_total` float NOT NULL DEFAULT 0,
                       `saldo_inserido` float NOT NULL DEFAULT 0,
                       `senha` varchar(255) NOT NULL,
                       PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ggr`
--

/*!40000 ALTER TABLE `ggr` DISABLE KEYS */;
INSERT INTO `ggr` VALUES (1,'',10,'2024-01-03 03:27:52','',0,0,0,126,0,0,0,10,0,'IRREGULAR',10,0,'');
/*!40000 ALTER TABLE `ggr` ENABLE KEYS */;

--
-- Table structure for table `ggr_deposito`
--

DROP TABLE IF EXISTS `ggr_deposito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ggr_deposito` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `email` varchar(255) DEFAULT NULL,
                                `valor` int(11) DEFAULT NULL,
                                `externalreference` varchar(255) DEFAULT NULL,
                                `status` varchar(255) DEFAULT NULL,
                                `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ggr_deposito`
--

/*!40000 ALTER TABLE `ggr_deposito` DISABLE KEYS */;
/*!40000 ALTER TABLE `ggr_deposito` ENABLE KEYS */;

--
-- Table structure for table `origem_src`
--

DROP TABLE IF EXISTS `origem_src`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `origem_src` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `origem` varchar(100) DEFAULT NULL,
                              `quantidade` int(11) DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `origem_src`
--

/*!40000 ALTER TABLE `origem_src` DISABLE KEYS */;
/*!40000 ALTER TABLE `origem_src` ENABLE KEYS */;

--
-- Table structure for table `perca`
--

DROP TABLE IF EXISTS `perca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perca` (
                         `token` varchar(255) NOT NULL,
                         `bet` float NOT NULL,
                         `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                         `email` varchar(255) NOT NULL,
                         `accumulated` float NOT NULL DEFAULT 0,
                         PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perca`
--

/*!40000 ALTER TABLE `perca` DISABLE KEYS */;
INSERT INTO `perca` VALUES ('0b674aa5eca032a71a920946128bcc61',1,'2024-01-18 23:25:00','pollymartins2184@gmail.com',0),('16b09ef641c12d6e5ff137b27979ab38',10,'2024-01-25 00:56:46','ailtonmachado926@gmail.com',6.1),('17a5e15abf11b622051b2d535d4501f0',5,'2024-01-23 12:47:58','jefferssonjunio8@gmail.com',5.85),('23862a015c074e659a5bc96b3f5eb0f1',10,'2024-01-25 00:48:04','ailtonmachado926@gmail.com',34.6),('2fcdfd8be5bbe9404252285361cbbe7b',5,'2024-01-18 02:28:25','encapsuladorede05@gmail.com',0.25),('463cb62a1ff6af9ddc7d8ec2d9f4b5b6',5,'2024-01-25 00:54:57','ailtonmachado926@gmail.com',6.25),('4688e0f6b54f1ec686f5d17eb2da3419',10,'2024-01-18 02:27:37','encapsuladorede05@gmail.com',3.6),('481f01d0204e5163e030960f8c46e373',1,'2024-01-18 20:44:23','pollymartins2184@gmail.com',0),('76c741485a08d32eb37a2b1cac99e2ff',10,'2024-01-18 01:44:14','encapsuladorede05@gmail.com',3.8),('7f3aec90db72df2bd1928feebfb4d652',5,'2024-01-23 15:45:06','jefferssonjunio8@gmail.com',4.9),('9123b0909ca116431d4a666d2cdf8973',4,'2024-01-23 15:46:21','jefferssonjunio8@gmail.com',1.72),('953cf9abf535a1edb0dd4cecde03efb2',5,'2024-01-18 20:27:18','pollymartins2184@gmail.com',0),('970c00b4827bfcc93d45ef12f4c0c7b2',1,'2024-01-18 21:04:34','pollymartins2184@gmail.com',0),('979da61a3d2a22907013719b37182ca3',5,'2024-01-25 00:53:52','ailtonmachado926@gmail.com',0.1),('9c5590adbc00b873b4b94d1c1c0441d7',10,'2024-01-25 00:57:49','ailtonmachado926@gmail.com',2.6),('a36b438ce2e3f0b9a2ba69d121991755',10,'2024-01-25 00:50:32','ailtonmachado926@gmail.com',9.1),('a5a5d508b16af0c5a9b88dec99230ecf',10,'2024-01-18 20:26:27','pollymartins2184@gmail.com',0),('a9f2ef377722b2eda612ef3d63825539',1,'2024-01-23 10:20:12','jefferssonjunio8@gmail.com',2.23),('af5d104a2dce491f03ac797a528a2789',10,'2024-01-23 10:19:14','jefferssonjunio8@gmail.com',1.2),('c8a915baf16afb01883246d1c86453c5',1,'2024-01-25 00:51:37','ailtonmachado926@gmail.com',3.82),('ea1d27f97abe18d6eb1e4d3c80fa3339',10,'2024-01-25 00:46:10','ailtonmachado926@gmail.com',10.9),('ed569d5cc26c6baaa7fc3117a652918f',9,'2024-01-25 00:58:46','ailtonmachado926@gmail.com',22.41),('f986e71874162a9253cf4931e9855636',5,'2024-01-25 00:54:12','ailtonmachado926@gmail.com',1.95);
/*!40000 ALTER TABLE `perca` ENABLE KEYS */;

--
-- Table structure for table `pixels`
--

DROP TABLE IF EXISTS `pixels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pixels` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `nome` varchar(255) NOT NULL,
                          `script` varchar(255) NOT NULL,
                          `local` varchar(255) NOT NULL,
                          `pagina` varchar(255) NOT NULL,
                          PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pixels`
--

/*!40000 ALTER TABLE `pixels` DISABLE KEYS */;
INSERT INTO `pixels` VALUES (1,'FACEBOOK ADS','d61b874c33fc0ea9d8a33ff4ac9361b5.js','body','todo-sem-jogo');
/*!40000 ALTER TABLE `pixels` ENABLE KEYS */;

--
-- Table structure for table `planos`
--

DROP TABLE IF EXISTS `planos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planos` (
                          `nome` varchar(255) NOT NULL,
                          `cpa` float NOT NULL DEFAULT 0,
                          `rev` varchar(255) NOT NULL,
                          `indicacao` varchar(255) NOT NULL,
                          `valor_saque_maximo` float NOT NULL DEFAULT 0,
                          `saque_diario` float NOT NULL DEFAULT 0,
                          `data` timestamp NOT NULL DEFAULT current_timestamp(),
                          `situacao` varchar(255) NOT NULL,
                          `senha` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planos`
--

/*!40000 ALTER TABLE `planos` DISABLE KEYS */;
/*!40000 ALTER TABLE `planos` ENABLE KEYS */;

--
-- Table structure for table `saque_afiliado`
--

DROP TABLE IF EXISTS `saque_afiliado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saque_afiliado` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `email` varchar(150) DEFAULT NULL,
                                  `nome` varchar(150) DEFAULT NULL,
                                  `pix` varchar(50) DEFAULT NULL,
                                  `valor` float DEFAULT 0,
                                  `status` varchar(50) DEFAULT 'AWAITING_FOR_APPROVAL',
                                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                                  `approved_at` timestamp NULL DEFAULT NULL,
                                  `canceled_at` timestamp NULL DEFAULT NULL,
                                  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saque_afiliado`
--

/*!40000 ALTER TABLE `saque_afiliado` DISABLE KEYS */;
/*!40000 ALTER TABLE `saque_afiliado` ENABLE KEYS */;

--
-- Table structure for table `saques`
--

DROP TABLE IF EXISTS `saques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saques` (
                          `email` varchar(255) NOT NULL,
                          `pix` varchar(255) NOT NULL,
                          `valor` varchar(255) NOT NULL,
                          `status` varchar(255) NOT NULL DEFAULT 'AWAITING_FOR_APPROVAL',
                          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                          `approved_at` timestamp NULL DEFAULT NULL,
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `nome` varchar(255) NOT NULL,
                          `canceled_at` timestamp NULL DEFAULT NULL,
                          PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saques`
--

/*!40000 ALTER TABLE `saques` DISABLE KEYS */;
/*!40000 ALTER TABLE `saques` ENABLE KEYS */;

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `token` (
                         `email` varchar(255) NOT NULL,
                         `value` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token`
--

/*!40000 ALTER TABLE `token` DISABLE KEYS */;
/*!40000 ALTER TABLE `token` ENABLE KEYS */;

--
-- Table structure for table `utm`
--

DROP TABLE IF EXISTS `utm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utm` (
                       `email` varchar(255) NOT NULL,
                       `utm_id` varchar(255) DEFAULT NULL,
                       `utm_source` varchar(255) DEFAULT NULL,
                       `utm_medium` varchar(255) DEFAULT NULL,
                       `utm_campaign` varchar(255) DEFAULT NULL,
                       `utm_term` varchar(255) DEFAULT NULL,
                       `utm_content` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utm`
--

/*!40000 ALTER TABLE `utm` DISABLE KEYS */;
INSERT INTO `utm` VALUES ('sarahmedeirosdeoliveira2345@gmail.com',NULL,'ig','paid','120202745669860157','120202745669920157','120202745670480157'),('franciscomiguelrosaalmeida@gmail.com',NULL,'ig','paid','120202745669860157','120202745669920157','120202745670480157');
/*!40000 ALTER TABLE `utm` ENABLE KEYS */;

--
-- Dumping routines for database 'u709343455_d21dji21ji'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-25 14:00:46