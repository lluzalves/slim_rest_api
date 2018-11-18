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
  `notification` char(5),
   primary key (id),
   foreign key (user_id) references users(id))
   CHARACTER SET utf8 COLLATE utf8_general_ci;

-- insert data
INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_icon`, `token`,  `role` ,`token_expiration`)
VALUES ('1', 'danielluz', 'danielluz.alves@outlook.com', '123456', 'daniel.jpg', 'qwert12345','user', '2018-11-10 00:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_icon`, `token`,  `role` ,`token_expiration`)
VALUES ('2', 'maria', 'maria@outlook.com', '123456', 'maria.jpg', 'qwert123456','user', '2018-10-10 00:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_icon`, `token`,  `role` ,`token_expiration`)
VALUES ('3', 'jose', 'jose@outlook.com', '123456', 'jose.jpg', 'qwert123457','user', '2018-21-10 00:00:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_icon`, `token`,  `role` ,`token_expiration`)
VALUES ('4', 'ana', 'ana@outlook.com', '123456', 'ana.jpg', 'qwert123458', 'user', '2017-11-10 00:00:00');

INSERT INTO `documents` (`id`, `description`, `user_id`, `file_url`, `created_at`, `updated_at`)
VALUES ('1', 'Cpf', '1', 'cpf.jpg', '2018-11-09 00:00:00', '2018-11-10 00:00:00');
INSERT INTO `documents` (`id`, `description`, `user_id`, `file_url`, `created_at`, `updated_at`)
VALUES ('2', 'Rg', '1', 'rg.jpg', '2018-11-09 00:00:00', '2018-11-10 00:00:00');
INSERT INTO `documents` (`id`, `description`, `user_id`, `file_url`, `created_at`, `updated_at`)
VALUES ('3', 'Comprovante Residencial', '2', 'residencial.jpg', '2018-11-09 00:00:00', '2018-11-10 00:00:00');
INSERT INTO `documents` (`id`, `description`, `user_id`, `file_url`, `created_at`, `updated_at`)
VALUES ('4', 'Cateira de Trabalho', '2', 'carteira.jpg', '2018-11-09 00:00:00', '2018-11-10 00:00:00');
INSERT INTO `documents` (`id`, `description`, `user_id`, `file_url`, `created_at`, `updated_at`)
VALUES ('5', 'Cpf', '3', 'cpf.jpg', '2018-11-09 00:00:00', '2018-11-10 00:00:00');
INSERT INTO `documents` (`id`, `description`, `user_id`, `file_url`, `created_at`, `updated_at`)
VALUES ('6', 'Rg', '3', 'rg.jpg', '2018-11-09 00:00:00', '2018-11-10 00:00:00');
