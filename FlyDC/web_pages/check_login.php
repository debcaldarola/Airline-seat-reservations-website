<?php
session_start();
include_once ('../utilities/functions.php');
include_once ('../utilities/db.php');

checkEnabledCookies();
checkHTTPS();
handleInactivity();

if(!empty($_POST)) {
    if(isset($_POST['login-email']) && isset($_POST['login-psw']) && $_POST['login-email']!="" && $_POST['login-psw']!="") {
        $email = $_POST['login-email'];
        $psw = $_POST['login-psw'];

        $conn = dbConnect();
        $email = sanitizeString($conn, $email);
        $hash = md5($psw);

        if(!userAlreadyExists($conn, $email, false))
            redirect('login.php', 'userNotExists');

        if(!pswIsCorrect($conn, $hash, $email))
            redirect('login.php', 'wrongPsw');

        closeConnection($conn);

        $_SESSION['userLoggedIn'] = true;
        $_SESSION['user'] = $email;
        $_SESSION['time'] = time();
        redirect('login.php', 'success');

    } else
        redirect('login.php', 'missingField');
} else
    redirect('login.php', 'missingField');