<?php
/**
 * Created by PhpStorm.
 * User: Jean-Mathieu
 * Date: 3/25/2016
 * Time: 1:34 AM
 */

include ("Connection.php");

$connection = new Connection();

$conn = $connection->getConnection();

$stmt = $conn->query("SELECT * FROM users ORDER BY user_id DESC LIMIT 20");
$stmt->execute();
$data = $stmt->fetchAll();

$signature = array();

foreach($data as $row){
    //$signature[] = "https://jmdev.ca/twitch/img/" . $row['username'] . "/image.png";
    $signature[] = array('username' => $row['username'], 'url' =>"//jmdev.ca/twitch/profile/" . $row['username'] . "_profile.png");
}

echo json_encode($signature);

