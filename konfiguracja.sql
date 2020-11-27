DROP USER IF EXISTS 'EwidencjaObiektow'@'localhost';
CREATE USER 'EwidencjaObiektow'@'localhost' IDENTIFIED BY 'EOinz2020';
CREATE DATABASE EwidencjaObiektow;
GRANT ALL PRIVILEGES on EwidencjaObiektow.* to 'EwidencjaObiektow'@'localhost';

CREATE TABLE `sessions` (
    `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
    `sess_data` BLOB NOT NULL,
    `sess_lifetime` INTEGER UNSIGNED NOT NULL,
    `sess_time` INTEGER UNSIGNED NOT NULL
) COLLATE utf8mb4_bin, ENGINE = InnoDB
