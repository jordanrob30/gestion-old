<?php
/**
 * @author Jordan Robinson
 * Date: 17/07/2018
 */

require_once("config.php");

class databasemanager
{
    var $conn;

    /**
     * databasemanager constructor.
     *
     * here we are setting our $conn variable to use for our db wrapper
     */
    public function __construct()
    {
        global $dbconfig;

        $this->conn = new PDO("mysql:host={$dbconfig['hostname']};dbname={$dbconfig['dbname']}",$dbconfig["username"], $dbconfig['password']);
    }
}