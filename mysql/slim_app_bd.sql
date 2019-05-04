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
  `created_at` datetime not null,
  `updated_at` datetime not null,
   unique(email),
   primary key (id))
CHARACTER SET utf8 COLLATE utf8_general_ci;


-- create edict table
create table if not exists `slim_app`.`edict`(
`id` int auto_increment not null,
`description` varchar(100) not null,
`title` varchar (100) not null,
`created_by` varchar(255) not null,
`elegilable_roles` varchar(100) not null,
`created_at` datetime not null,
`updated_at` datetime not null,
`notification` char(120) not null,
`is_available` tinyint(1) not null,
`starts_at` datetime not null,
`end_at` datetime not null,
`type` varchar(100) not null,
primary key (id))
CHARACTER SET utf8 COLLATE utf8_general_ci;

-- create document table
create table if not exists `slim_app`.`documents`(
  `id` int auto_increment not null,
  `description` varchar(100) not null,
  `user_id` int (10) not null,
  `edict_id` int (10) not null,
  `file_url` varchar(255) not null,
  `created_at` datetime not null,
  `updated_at` datetime not null,
  `notification` char(20) not null,
  `is_validated` tinyint(1) not null,
  `type` varchar(100) not null,
   primary key (id),
   foreign key (user_id) references users(id),
   foreign key (edict_id) references edict(id))
CHARACTER SET utf8 COLLATE utf8_general_ci;

-- create user notification table
create table if not exists `slim_app`.`usnotifications`(
`id` int auto_increment not null,
`body` varchar(1000) not null,
`created_at` datetime not null,
`updated_at` datetime not null,
`creator_id` int (10) not null,
`receiver_id` int (10) not null,
`read_status` tinyint(1) not null,
`type` varchar (50) not null,
 primary key (id),
 foreign key (receiver_id) references users(id))
CHARACTER SET utf8 COLLATE utf8_general_ci;

-- create document notification table
create table if not exists `slim_app`.`docnotifications`(
`id` int auto_increment not null,
`body` varchar(1000) not null,
`created_at` datetime not null,
`updated_at` datetime not null,
`creator_id` int (10) not null,
`receiver_id` int (10) not null,
`document_id` int (10) not null,
`read_status` tinyint(1) not null,
`type`  varchar (50) not null,
 primary key (id),
 foreign key (document_id) references documents(id))
CHARACTER SET utf8 COLLATE utf8_general_ci;


