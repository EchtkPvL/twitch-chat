<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once "config.php";
header("Content-Type: text/plain");

$twitchApi = new \TwitchApi\TwitchApi($options);

if(checkLogin($twitchApi)) echo "Logged in\n";

$user = $twitchApi->getAuthenticatedUser($_SESSION['access_token']);
print_r($user);
