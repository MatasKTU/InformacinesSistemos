-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2024 at 10:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zvejybos_prekiu_parduotuve`
--

-- --------------------------------------------------------

--
-- Table structure for table `administratorius`
--

CREATE TABLE `administratorius` (
  `id_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `administratorius`
--

INSERT INTO `administratorius` (`id_Naudotojas`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `apmokejimas`
--

CREATE TABLE `apmokejimas` (
  `korteles_numeris` decimal(20,0) DEFAULT NULL,
  `korteles_galiojimo_data` date DEFAULT NULL,
  `korteles_savininko_vardas` varchar(255) DEFAULT NULL,
  `korteles_savininko_pavarde` varchar(255) DEFAULT NULL,
  `suma` float DEFAULT NULL,
  `id_Apmokejimas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `apmokejimas`
--

INSERT INTO `apmokejimas` (`korteles_numeris`, `korteles_galiojimo_data`, `korteles_savininko_vardas`, `korteles_savininko_pavarde`, `suma`, `id_Apmokejimas`) VALUES
(1234567890123456, '2025-12-31', 'Jonas', 'Jonaitis', 99.99, 1);

-- --------------------------------------------------------

--
-- Table structure for table `atsiliepimas`
--

CREATE TABLE `atsiliepimas` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `vertinimas` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `aprasas` varchar(255) DEFAULT NULL,
  `id_Atsiliepimas` int(11) NOT NULL,
  `fk_Klientas_id_Naudotojas` int(11) NOT NULL,
  `fk_Preke_id_Preke` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `atsiliepimas`
--

INSERT INTO `atsiliepimas` (`pavadinimas`, `vertinimas`, `data`, `aprasas`, `id_Atsiliepimas`, `fk_Klientas_id_Naudotojas`, `fk_Preke_id_Preke`) VALUES
('Puiki prekė', 5, '2024-12-15', 'Labai patenkintas preke', 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gamintojas`
--

CREATE TABLE `gamintojas` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `aprasymas` varchar(255) DEFAULT NULL,
  `tinklapio_adresas` varchar(255) DEFAULT NULL,
  `el_pastas` varchar(255) DEFAULT NULL,
  `tel_nr` decimal(20,0) DEFAULT NULL,
  `adresas` varchar(255) DEFAULT NULL,
  `valstybe` varchar(255) DEFAULT NULL,
  `id_Gamintojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `gamintojas`
--

INSERT INTO `gamintojas` (`pavadinimas`, `aprasymas`, `tinklapio_adresas`, `el_pastas`, `tel_nr`, `adresas`, `valstybe`, `id_Gamintojas`) VALUES
('Žvejo Rojus', 'Žvejybos įrankių tiekėjas', 'www.zvejorojus.lt', 'info@zvejorojus.lt', 37060000001, 'Kauno g. 10, Vilnius', 'Lietuva', 1),
('Tavo tevas', 'roze', 'roze.lt', 'roze@gmail.com', 370696969696, 'zvejybos g. 4', 'Amerika', 2);

-- --------------------------------------------------------

--
-- Table structure for table `kategorija`
--

CREATE TABLE `kategorija` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `id_Kategorija` int(11) NOT NULL,
  `fk_Preke_id_Preke` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `kategorija`
--

INSERT INTO `kategorija` (`pavadinimas`, `id_Kategorija`, `fk_Preke_id_Preke`) VALUES
('Meškerės', 1, 1),
('Meškerės', 3, 12),
('Ritės', 4, 13);

-- --------------------------------------------------------

--
-- Table structure for table `klientas`
--

CREATE TABLE `klientas` (
  `adresas` varchar(255) DEFAULT NULL,
  `id_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `klientas`
--

INSERT INTO `klientas` (`adresas`, `id_Naudotojas`) VALUES
('Vilniaus g. 5, Kaunas', 2);

-- --------------------------------------------------------

--
-- Table structure for table `naudotojas`
--

CREATE TABLE `naudotojas` (
  `vardas` varchar(255) DEFAULT NULL,
  `pavarde` varchar(255) DEFAULT NULL,
  `el_pastas` varchar(255) DEFAULT NULL,
  `slaptazodis` varchar(255) DEFAULT NULL,
  `telefono_nr` varchar(255) DEFAULT NULL,
  `id_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `naudotojas`
--

INSERT INTO `naudotojas` (`vardas`, `pavarde`, `el_pastas`, `slaptazodis`, `telefono_nr`, `id_Naudotojas`) VALUES
('Petras', 'Petraitis', 'petras@gmail.com', 'slaptazodis123', '37061234567', 1),
('Ona', 'Onaite', 'ona@gmail.com', 'slaptazodis456', '37067654321', 2),
('Aurimas', 'Aurimaitis', 'aurimas@gmail.com', 'slaptazodis789', '37069999999', 3);

-- --------------------------------------------------------

--
-- Table structure for table `pagalbos_uzklausa`
--

CREATE TABLE `pagalbos_uzklausa` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `aprasas` varchar(255) DEFAULT NULL,
  `busena` int(11) DEFAULT NULL,
  `id_Pagalbos_uzklausa` int(11) NOT NULL,
  `fk_Administratorius_id_Naudotojas` int(11) NOT NULL,
  `fk_Klientas_id_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `pagalbos_uzklausa`
--

INSERT INTO `pagalbos_uzklausa` (`pavadinimas`, `data`, `aprasas`, `busena`, `id_Pagalbos_uzklausa`, `fk_Administratorius_id_Naudotojas`, `fk_Klientas_id_Naudotojas`) VALUES
('Klausimas dėl pristatymo', '2024-12-16', 'Kada bus pristatyta mano prekė?', 2, 1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `paveikslas`
--

CREATE TABLE `paveikslas` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `id_Paveikslas` int(11) NOT NULL,
  `fk_Preke_id_Preke` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `paveikslas`
--

INSERT INTO `paveikslas` (`pavadinimas`, `id_Paveikslas`, `fk_Preke_id_Preke`) VALUES
('https://static.vecteezy.com/system/resources/previews/048/719/061/non_2x/fishing-rod-on-transparent-background-free-png.png', 1, 1),
('https://png.pngtree.com/png-vector/20240205/ourmid/pngtree-fishing-rod-with-fish-png-image_11545340.png', 3, 12),
('https://zvejybosreikmenys.lt/38848-large_default/rite-prologic-element-xd-7000-8000-fd.jpg', 4, 13);

-- --------------------------------------------------------

--
-- Table structure for table `preke`
--

CREATE TABLE `preke` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `kaina` float DEFAULT NULL,
  `aprasymas` varchar(255) DEFAULT NULL,
  `ilgis` float DEFAULT NULL,
  `daliu_sk` int(11) DEFAULT NULL,
  `medziaga` varchar(255) DEFAULT NULL,
  `ziedeliu_sk` int(11) DEFAULT NULL,
  `dydis` int(11) DEFAULT NULL,
  `guoliai` int(11) DEFAULT NULL,
  `perdavimas` varchar(255) DEFAULT NULL,
  `bugnelio_talpa` int(11) DEFAULT NULL,
  `svoris` float DEFAULT NULL,
  `kiekis` int(11) DEFAULT NULL,
  `lankstumas` int(11) DEFAULT NULL,
  `id_Preke` int(11) NOT NULL,
  `fk_Gamintojas_id_Gamintojas` int(11) NOT NULL,
  `fk_Tiekejas_id_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `preke`
--

INSERT INTO `preke` (`pavadinimas`, `kaina`, `aprasymas`, `ilgis`, `daliu_sk`, `medziaga`, `ziedeliu_sk`, `dydis`, `guoliai`, `perdavimas`, `bugnelio_talpa`, `svoris`, `kiekis`, `lankstumas`, `id_Preke`, `fk_Gamintojas_id_Gamintojas`, `fk_Tiekejas_id_Naudotojas`) VALUES
('Meškerė Ultra', 45.99, 'Lengva ir tvirta meškerė, pigi', 2.5, 2, 'Anglis', 8, NULL, NULL, NULL, NULL, 0.8, 40, 1, 1, 1, 3),
('aaa', 11, 'aaa', 11, 11, 'medis', 11, NULL, NULL, NULL, NULL, NULL, 111, 1, 12, 1, 3),
('rite', 100, 'rite didele rite', NULL, NULL, NULL, NULL, 7000, 6, '4.1:1', 350, 650, 10, NULL, 13, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `prekes_kiekis`
--

CREATE TABLE `prekes_kiekis` (
  `kiekis` int(11) DEFAULT NULL,
  `id_Prekes_kiekis` int(11) NOT NULL,
  `fk_Uzsakymas_id_Uzsakymas` int(11) NOT NULL,
  `fk_Preke_id_Preke` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `prekes_kiekis`
--

INSERT INTO `prekes_kiekis` (`kiekis`, `id_Prekes_kiekis`, `fk_Uzsakymas_id_Uzsakymas`, `fk_Preke_id_Preke`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `prekes_lankstumas`
--

CREATE TABLE `prekes_lankstumas` (
  `id_Prekes_lankstumas` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `prekes_lankstumas`
--

INSERT INTO `prekes_lankstumas` (`id_Prekes_lankstumas`, `name`) VALUES
(1, 'Didelis'),
(2, 'Vidutiniškas'),
(3, 'Mažas');

-- --------------------------------------------------------

--
-- Table structure for table `pristatymo_budas`
--

CREATE TABLE `pristatymo_budas` (
  `id_Pristatymo_budas` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `pristatymo_budas`
--

INSERT INTO `pristatymo_budas` (`id_Pristatymo_budas`, `name`) VALUES
(1, 'Kurjeris'),
(2, 'Paštomatas');

-- --------------------------------------------------------

--
-- Table structure for table `tiekejas`
--

CREATE TABLE `tiekejas` (
  `pavadinimas` varchar(255) DEFAULT NULL,
  `adresas` varchar(255) DEFAULT NULL,
  `id_Naudotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `tiekejas`
--

INSERT INTO `tiekejas` (`pavadinimas`, `adresas`, `id_Naudotojas`) VALUES
('Žvejo Tiekimas', 'Gatvės g. 1, Klaipėda', 3);

-- --------------------------------------------------------

--
-- Table structure for table `uzklausos_busena`
--

CREATE TABLE `uzklausos_busena` (
  `id_Uzklausos_busena` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `uzklausos_busena`
--

INSERT INTO `uzklausos_busena` (`id_Uzklausos_busena`, `name`) VALUES
(1, 'Atsakyta'),
(2, 'Laukiama');

-- --------------------------------------------------------

--
-- Table structure for table `uzsakymas`
--

CREATE TABLE `uzsakymas` (
  `data` date DEFAULT NULL,
  `visa_kaina` float DEFAULT NULL,
  `adresas` varchar(255) DEFAULT NULL,
  `svoris` float DEFAULT NULL,
  `busena` int(11) DEFAULT NULL,
  `pristatymo_budas` int(11) DEFAULT NULL,
  `id_Uzsakymas` int(11) NOT NULL,
  `fk_Klientas_id_Naudotojas` int(11) NOT NULL,
  `fk_Apmokejimas_id_Apmokejimas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `uzsakymas`
--

INSERT INTO `uzsakymas` (`data`, `visa_kaina`, `adresas`, `svoris`, `busena`, `pristatymo_budas`, `id_Uzsakymas`, `fk_Klientas_id_Naudotojas`, `fk_Apmokejimas_id_Apmokejimas`) VALUES
('2024-12-16', 100, 'Jonavos g. 5, Kaunas', 1.5, 2, 1, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `uzsakymo_busena`
--

CREATE TABLE `uzsakymo_busena` (
  `id_Uzsakymo_busena` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `uzsakymo_busena`
--

INSERT INTO `uzsakymo_busena` (`id_Uzsakymo_busena`, `name`) VALUES
(1, 'Pristatytas'),
(2, 'Ruošiamas'),
(3, 'Išsiųstas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administratorius`
--
ALTER TABLE `administratorius`
  ADD PRIMARY KEY (`id_Naudotojas`);

--
-- Indexes for table `apmokejimas`
--
ALTER TABLE `apmokejimas`
  ADD PRIMARY KEY (`id_Apmokejimas`);

--
-- Indexes for table `atsiliepimas`
--
ALTER TABLE `atsiliepimas`
  ADD PRIMARY KEY (`id_Atsiliepimas`),
  ADD KEY `fk_Klientas_id_Naudotojas` (`fk_Klientas_id_Naudotojas`),
  ADD KEY `fk_Preke_id_Preke` (`fk_Preke_id_Preke`);

--
-- Indexes for table `gamintojas`
--
ALTER TABLE `gamintojas`
  ADD PRIMARY KEY (`id_Gamintojas`);

--
-- Indexes for table `kategorija`
--
ALTER TABLE `kategorija`
  ADD PRIMARY KEY (`id_Kategorija`),
  ADD KEY `fk_Preke_id_Preke` (`fk_Preke_id_Preke`);

--
-- Indexes for table `klientas`
--
ALTER TABLE `klientas`
  ADD PRIMARY KEY (`id_Naudotojas`);

--
-- Indexes for table `naudotojas`
--
ALTER TABLE `naudotojas`
  ADD PRIMARY KEY (`id_Naudotojas`);

--
-- Indexes for table `pagalbos_uzklausa`
--
ALTER TABLE `pagalbos_uzklausa`
  ADD PRIMARY KEY (`id_Pagalbos_uzklausa`),
  ADD KEY `busena` (`busena`),
  ADD KEY `fk_Administratorius_id_Naudotojas` (`fk_Administratorius_id_Naudotojas`),
  ADD KEY `fk_Klientas_id_Naudotojas` (`fk_Klientas_id_Naudotojas`);

--
-- Indexes for table `paveikslas`
--
ALTER TABLE `paveikslas`
  ADD PRIMARY KEY (`id_Paveikslas`),
  ADD KEY `fk_Preke_id_Preke` (`fk_Preke_id_Preke`);

--
-- Indexes for table `preke`
--
ALTER TABLE `preke`
  ADD PRIMARY KEY (`id_Preke`),
  ADD KEY `lankstumas` (`lankstumas`),
  ADD KEY `fk_Gamintojas_id_Gamintojas` (`fk_Gamintojas_id_Gamintojas`),
  ADD KEY `fk_Tiekejas_id_Naudotojas` (`fk_Tiekejas_id_Naudotojas`);

--
-- Indexes for table `prekes_kiekis`
--
ALTER TABLE `prekes_kiekis`
  ADD PRIMARY KEY (`id_Prekes_kiekis`),
  ADD KEY `fk_Uzsakymas_id_Uzsakymas` (`fk_Uzsakymas_id_Uzsakymas`),
  ADD KEY `fk_Preke_id_Preke` (`fk_Preke_id_Preke`);

--
-- Indexes for table `prekes_lankstumas`
--
ALTER TABLE `prekes_lankstumas`
  ADD PRIMARY KEY (`id_Prekes_lankstumas`);

--
-- Indexes for table `pristatymo_budas`
--
ALTER TABLE `pristatymo_budas`
  ADD PRIMARY KEY (`id_Pristatymo_budas`);

--
-- Indexes for table `tiekejas`
--
ALTER TABLE `tiekejas`
  ADD PRIMARY KEY (`id_Naudotojas`);

--
-- Indexes for table `uzklausos_busena`
--
ALTER TABLE `uzklausos_busena`
  ADD PRIMARY KEY (`id_Uzklausos_busena`);

--
-- Indexes for table `uzsakymas`
--
ALTER TABLE `uzsakymas`
  ADD PRIMARY KEY (`id_Uzsakymas`),
  ADD KEY `busena` (`busena`),
  ADD KEY `pristatymo_budas` (`pristatymo_budas`),
  ADD KEY `fk_Klientas_id_Naudotojas` (`fk_Klientas_id_Naudotojas`),
  ADD KEY `fk_Apmokejimas_id_Apmokejimas` (`fk_Apmokejimas_id_Apmokejimas`);

--
-- Indexes for table `uzsakymo_busena`
--
ALTER TABLE `uzsakymo_busena`
  ADD PRIMARY KEY (`id_Uzsakymo_busena`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apmokejimas`
--
ALTER TABLE `apmokejimas`
  MODIFY `id_Apmokejimas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `atsiliepimas`
--
ALTER TABLE `atsiliepimas`
  MODIFY `id_Atsiliepimas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gamintojas`
--
ALTER TABLE `gamintojas`
  MODIFY `id_Gamintojas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kategorija`
--
ALTER TABLE `kategorija`
  MODIFY `id_Kategorija` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `naudotojas`
--
ALTER TABLE `naudotojas`
  MODIFY `id_Naudotojas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pagalbos_uzklausa`
--
ALTER TABLE `pagalbos_uzklausa`
  MODIFY `id_Pagalbos_uzklausa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paveikslas`
--
ALTER TABLE `paveikslas`
  MODIFY `id_Paveikslas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `preke`
--
ALTER TABLE `preke`
  MODIFY `id_Preke` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `prekes_kiekis`
--
ALTER TABLE `prekes_kiekis`
  MODIFY `id_Prekes_kiekis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prekes_lankstumas`
--
ALTER TABLE `prekes_lankstumas`
  MODIFY `id_Prekes_lankstumas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pristatymo_budas`
--
ALTER TABLE `pristatymo_budas`
  MODIFY `id_Pristatymo_budas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `uzklausos_busena`
--
ALTER TABLE `uzklausos_busena`
  MODIFY `id_Uzklausos_busena` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `uzsakymas`
--
ALTER TABLE `uzsakymas`
  MODIFY `id_Uzsakymas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uzsakymo_busena`
--
ALTER TABLE `uzsakymo_busena`
  MODIFY `id_Uzsakymo_busena` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administratorius`
--
ALTER TABLE `administratorius`
  ADD CONSTRAINT `administratorius_ibfk_1` FOREIGN KEY (`id_Naudotojas`) REFERENCES `naudotojas` (`id_Naudotojas`);

--
-- Constraints for table `atsiliepimas`
--
ALTER TABLE `atsiliepimas`
  ADD CONSTRAINT `atsiliepimas_ibfk_1` FOREIGN KEY (`fk_Klientas_id_Naudotojas`) REFERENCES `klientas` (`id_Naudotojas`),
  ADD CONSTRAINT `atsiliepimas_ibfk_2` FOREIGN KEY (`fk_Preke_id_Preke`) REFERENCES `preke` (`id_Preke`);

--
-- Constraints for table `kategorija`
--
ALTER TABLE `kategorija`
  ADD CONSTRAINT `kategorija_ibfk_1` FOREIGN KEY (`fk_Preke_id_Preke`) REFERENCES `preke` (`id_Preke`);

--
-- Constraints for table `klientas`
--
ALTER TABLE `klientas`
  ADD CONSTRAINT `klientas_ibfk_1` FOREIGN KEY (`id_Naudotojas`) REFERENCES `naudotojas` (`id_Naudotojas`);

--
-- Constraints for table `pagalbos_uzklausa`
--
ALTER TABLE `pagalbos_uzklausa`
  ADD CONSTRAINT `pagalbos_uzklausa_ibfk_1` FOREIGN KEY (`busena`) REFERENCES `uzklausos_busena` (`id_Uzklausos_busena`),
  ADD CONSTRAINT `pagalbos_uzklausa_ibfk_2` FOREIGN KEY (`fk_Administratorius_id_Naudotojas`) REFERENCES `administratorius` (`id_Naudotojas`),
  ADD CONSTRAINT `pagalbos_uzklausa_ibfk_3` FOREIGN KEY (`fk_Klientas_id_Naudotojas`) REFERENCES `klientas` (`id_Naudotojas`);

--
-- Constraints for table `paveikslas`
--
ALTER TABLE `paveikslas`
  ADD CONSTRAINT `paveikslas_ibfk_1` FOREIGN KEY (`fk_Preke_id_Preke`) REFERENCES `preke` (`id_Preke`);

--
-- Constraints for table `preke`
--
ALTER TABLE `preke`
  ADD CONSTRAINT `preke_ibfk_1` FOREIGN KEY (`lankstumas`) REFERENCES `prekes_lankstumas` (`id_Prekes_lankstumas`),
  ADD CONSTRAINT `preke_ibfk_2` FOREIGN KEY (`fk_Gamintojas_id_Gamintojas`) REFERENCES `gamintojas` (`id_Gamintojas`),
  ADD CONSTRAINT `preke_ibfk_3` FOREIGN KEY (`fk_Tiekejas_id_Naudotojas`) REFERENCES `tiekejas` (`id_Naudotojas`);

--
-- Constraints for table `prekes_kiekis`
--
ALTER TABLE `prekes_kiekis`
  ADD CONSTRAINT `prekes_kiekis_ibfk_1` FOREIGN KEY (`fk_Uzsakymas_id_Uzsakymas`) REFERENCES `uzsakymas` (`id_Uzsakymas`),
  ADD CONSTRAINT `prekes_kiekis_ibfk_2` FOREIGN KEY (`fk_Preke_id_Preke`) REFERENCES `preke` (`id_Preke`);

--
-- Constraints for table `tiekejas`
--
ALTER TABLE `tiekejas`
  ADD CONSTRAINT `tiekejas_ibfk_1` FOREIGN KEY (`id_Naudotojas`) REFERENCES `naudotojas` (`id_Naudotojas`);

--
-- Constraints for table `uzsakymas`
--
ALTER TABLE `uzsakymas`
  ADD CONSTRAINT `uzsakymas_ibfk_1` FOREIGN KEY (`busena`) REFERENCES `uzsakymo_busena` (`id_Uzsakymo_busena`),
  ADD CONSTRAINT `uzsakymas_ibfk_2` FOREIGN KEY (`pristatymo_budas`) REFERENCES `pristatymo_budas` (`id_Pristatymo_budas`),
  ADD CONSTRAINT `uzsakymas_ibfk_3` FOREIGN KEY (`fk_Klientas_id_Naudotojas`) REFERENCES `klientas` (`id_Naudotojas`),
  ADD CONSTRAINT `uzsakymas_ibfk_4` FOREIGN KEY (`fk_Apmokejimas_id_Apmokejimas`) REFERENCES `apmokejimas` (`id_Apmokejimas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
