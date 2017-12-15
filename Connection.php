<?php

/**
 * Created by PhpStorm.
 * User: Jean-Mathieu
 * Date: 3/25/2016
 * Time: 1:10 AM
 */
class Connection
{
    private $connection = null;
    public function getConnection(){
        try{
            return new PDO("mysql:host=$host;dbname=$db", '$username', '$password');
        }catch(Exception $e){
            echo "Can't connect to DATABASE!";
            return null;
        }
    }
}