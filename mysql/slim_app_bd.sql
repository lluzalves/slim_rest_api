-- create database
drop database if exists slim_app;
create database if not exists slim_app DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
use slim_app;


   -- create user table
  create table if not exists `slim_app`.`users`(
  `id` int auto_increment not null,
  `name` varchar(100) not null,
  `email` varchar(100) not null,
  `password` varchar(255) not null,
  `profile_icon` varchar(255) not null,
  `prontuario` int (6) not null,
  `token` varchar(255) null,
  `role` char(16) null,
  `token_expiration` DATETIME not null,
   unique(email),
   primary key (id))
   CHARACTER SET utf8 COLLATE utf8_general_ci;

-- create document table
   create table if not exists `slim_app`.`documents`(
  `id` int auto_increment not null,
  `description` varchar(100) not null,
  `user_id` int (10) not null,
  `file_url` varchar(255) not null,
  `created_at` datetime not null,
  `updated_at` datetime not null,
  `notification` char(5) not null,
  `is_validated` tinyint(1) not null,
  `type` varchar(100) not null,
   primary key (id),
   foreign key (user_id) references users(id))
   CHARACTER SET utf8 COLLATE utf8_general_ci;

-- create periodic table
   create table if not exists `slim_app`.`periodicurl`(
  `id` int auto_increment not null,
  `url_token` DATETIME not null,
  `user_email`  varchar(100) not null,
   primary key (id),
   foreign key (user_email) references users(email))
   CHARACTER SET utf8 COLLATE utf8_general_ci;

