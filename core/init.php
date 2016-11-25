<?php
/**
 * Connect To the Database
 */
$db = mysqli_connect('localhost', 'root','', 'onlineshop');
if(mysqli_connect_error()){
    echo 'Database connection failed with following errors:' .mysqli_connect_error();
    die();
}

require_once $_SERVER['DOCUMENT_ROOT']. '.../config.php';
//require_once $_SERVER['DOCUMENT_ROOT']. '/OnlineShop/config.php';
require_once  BASEURL. 'helpers/helpers.php';

//define('BASEURL', '/OnlineShop/');
