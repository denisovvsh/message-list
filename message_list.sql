-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Мар 22 2021 г., 08:32
-- Версия сервера: 8.0.23-0ubuntu0.20.04.1
-- Версия PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `message_list`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `id_author` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `authors`
--

INSERT INTO `authors` (`id_author`, `name`, `lastname`) VALUES
(1, 'Иван', 'Иванов'),
(2, 'Сергей', 'Сергеев');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id_comment` int NOT NULL,
  `id_message` int NOT NULL,
  `id_author` int NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id_message` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `id_author` int NOT NULL,
  `excerpt` varchar(255) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id_message`, `title`, `id_author`, `excerpt`, `text`) VALUES
(4, 'Сообщение 4', 1, 'Описание сообщения 4', 'Текст сообщения 4'),
(5, 'Сообщение 5', 2, 'Описание сообщения 5', 'Текст сообщения 5'),
(6, 'Сообщение 6', 1, 'Описание сообщения 6', 'Текст сообщения 6'),
(7, 'Сообщение 7', 1, 'Описание сообщения 7', 'Текст сообщения 7'),
(8, 'Сообщение 8', 2, 'Описание сообщения 8', 'dfdf'),
(9, 'Сообщение 9', 1, 'Описание сообщения 9', 'Текст сообщения 9'),
(10, 'Сообщение 10', 1, 'Описание сообщения 10', 'Текст сообщения 10'),
(11, 'Сообщение 11', 2, 'Описание сообщения 11', 'Текст сообщения 11'),
(12, 'Сообщение 12', 1, 'Описание сообщения 12', 'Текст сообщения 12'),
(13, 'Сообщение 13', 1, 'Описание сообщения 13', 'Текст сообщения 13'),
(14, 'Сообщение 14', 2, 'Описание сообщения 14', 'Текст сообщения 14'),
(15, 'Сообщение 15', 1, 'Описание сообщения 15', 'Текст сообщения 15'),
(16, 'Сообщение 16', 1, 'Описание сообщения 16', 'Текст сообщения 16'),
(17, 'Сообщение 17', 1, 'Описание сообщения 17', 'Текст сообщения 17'),
(18, 'Сообщение 18', 2, 'Описание сообщения 18', 'Текст сообщения 18'),
(19, 'Сообщение 19', 1, 'Описание сообщения 19', 'Текст сообщения 19'),
(20, 'Сообщение 20', 1, 'Описание сообщения 20', 'Текст сообщения 20');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id_author`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `id_author` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
