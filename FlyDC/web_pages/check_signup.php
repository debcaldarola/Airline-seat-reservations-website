<?php
session_start();
include_once ('../utilities/functions.php');
include_once ('../utilities/db.php');

checkEnabledCookies();
checkHTTPS();
handleInactivity();

if(!empty($_POST)) {
    if(isset($_POST['email']) && isset($_POST['psw']) && isset($_POST['confirm_psw'])
        && $_POST['email'] != "" && $_POST['psw'] != "" && $_POST['confirm_psw'] != "") {
        $email = $_POST['email'];
        $psw = $_POST['psw'];
        $confirmed_psw = $_POST['confirm_psw'];

        if(!checkValidEmail($email) || ($psw != $confirmed_psw) || !checkValidPsw($psw)) {
            /*echo '<html><link rel="stylesheet" type="text/css" href="../css/classes.css">
                    <div class="registration-failed">
                        <p>Attention! Invalid information</p></div></html>';*/
            redirect('sign_up.php', 'invalidInfo');
        }

        $conn = dbConnect();
        mysqli_begin_transaction($conn);
        mysqli_autocommit($conn, false);
        $email = sanitizeString($conn, $email);
        $hash = md5($psw);

        if(userAlreadyExists($conn, $email, true))
            redirect('sign_up.php', 'existingUser');
        insertUser($conn, $email, $hash);
        closeConnection($conn);
        redirect('sign_up.php', 'success');
    } else {
        redirect('sign_up.php', 'missingField');
    }
} else {
    redirect('sign_up.php', 'missingField');
}


