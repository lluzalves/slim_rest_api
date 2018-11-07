-- create database
drop database if exists slim_app;
create database if not exists slim_app DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
use slim_app;


   -- create user table
  create table if not exists `slim_app`.`users`(
  `id` int auto_increment not null,
  `username` varchar(100) not null,
  `email` varchar(100) not null,
  `password` varchar(255) not null,
  `profile_icon` varchar(255) not null,
  `apikey` varchar(255) not null,
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
   primary key (id),
   foreign key (user_id) references users(id))
   CHARACTER SET utf8 COLLATE utf8_general_ci;
