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

if(isset($_REQUEST["md"]))
{
    $module = $_REQUEST["md"];

    if(isset($_REQUEST["ac"]))
    {
        $action = $_REQUEST["ac"];
    }
    else
    {
        $action = "index";
    }

    if($module != "Login")
    {
        include("themes/{$theme}/header.php");
        include("modules/{$module}/{$action}.php");
        include("themes/{$theme}/footer.php");
    }
    else
    {
        include("modules/User/index.php");
    }
}
else
{
    include("modules/User/index.php");
}

