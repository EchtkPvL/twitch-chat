<?php

function checkLogin($twitchApi){
    if(empty($_SESSION['access_token'])){
        header("Location: /login.php");
        die;
    } else {
        $user = $twitchApi->getAuthenticatedUser($_SESSION['access_token']);
        if(empty($user['_id'])){
            header("Location: /logout.php");
            die;
        }
    }

    return true;
}
