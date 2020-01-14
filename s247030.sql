-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Lug 17, 2018 alle 22:39
-- Versione del server: 10.1.32-MariaDB
-- Versione PHP: 7.2.5

CREATE DATABASE IF NOT EXISTS `s247030` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `s247030`;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s247030`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `caselle`
--


CREATE TABLE IF NOT EXISTS `caselle` (
  `id` int(11) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `caselle`
--

INSERT INTO `caselle` (`id`, `num`) VALUES
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(27, 1),
(28, 1),
(36, 1),
(37, 1),
(45, 1),
(46, 1),
(54, 1),
(55, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `pezzi`
--

CREATE TABLE IF NOT EXISTS `pezzi` (
  `id` int(11) NOT NULL,
  `utente` varchar(50) NOT NULL,
  `inizio` int(11) NOT NULL,
  `fine` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `pezzi`
--

INSERT INTO `pezzi` (`id`, `utente`, `inizio`, `fine`) VALUES
(1, 'u2@p.it', 12, 15),
(1, 'u1@p.it', 27, 54);

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE IF NOT EXISTS `utenti` (
  `utente` varchar(50) NOT NULL,
  `password` binary(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`utente`, `password`) VALUES
('u1@p.it', 0x6266383163346634663437643562366363373437626236323539376162666233),
('u2@p.it', 0x6233366131353637316336643364326166643561306231323930633465333431);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `caselle`
--
ALTER TABLE `caselle`
  ADD PRIMARY KEY (`id`,`num`);

--
-- Indici per le tabelle `pezzi`
--
ALTER TABLE `pezzi`
  ADD PRIMARY KEY (`id`,`utente`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`utente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
