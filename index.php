<?php
/**
 * @author: Jordan Robinson
 * Date: 17/07/2018
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("include/config.php");
include_once("include/databasemanager.php");

$db = new databasemanager();

global $theme;

include("themes/{$theme}/header.php");



