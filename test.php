<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jean-Mathieu
 * Date: 9/6/2015
 * Time: 3:43 PM
 */

$username = "jean8mathieu";

$URL = "https://api.twitch.tv/kraken/streams?channel=" . $username;
$content = file_get_contents($URL);
$array = json_decode($content, TRUE);

print_r($array['streams']);


echo "<br>Game: " . $array['streams'][0]['game'];