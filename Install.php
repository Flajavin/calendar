<?php

namespace mpf\modules\calendar;
use mpf\base\LogAwareSingleton;

/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 01.11.2016
 * Time: 10:50
 */
class Install extends LogAwareSingleton
{

    public function database()
    {
        $import = "-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) unsigned NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 NOT NULL,
  `details` text CHARACTER SET utf8 NOT NULL,
  `icon` varchar(200) NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `added_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `event_time` datetime NOT NULL,
  `event_end_time` datetime NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `visibility` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_time` (`event_time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `event_categories`;
CREATE TABLE `event_categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name_ro` varchar(150) CHARACTER SET utf8 NOT NULL,
  `name_en` varchar(150) CHARACTER SET utf8 NOT NULL,
  `html_class_suffix` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `event_categories` (`id`, `name_ro`, `name_en`, `html_class_suffix`) VALUES
(1,	'General',	'General',	'general'),
(2,	'Petreceri',	'Parties',	'sm'),
(5,	'BDSMro.ro',	'BDSMro.ro',	'rsi'),
(6,	'Intalniri',	'Meetings',	'meeting');

DROP TABLE IF EXISTS `event_organisers`;
CREATE TABLE `event_organisers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `event_organisers_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_organisers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `event_participants`;
CREATE TABLE `event_participants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `event_requirement_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `event_requirement_id` (`event_requirement_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `event_participants_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_participants_ibfk_2` FOREIGN KEY (`event_requirement_id`) REFERENCES `event_requirements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_participants_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `event_requirements`;
CREATE TABLE `event_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `title_ro` varchar(150) NOT NULL,
  `title_en` varchar(150) NOT NULL,
  `min_number` smallint(5) unsigned NOT NULL,
  `max_number` smallint(5) unsigned NOT NULL,
  `recommended_number` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `event_requirements_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `event_templates`;
CREATE TABLE `event_templates` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title_ro` varchar(200) NOT NULL,
  `title_en` varchar(200) NOT NULL,
  `details` text NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `event_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2016-11-01 09:26:29";
    }

    public function resources()
    {

    }

}