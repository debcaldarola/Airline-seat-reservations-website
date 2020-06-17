<?php
include_once ('../utilities/functions.php');
session_start();
/*checkEnabledCookies();
if(isset($_SESSION['cookies']) && $_SESSION['cookies'] == false) {
    echo '<html lang="en">
            <head><title></title><link rel="stylesheet" type="text/css" href="../css/classes.css"></head>
            <body class="default">
            <h1 class="disabled-cookies">
                Attention! Cookies are disabled. Enable them to continue on the website.
            </h1></body></html>';
    exit();
}*/
checkHTTPS();
$inactive = checkInactivity();

if($inactive==true) {
    echo 'notLogged';
    exit();
}

if(!empty($_GET['seat']) && !empty($_SESSION) && !empty($_SESSION['seats'])) {
    $seat = $_GET['seat'];
    /*if ((chr($seat[0]) < 65 || chr($seat[0]) >= 65+N_COLUMNS) || ($seat[1] < 1 || $seat[1] > N_ROWS)) {
        echo '<p class="box-msg failure">Invalid seat number</p>';
        redirect('index.php', 'invalidRequest');
        exit();
    }*/
    $conn = dbConnect();
    $seat = sanitizeString($conn, $seat);
    mysqli_begin_transaction($conn);
    mysqli_autocommit($conn, false);
    if(seatIsFree($conn, $seat)) {
        $result = bookSeat($seat, $conn);
        if($result == false) {  // error
            mysqli_rollback($conn);
            mysqli_autocommit($conn, true);
            echo 'free';
        } else {
            $_SESSION['seats'][$seat] = 'yellow';
            echo 'my_booking';
        }
    } else {    // booked or bought
        $booking = seatIsBooked($conn, $seat);
        if ($booking[0] == true) {  // booked by someone (me or someone else)
            if ($booking[1] == true) {  // my booking => unselect booking
                $email = $booking[2];
                removeBooking($seat, $conn, $email);
                if (!mysqli_commit($conn)) {
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    die("Ops! Something went wrong...");
                }
                $_SESSION['seats'][$seat] = 'green';
                echo 'free';
            } else {    // someone else's booking => remove their booking and book for me
                $email = $booking[2];
                removeBooking($seat, $conn, $email);
                $result = bookSeat($seat, $conn);
                if($result == true) {  // booking successful
                    $_SESSION['seats'][$seat] = 'yellow';
                    echo 'my_booking';
                } else {  // error
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    echo 'booked';
                }
            }
        } else {    // bought
            mysqli_rollback($conn);
            mysqli_autocommit($conn, true);
            echo 'bought';
        }
    }
    closeConnection($conn);
} else
    redirect('index.php', 'invalidRequest');