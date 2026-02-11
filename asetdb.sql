-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               PostgreSQL 18.1 on x86_64-windows, compiled by msvc-19.44.35221, 64-bit
-- Server OS:                    
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES  */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table public.aset
CREATE TABLE IF NOT EXISTS "aset" (
	"asetid" BIGINT NOT NULL,
	"jenisid" BIGINT NOT NULL DEFAULT '0',
	"merkid" BIGINT NOT NULL DEFAULT '0',
	"lokasiid" BIGINT NOT NULL DEFAULT '0',
	"asetkode" VARCHAR(20) NOT NULL,
	"pembeliandate" TIMESTAMP NOT NULL,
	"pembelianno" VARCHAR(20) NOT NULL,
	"isdeleted" INTEGER NOT NULL,
	"createdby" BIGINT NOT NULL,
	"createddate" TIMESTAMP NOT NULL,
	"updatedby" BIGINT NULL DEFAULT NULL,
	"updateddate" TIMESTAMP NULL DEFAULT NULL,
	"deletedby" BIGINT NULL DEFAULT NULL,
	"deleteddate" TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY ("asetid"),
	CONSTRAINT "FK_aset_jenis" FOREIGN KEY ("jenisid") REFERENCES "jenis" ("jenisid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_aset_lokasi" FOREIGN KEY ("lokasiid") REFERENCES "lokasi" ("lokasiid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_aset_merk" FOREIGN KEY ("merkid") REFERENCES "merk" ("merkid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_aset_user" FOREIGN KEY ("createdby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_aset_user_2" FOREIGN KEY ("updatedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_aset_user_3" FOREIGN KEY ("deletedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.aset: -1 rows
/*!40000 ALTER TABLE "aset" DISABLE KEYS */;
/*!40000 ALTER TABLE "aset" ENABLE KEYS */;

-- Dumping structure for table public.asetmove
CREATE TABLE IF NOT EXISTS "asetmove" (
	"asetmoveid" BIGINT NOT NULL,
	"asetid" BIGINT NOT NULL DEFAULT '0',
	"asetmoveno" VARCHAR(20) NOT NULL,
	"lokasiawalid" BIGINT NOT NULL,
	"lokasiakhirid" BIGINT NOT NULL,
	"isdeleted" INTEGER NOT NULL,
	"createdby" BIGINT NOT NULL,
	"createddate" TIMESTAMP NOT NULL,
	"updatedby" BIGINT NULL DEFAULT NULL,
	"updateddate" TIMESTAMP NULL DEFAULT NULL,
	"deletedby" BIGINT NULL DEFAULT NULL,
	"deleteddate" TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY ("asetmoveid"),
	CONSTRAINT "FK_asetmove_aset" FOREIGN KEY ("asetid") REFERENCES "aset" ("asetid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_asetmove_lokasi" FOREIGN KEY ("lokasiawalid") REFERENCES "lokasi" ("lokasiid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_asetmove_lokasi_2" FOREIGN KEY ("lokasiakhirid") REFERENCES "lokasi" ("lokasiid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_asetmove_user" FOREIGN KEY ("createdby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_asetmove_user_2" FOREIGN KEY ("updatedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_asetmove_user_3" FOREIGN KEY ("deletedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.asetmove: 0 rows
/*!40000 ALTER TABLE "asetmove" DISABLE KEYS */;
/*!40000 ALTER TABLE "asetmove" ENABLE KEYS */;

-- Dumping structure for table public.jenis
CREATE TABLE IF NOT EXISTS "jenis" (
	"jenisid" BIGINT NOT NULL,
	"jeniskode" VARCHAR(5) NOT NULL,
	"jenis" VARCHAR(20) NOT NULL,
	"isdeleted" INTEGER NOT NULL,
	"createdby" BIGINT NOT NULL,
	"createddate" TIMESTAMP NOT NULL,
	"updatedby" BIGINT NULL DEFAULT NULL,
	"updateddate" TIMESTAMP NULL DEFAULT NULL,
	"deletedby" BIGINT NULL DEFAULT NULL,
	"deleteddate" TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY ("jenisid"),
	CONSTRAINT "FK_jenis_user" FOREIGN KEY ("createdby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_jenis_user_2" FOREIGN KEY ("updatedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_jenis_user_3" FOREIGN KEY ("deletedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.jenis: -1 rows
/*!40000 ALTER TABLE "jenis" DISABLE KEYS */;
/*!40000 ALTER TABLE "jenis" ENABLE KEYS */;

-- Dumping structure for table public.lokasi
CREATE TABLE IF NOT EXISTS "lokasi" (
	"lokasiid" BIGINT NOT NULL,
	"lokasikode" VARCHAR(5) NOT NULL,
	"lokasi" VARCHAR(20) NOT NULL,
	"isdeleted" INTEGER NOT NULL,
	"createdby" BIGINT NOT NULL,
	"createddate" TIMESTAMP NOT NULL,
	"updatedby" BIGINT NULL DEFAULT NULL,
	"updateddate" TIMESTAMP NULL DEFAULT NULL,
	"deletedby" BIGINT NULL DEFAULT NULL,
	"deleteddate" TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY ("lokasiid"),
	CONSTRAINT "FK_lokasi_user" FOREIGN KEY ("createdby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_lokasi_user_2" FOREIGN KEY ("updatedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_lokasi_user_3" FOREIGN KEY ("deletedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.lokasi: 1 rows
/*!40000 ALTER TABLE "lokasi" DISABLE KEYS */;
INSERT INTO "lokasi" ("lokasiid", "lokasikode", "lokasi", "isdeleted", "createdby", "createddate", "updatedby", "updateddate", "deletedby", "deleteddate") VALUES
	(1, '231', 'dsDASSDD', 0, 1, '2026-02-11 12:56:41', NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE "lokasi" ENABLE KEYS */;

-- Dumping structure for table public.merk
CREATE TABLE IF NOT EXISTS "merk" (
	"merkid" BIGINT NOT NULL,
	"merk" VARCHAR(20) NOT NULL,
	"isdeleted" INTEGER NOT NULL,
	"createdby" BIGINT NOT NULL,
	"createddate" TIMESTAMP NOT NULL,
	"updatedby" BIGINT NULL DEFAULT NULL,
	"updateddate" TIMESTAMP NULL DEFAULT NULL,
	"deletedby" BIGINT NULL DEFAULT NULL,
	"deleteddate" TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY ("merkid"),
	CONSTRAINT "FK_merk_user" FOREIGN KEY ("createdby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_merk_user_2" FOREIGN KEY ("updatedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_merk_user_3" FOREIGN KEY ("deletedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.merk: 1 rows
/*!40000 ALTER TABLE "merk" DISABLE KEYS */;
INSERT INTO "merk" ("merkid", "merk", "isdeleted", "createdby", "createddate", "updatedby", "updateddate", "deletedby", "deleteddate") VALUES
	(1, 'test', 0, 1, '2026-02-11 12:53:51', NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE "merk" ENABLE KEYS */;

-- Dumping structure for table public.user
CREATE TABLE IF NOT EXISTS "user" (
	"userid" BIGINT NOT NULL,
	"username" VARCHAR(20) NOT NULL,
	"nama" VARCHAR(100) NOT NULL,
	"isdeleted" INTEGER NOT NULL,
	"createdby" BIGINT NOT NULL,
	"createddate" TIMESTAMP NOT NULL,
	"updatedby" BIGINT NULL DEFAULT NULL,
	"updateddate" TIMESTAMP NULL DEFAULT NULL,
	"deletedby" BIGINT NULL DEFAULT NULL,
	"deleteddate" TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY ("userid"),
	CONSTRAINT "FK_user_user" FOREIGN KEY ("createdby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_user_user_2" FOREIGN KEY ("updatedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT "FK_user_user_3" FOREIGN KEY ("deletedby") REFERENCES "user" ("userid") ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Dumping data for table public.user: 2 rows
/*!40000 ALTER TABLE "user" DISABLE KEYS */;
INSERT INTO "user" ("userid", "username", "nama", "isdeleted", "createdby", "createddate", "updatedby", "updateddate", "deletedby", "deleteddate") VALUES
	(1, 'admin', 'test', 0, 1, '2026-02-11 05:52:16', NULL, NULL, NULL, NULL),
	(3, 'test', 'test', 0, 1, '2026-02-11 12:52:44', NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE "user" ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
