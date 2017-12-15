<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jean-Mathieu
 * Date: 9/5/2015
 * Time: 4:35 AM
 */


include ("Connection.php");

$connection = new Connection();

$conn = $connection->getConnection();


//$username = @$_POST['username'];
$username = @$_GET['username'];
if($username == null)
    $user = explode("/","http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

if(@$user[5] != null){
    $username = $user[5];
    $dynamic = true;
}else{
    $dynamic = false;
}
$username = str_replace("%20", "",$username);
$username = str_replace(" ", "",$username);

if(strlen($username)> 21)
    $displayUsername = substr($username,0,19) . "...";
else
    $displayUsername = $username;

$client_id = "plgqsa87lu7qnygux1eilpfa3cfng31";
$URL = "https://api.twitch.tv/kraken/streams?channel=" . $username . "?client_id=" . $client_id;
$URL2 = "https://api.twitch.tv/kraken/channels/" . $username . "?client_id=" . $client_id;

$content = @file_get_contents($URL);
$array = json_decode($content, TRUE);

$content = @file_get_contents($URL2);
$array2 = json_decode($content, TRUE);

if($array['error'] == "Bad Request"){
    echo json_encode(array('error' => 'true', 'result' => "There's an error with TWITCH API"));
    die();
}



/*if(strlen($array['streams'][0]['game']) > 21)
    $displayGame = substr($array['streams'][0]['game'],0,19) . "...";
else*/
    $displayGame = @$array['streams'][0]['game'];




$fontpath = realpath('.'); //replace . with a different directory if needed
putenv('GDFONTPATH='.$fontpath);

//$font = 'arial.ttf';
$font = 'Roboto-Regular.ttf';
$copyright = "Made By J-M";
//header('Content-Type: image/png');

$frame = imagecreatefrompng("background.png");


//imagecopymergy(output,image,x,y,0,0,w,h,100)
$color = imagecreatetruecolor(50, 30);
$red = imagecolorallocate($color, 255, 0, 0);
$blue = imagecolorallocate($color, 0, 82, 255);
$green = imagecolorallocate($color, 0, 128, 0);
$black = imagecolorallocate($color, 0, 0, 0);

//imagecopymerge_alpha($frame, $rank, 55, 120, 0, 0, 24, 23, 100);

@imagecopymerge($frame, @imagecreatefrompng(imageResize($array2['logo'], $username)), 10, 10, 0, 0, 53, 53, 100);

if(@sizeof($array['streams'][0]) > 0){
    imagettftext($frame, 20, 0, 325, 30, $green, $font, "LIVE");
    imagettftext($frame, 12,0,80,50,$black,$font,"Playing: " . $displayGame);
    //imagettftext($frame, 12,0,300,50,$black,$font,"Status: " . $array['streams'][0]['status']);
    imagettftext($frame, 8, 0, 165, 83, $red, $font, "Click on me to see the live stream! :)");
}else
    imagettftext($frame, 20, 0, 325, 30, $red, $font, "OFFLINE");

imagettftext($frame, 20, 0, 80, 30, $blue, $font, $displayUsername);

imagettftext($frame, 12, 0, 175, 70, $black, $font, number_format($array2['followers']) . " followers");
imagettftext($frame, 12, 0, 325, 70, $black, $font, number_format($array2['views']) . " views");

imagettftext($frame, 8, 0, 380, 85, $black, $font, $copyright);


if($dynamic){
    if($array2 == null){
        header('Content-Type: text/json');
        echo json_encode(array('error' => 'true', 'result' => 'Could not load the Twitch API or the username does not exist...'));
    }else{
        header('Content-Type: image/png');
        imagepng($frame);
        imagepng($frame, $fontpath .  '/profile/' . $username . '_profile.png',9);
    }
}else{
    if($array2 != null){
        header('Content-Type: text/json');

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bindParam(1,$username);
        $stmt->execute();

        if($stmt->rowCount() < 1){
            $stmt = $conn->prepare("INSERT INTO users (`username`) VALUES(?)");
            $stmt->bindParam(1, $username);
            if($stmt->execute()){
                echo json_encode(array('error' => 'false', 'result' => array('url' => "http://jmdev.ca/twitch/img/" . $username . "/image.png", 'username' => $username)));
                imagepng($frame, $fontpath .  '/profile/' . $username . '_profile.png',9);
            }else{
                echo json_encode(array('error' => 'true', 'result' => 'Could not insert into database...' . print_r($stmt->errorInfo())));
            }
        }else{
            echo json_encode(array('error' => 'false', 'result' => array('url' => "http://jmdev.ca/twitch/img/" . $username . "/image.png", 'username' => $username)));
        }

    }else{
        header('Content-Type: text/json');
        echo json_encode(array('error' => 'true', 'result' => 'Could not load the Twitch API or the username does not exist...'));
    }
}



function imageResize($URL,$username){
    $ext = end(explode('.', $URL));



    $fontpath = realpath('.'); //replace . with a different directory if needed
    putenv('GDFONTPATH='.$fontpath);

    list($w,$h) = @getimagesize($URL);
    $nw = $w * 0.18;
    $nh = $h * 0.18;

    $thumb = @imagecreatetruecolor($nw, $nh);
    if($ext == "png"){
        $source = @imagecreatefrompng($URL);
    }else{
        $source = @imagecreatefromjpeg($URL);
    }

    // Resize
    @imagecopyresized($thumb, $source, 0, 0, 0, 0, $nw, $nh, $w, $h);

// Output
    @imagepng($thumb, $fontpath .  '/img/' . $username . '_logo.png',9);


    //imagepng($thumb,NULL,9);
    return $fontpath .  '/img/' . $username . '_logo.png';
}