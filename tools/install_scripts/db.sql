CREATE DATABASE `files`;
USE `files`;
CREATE TABLE IF NOT EXISTS `file_item`
(
    `id`            bigint(20) unsigned                    NOT NULL AUTO_INCREMENT,
    `path`          varchar(255) COLLATE latin1_general_ci NOT NULL,
    `name`          varchar(100) COLLATE latin1_general_ci NOT NULL,
    `type`          tinyint(3) unsigned                    NOT NULL DEFAULT '0',
    `state`         tinyint(3) unsigned                    NOT NULL DEFAULT '0',
    `inserted_date` datetime                               NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  COLLATE = latin1_general_ci;
