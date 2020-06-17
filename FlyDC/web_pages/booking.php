<?php
session_start();
include_once '../utilities/functions.php';
include_once '../utilities/db.php';
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

if(isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] == true && isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $arr = explode("@", $user); // get name before '@' in email address
    $user_name = $arr[0];
}
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>FlyDC - Home</title>
        <link rel="icon" href="../images/airplane.ico">
        <!-- CSS files -->
        <link rel="stylesheet" type="text/css" href="../css/home.css">
        <link rel="stylesheet" type="text/css" href="../css/navbar.css">
        <link rel="stylesheet" type="text/css" href="../css/classes.css">
        <!-- Javascript, Ajax -->
        <script type="text/javascript" src="../js/functions.js"></script>
        <noscript>
            Sorry: Your browser does not support or has disabled javascript.
        </noscript>
    </head>
    <body class="default">
        <div class="container">
            <header id="header">
                <h1 id="welcome" class="welcome">FlyDC
                    <img src="../images/airplane.png" class="header-logo">
                </h1>
            </header>
            <div class="column side">
                <div class="nav_bar">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="booking.php">My reservations</a></li>
                        <li><a>About</a></li>
                    </ul>
                </div>
            </div>
            <div class="column middle">
                <?php
                echo'<p class="welcome-user">Hi, '.$user_name.'! Your reservations:</p>';
                $conn = dbConnect();
                $res = getReservations($conn, $user);
                closeConnection($conn);
                $length = count($res);
                if ($length == 0) {
                    echo '<p>Ops! It looks like you have made no reservations for this flight.</p>';
                    echo '</div></div></body></html>';
                    exit();
                }
                echo '<table class="reservations">';
                echo '<th>Seat</th><th>Status</th>';
                for($i=0; $i<$length; $i++) {
                    echo '<tr>';
                    echo '<td class="res_td">'.$res[$i][0].'</td>';
                    if ($res[$i][1] == 1)
                        echo '<td class="res_td">Purchased</td>';
                    else
                        echo '<td class="res_td">Booked</td>';
                    echo '</tr>';
                }
                echo '</table>';
                ?>
            </div>
        </div>
    </body>
</html>
