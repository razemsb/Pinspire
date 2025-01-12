-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 12 2025 г., 15:49
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
-- Структура таблицы `admin`
--

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL,
  `Admin_login` varchar(30) NOT NULL,
  `Admin_ID` int(11) NOT NULL,
  `Admin_password` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `admin`
--

INSERT INTO `admin` (`ID`, `Admin_login`, `Admin_ID`, `Admin_password`) VALUES
(1, 'razemsb', 1, '$2y$10$IPXv.ScQDDVwIiJrjOnU7e/3FQ/4Dhmal.fS5mOw/c7GI32xIyGqu');

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE `images` (
  `ID` int(11) NOT NULL,
  `Image_Name` varchar(50) NOT NULL,
  `Path` varchar(100) NOT NULL,
  `Preview_Path` varchar(255) DEFAULT NULL,
  `Category` enum('Аниме','Игры','Природа','Музыка','Мемы','Машины','Другое','Животные') NOT NULL,
  `upload_user_id` int(50) NOT NULL,
  `Date_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Description` text NOT NULL,
  `Tags` varchar(60) DEFAULT NULL,
  `Active` enum('Active','Banned') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`ID`, `Image_Name`, `Path`, `Preview_Path`, `Category`, `upload_user_id`, `Date_upload`, `Description`, `Tags`, `Active`) VALUES
(17, 'крутой кот', 'uploads/6783c2cde0329.jpeg', 'preview/6783c2cde0329.jpeg', 'Другое', 1, '2025-01-12 13:47:27', 'реально крутой', 'Animals', 'Active'),
(18, 'punk not dead', 'uploads/6783c4c177870.jpeg', 'preview/6783c4c177870.jpeg', 'Мемы', 1, '2025-01-12 13:33:53', 'ведь панк никогда не умрет', 'Mems, Punk', 'Active'),
(19, 'Granger', 'uploads/6783c644bc83e.png', 'preview/6783c644bc83e.png', 'Игры', 1, '2025-01-12 13:40:20', 'top 1 gold line', 'Game, Mobile Legends', 'Active'),
(20, 'реально', 'uploads/6783c6fab5c9a.jpg', 'preview/6783c6fab5c9a.jpg', 'Игры', 1, '2025-01-12 13:43:22', 'почему...\r\n', 'Mems, Games', 'Active'),
(21, 'Soryu Asuka Langley', 'uploads/6783c766c6bc9.jpeg', 'preview/6783c766c6bc9.jpeg', 'Аниме', 1, '2025-01-12 13:45:10', 'evangelion na - na', 'Anime', 'Active'),
(22, 'женщина', 'uploads/6783c870c5710.jpeg', 'preview/6783c870c5710.jpeg', 'Игры', 1, '2025-01-12 13:49:36', 'ААААААА ЖЕНЩИНА', 'Miside, Mita', 'Active'),
(23, 'father of twitch', 'uploads/6783cc531ceae.jpg', 'preview/6783cc531ceae.jpg', 'Другое', 1, '2025-01-12 14:06:11', 'файл слишком большой...', 'People, Streamer, T2X2', 'Active'),
(24, 'ulqiorra', 'uploads/6783cc7f2f4de.jpeg', 'preview/6783cc7f2f4de.jpeg', 'Аниме', 1, '2025-01-12 14:06:55', 'espada', 'Bleach, Anime, Ulqiorra', 'Active'),
(25, 'punks nya pop shit', 'uploads/6783e2792ea2b.jpeg', 'preview/6783e2792ea2b.jpeg', 'Мемы', 1, '2025-01-12 15:40:41', 'факты', 'Mems, Punk', 'Active'),
(26, '2b', 'uploads/6783e3755791f.jpeg', 'preview/6783e3755791f.jpeg', 'Игры', 1, '2025-01-12 15:44:53', '2B ', '2B, NieR Automata', 'Active');

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
  `is_admin` enum('user','admin','system_admin') NOT NULL,
  `is_active` enum('active','not_active') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`ID`, `Login`, `Password`, `Email`, `Date_reg`, `Avatar`, `is_admin`, `is_active`) VALUES
(1, 'razemsb', '$2y$10$IPXv.ScQDDVwIiJrjOnU7e/3FQ/4Dhmal.fS5mOw/c7GI32xIyGqu', 'maxim1xxx363@gmail.com', '2025-01-12 15:24:11', 'uploads/avatar_6783de9b27f969.25024989.jpeg', 'admin', 'active'),
(2, 'evrei_cringe', '$2y$10$LsM89u5Q6l85obIKxgzJS./hpMAyWtm5A5eXOXDIad9Qm5WuQ89Zu', 'nkovaleva071@gmail.com', '2025-01-12 08:45:12', 'uploads/basic_avatar.svg', 'user', 'active');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Admin_ID` (`Admin_ID`);

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
-- AUTO_INCREMENT для таблицы `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `images`
--
ALTER TABLE `images`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `users` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
