<?php
session_start();
include_once ('../utilities/functions.php');
include_once ('../utilities/db.php');

checkEnabledCookies();
checkHTTPS();
handleInactivity();

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FlyDC Sign up</title>
    <link rel="icon" href="../images/airplane.ico">
    <link rel="stylesheet" type="text/css" href="../css/classes.css">
    <link rel="stylesheet" type="text/css" href="../css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../css/home.css">
    <script type="text/javascript" src="../js/functions.js"></script>
    <noscript>
        Sorry: Your browser does not support or has disabled javascript.
    </noscript>
</head>
<body class="default">
    <header id="header-login">
        <h1 id="welcome-login" class="welcome">FlyDC
            <img src="../images/airplane.png" class="header-logo" alt="">
        </h1>
    </header>
    <div class="column side">
        <div class="nav_bar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a>About</a></li>
            </ul>
        </div>
    </div>

<?php

if(!isset($_SESSION['userLoggedIn']) || $_SESSION['userLoggedIn']==false) {
    echo '<div class="column middle"><p class="box-msg failure">You are not logged in. Redirecting to homepage...</p></div>';
} else {
    $_SESSION['userLoggedIn'] = false;
    destroySession();
    echo '<div class="column middle"><p class="box-msg success">Logout successful. Redirecting to homepage...</p></div>';
}
header("refresh:1;url=index.php");
//redirect('index.php', '');
?>