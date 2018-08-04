<?php
/**
 * @author Jordan Robinson
 * Date: 17/07/2018
 */


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

     /**
     * query wrapper
     *
     * @param $sql
     *
     * @return int $result returning the sql results set
     */
    public function query($sql)
    {
        $result = $this->conn->query($sql);

        return $result;
    }

    /**
     * return number of rows in returned query
     *
     * @param $result - the sql results generated from the query
     * @return mixed - the number of rows
     */
    public function num_rows($result)
    {
        $num_rows = $result->rowCount();

        return $num_rows;
    }

    public function fetch_assoc($result)
    {
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function get_last_insert_id()
    {
        $this->conn->lastInsertId();
    }

}