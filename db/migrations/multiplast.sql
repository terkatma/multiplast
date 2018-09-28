CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `company` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `invitation_count` int(11) NOT NULL,
  `ticket_count` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_czech_ci NOT NULL,
  `hash` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `is_woman` tinyint(1) NOT NULL DEFAULT '0',
  `language` varchar(2) COLLATE utf8_czech_ci NOT NULL DEFAULT 'cz',
  `is_sent` tinyint(4) NOT NULL DEFAULT '0',
  `is_answered` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;
