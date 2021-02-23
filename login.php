<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once "config.php";
header("Content-Type: text/plain");

$twitchApi = new \TwitchApi\TwitchApi($options);

if(!isset($_SESSION['access_token'])){
    if(!empty($_GET['code']) && !empty($_GET['scope'])){
        $credentials = $twitchApi->getAccessCredentials($_GET['code']);
        
        if(
            empty($credentials['access_token'])
            OR (!empty($credentials['status']) && $credentials['status'] == 400)
        ){
            header("Location: ".$twitchApi->getAuthenticationUrl());
            die;
        }
        
        $_SESSION['access_token'] = $credentials['access_token'];
        $_SESSION['refresh_token'] = $credentials['refresh_token'];
        $_SESSION['expires'] = time() + $credentials['expires_in'] - 10;
        $_SESSION['access_response'] = $credentials;
    } else {
        header("Location: ".$twitchApi->getAuthenticationUrl());
        die;
    }
} else {
    $user = $twitchApi->getAuthenticatedUser($_SESSION['access_token']);
    if(empty($user['_id'])){
        header("Location: /logout.php");
        die;
    }
}

header("Location: /chat.php");
die;
