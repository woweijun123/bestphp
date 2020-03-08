create database `project` default charset utf8mb4 collate utf8mb4_general_ci;
use `project`;
create table `item`
(
  `id`        int(11)      not null auto_increment, 
  `item_name` varchar(255) not null,
  primary key (`id`)
) engine = InnoDb
  auto_increment = 1
  default charset = utf8mb4;