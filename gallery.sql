-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 11 2025 г., 22:07
-- Версия сервера: 5.7.24
-- Версия PHP: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `gallery`
--

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE `images` (
  `ID` int(11) NOT NULL,
  `Image_Name` varchar(50) NOT NULL,
  `Path` varchar(100) NOT NULL,
  `Category` enum('Аниме','Игры','Природа','Музыка','Мемы','Машины','Другое') NOT NULL,
  `upload_user_id` int(50) NOT NULL,
  `Date_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`ID`, `Image_Name`, `Path`, `Category`, `upload_user_id`, `Date_upload`, `Description`) VALUES
(1, 'Mems', 'uploads/1234567890.jpeg', 'Мемы', 1, '2025-01-11 18:38:12', 'Memes'),
(2, 'Mita Miside', 'uploads/mita.jpeg', 'Игры', 1, '2025-01-11 18:47:35', 'mita yopta script'),
(3, 'Asuka Lengley', 'uploads/asuka.jpeg', 'Аниме', 1, '2025-01-11 18:47:11', 'asuka lengley from Evangelion'),
(4, 'SEREGA PIRAT', 'uploads/sergey.jpeg', 'Другое', 1, '2025-01-11 18:47:11', 'мое тп отменено'),
(5, 'Ulquiorra', 'uploads/ulqiorra.jpeg', 'Аниме', 1, '2025-01-11 19:34:56', 'Ulquiorra from anime Bleach yopta'),
(6, 'Granger Marksman', 'uploads/greno4ka.png', 'Игры', 1, '2025-01-11 20:49:34', 'granger top 2 marksman it the game!'),
(7, 'TOXA', 'uploads/t2x2.jpg', 'Другое', 2, '2025-01-11 21:58:28', 'the best streamer in Russia and the CIS countries');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Login` varchar(20) NOT NULL,
  `Password` varchar(70) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Date_reg` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Avatar` varchar(80) NOT NULL,
  `is_admin` enum('user','admin','system_admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`ID`, `Login`, `Password`, `Email`, `Date_reg`, `Avatar`, `is_admin`) VALUES
(1, 'razemsb', '$2y$10$IPXv.ScQDDVwIiJrjOnU7e/3FQ/4Dhmal.fS5mOw/c7GI32xIyGqu', 'maxim1xxx363@gmail.com', '2025-01-11 19:18:16', 'uploads/mita.jpeg', 'admin'),
(2, 'evrei_cringe', '$2y$10$LsM89u5Q6l85obIKxgzJS./hpMAyWtm5A5eXOXDIad9Qm5WuQ89Zu', 'nkovaleva071@gmail.com', '2025-01-11 20:58:04', 'uploads/basic_avatar.svg', 'user');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `images`
--
ALTER TABLE `images`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
