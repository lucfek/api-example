DROP DATABASE IF EXISTS api;
CREATE DATABASE api;


CREATE TABLE `api`.`users` (
     `uuid` char(36) NOT NULL,
     `firstname` varchar(255)  NOT NULL,
     `lastname` varchar(255) NOT NULL,
     `email` varchar(255)  NOT NULL UNIQUE,
     `phone` varchar(255) NOT NULL,
     `password` varchar(255) NOT NULL,
     `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     PRIMARY KEY (`uuid`)
);

CREATE TABLE `api`.`otp` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_email` varchar(255)  NOT NULL,
     `tries` tinyint NOT NULL,
     `password` varchar(255) NOT NULL,
     `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     PRIMARY KEY (`id`)
);

CREATE TRIGGER `api`.`before_insert_users`
    BEFORE INSERT ON users
    FOR EACH ROW
    SET new.uuid = uuid();