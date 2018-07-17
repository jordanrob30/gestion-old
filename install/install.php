<?php
/**
 * install file
 * @author: Jordan Robinson
 * Date: 17/07/2018
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../include/databasemanager.php");

$db = new databasemanager();


$users_table_q = "CREATE TABLE IF NOT EXISTS `gestion_main`.`users` 
(
  `userid` INT(16) NOT NULL AUTO_INCREMENT ,
  `firstname` VARCHAR(255) NOT NULL ,
  `lastname` VARCHAR(255) NOT NULL ,
  `username` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL , 
  `description` LONGTEXT NOT NULL , 
  `role` INT(4) NOT NULL , PRIMARY KEY (`userid`)) 
  ENGINE = InnoDB;";

$db->query($users_table_q);
?>