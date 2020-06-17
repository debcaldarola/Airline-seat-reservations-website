-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 18, 2019 alle 13:22
-- Versione del server: 10.3.15-MariaDB
-- Versione PHP: 7.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s263626`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `flights`
--

DROP TABLE IF EXISTS `flights`;
CREATE TABLE `flights` (
  `flightID` varchar(5) NOT NULL,
  `modelID` varchar(5) NOT NULL,
  `date` date NOT NULL,
  `departure` varchar(30) NOT NULL,
  `destination` varchar(30) NOT NULL,
  `totSeats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `flights`
--

INSERT INTO `flights` (`flightID`, `modelID`, `date`, `departure`, `destination`, `totSeats`) VALUES
('DC123', 'AAAAA', '2019-06-20', 'FCO', 'YYZ', 60);

-- --------------------------------------------------------

--
-- Struttura della tabella `plane_models`
--

DROP TABLE IF EXISTS `plane_models`;
CREATE TABLE `plane_models` (
  `modelID` varchar(5) NOT NULL,
  `totSeats` int(11) NOT NULL,
  `nRows` int(11) NOT NULL,
  `nColumns` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `plane_models`
--

INSERT INTO `plane_models` (`modelID`, `totSeats`, `nRows`, `nColumns`) VALUES
('AAAAA', 60, 10, 6);

-- --------------------------------------------------------

--
-- Struttura della tabella `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `email` varchar(30) NOT NULL,
  `flightID` varchar(5) NOT NULL,
  `seatID` varchar(2) NOT NULL,
  `paid` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `reservations`
--

INSERT INTO `reservations` (`email`, `flightID`, `seatID`, `paid`) VALUES
('u1@p.it', 'DC123', 'A4', 0),
('u1@p.it', 'DC123', 'D4', 0),
('u2@p.it', 'DC123', 'B2', 1),
('u2@p.it', 'DC123', 'B3', 1),
('u2@p.it', 'DC123', 'B4', 1),
('u2@p.it', 'DC123', 'F4', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `email` varchar(30) NOT NULL,
  `psw` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`email`, `psw`) VALUES
('u1@p.it', 'ec6ef230f1828039ee794566b9c58adcuasiekpuen'),
('u2@p.it', '1d665b9b1467944c128a5575119d1cfdraepekenwe');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`flightID`,`modelID`),
  ADD KEY `modelID` (`modelID`);

--
-- Indici per le tabelle `plane_models`
--
ALTER TABLE `plane_models`
  ADD PRIMARY KEY (`modelID`);

--
-- Indici per le tabelle `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`email`,`flightID`,`seatID`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
