<?php
session_start();
include_once ('../utilities/functions.php');
include_once ('../utilities/db.php');
checkEnabledCookies();
if(isset($_SESSION['cookies']) && $_SESSION['cookies'] == false) {
    echo '<html lang="en">
            <head><title></title><link rel="stylesheet" type="text/css" href="../css/classes.css"></head>
            <body class="default">
            <h1 class="disabled-cookies">
                Attention! Cookies are disabled. Enable them to continue on the website.
            </h1></body></html>';
    exit();
}
checkHTTPS();
handleInactivity();
checkLogIn();

if(isset($_POST['purchase']) && isset($_SESSION['seats'])) {
    // ciclo su $_Session cercando colore giallo => check con db che sia ancora disponibile => acquisto => redirect con msg successful
    $conn = dbConnect();
    mysqli_begin_transaction($conn);
    mysqli_autocommit($conn, false);
    $success = true;
    $keys = array();
    $user = sanitizeString($conn, $_SESSION['user']);
    foreach ($_SESSION['seats'] as $key=>$value) {
        if ($value == 'yellow') {
            $keys[$key] = $value;
            $book_info = seatIsBooked($conn, $key);
            if(seatIsFree($conn, $key)) {   // free seat
                if($success)
                    buyNewSeat($conn, $key, $user);
            } else if ($book_info[0]==true && $book_info[1]==true) { // seat booked for current user
                if($success)
                    buyBookedSeat($conn, $key, $user);
            } else {
                $success = false;
            }
        }
    }
    if(!$success)
        mysqli_rollback($conn);

    // Edit session variables
    foreach ($keys as $key=>$value) {
        if($success)
            $_SESSION['seats'][$key] = 'red';
        else {
            removeBooking($key, $conn, $user);
            $_SESSION['seats'][$key] = 'green';
            if(!mysqli_commit($conn))
                die ("Ops! Something went wrong... Please repeat the operation.");
        }
    }

    if($success) {
        if (!mysqli_commit($conn)) {
            mysqli_rollback($conn);
            mysqli_autocommit($conn, true);
            die ("Ops! Something went wrong... Please repeat the operation.");
        }
    }

    closeConnection($conn);
    if ($success) {
        redirect('index.php', 'purchaseSuccessful');
    } else {
        redirect('index.php', 'purchaseFailed');
    }
} else {
    redirect('index.php', 'purchaseFailed');
}