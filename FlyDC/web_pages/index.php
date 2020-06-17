<?php
include_once '../utilities/functions.php';
include_once '../utilities/db.php';

session_start();
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

if(!empty($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'SessionTimeOut':
            echo "<script type='text/javascript'>alert('Session expired! Relog to continue')</script>";
            unset($_GET['msg']);
            header("refresh:1;url=login.php");
            break;
        case 'purchaseFailed':
            echo "<script type='text/javascript'>alert(\"Attention! Purchase failed: at least on of the selected seats is not available anymore.\");</script>";
            unset($_GET['msg']);
            break;
        case 'purchaseSuccessful':
            echo "<script type='text/javascript'>alert(\"Purchase successful!\");</script>";
            unset($_GET['msg']);
            break;
        case 'invalidRequest':
            echo "<script type='text/javascript'>alert('Invalid request to server')</script>";
            unset($_GET['msg']);
            break;
    }
}
if(isset($_GET['refresh'])) {
    if(empty($_SESSION) || (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn']==false)) {
        echo "<script type='text/javascript'>alert('Session expired! Relog to continue')</script>";
    }
    unset($_GET['refresh']);
}

if(isset($_SESSION['userLoggedIn']) && isset($_SESSION['user']) && $_SESSION['userLoggedIn']==true) {
    $user = $_SESSION['user'];
    $arr = explode("@", $user); // get name before '@' in email address
    $user_name = $arr[0];
    $loggedIn = true;
    $_SESSION['seats'] = array();
} else {
    $loggedIn = false;
    $user_name = "Guest";
    $_SESSION['userLoggedIn'] = false;
}

$welcome = "Welcome ".$user_name."!";
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
<?php
    if ($loggedIn) {
        echo '<ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="booking.php">My reservations</a></li>
                    <li><a href="#about">About</a></li>
              </ul>';
    } else {
        echo '<ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="sign_up.php">Sign up</a></li>
                <li><a href="#about">About</a></li>
            </ul>';
    }
?>
                </div>
            </div>
            <div class="column middle">
                <div>
                <?php
                echo'<p class="welcome-user">'.$welcome.'</p>';
                echo '<p id="flight-info">Flight '.FLIGHT_ID.'</p>';
                ?>
                </div>
                <p id="result"></p>
                <div class="flight-container" id="flight-cnt">
                    <table class="flight" id="flight">
                        <?php
                        $conn = dbConnect();
                        for($i=1; $i<=N_ROWS; $i++) {
                            $letter = 65;
                            echo '<tr>';
                            for($j=0; $j<N_COLUMNS; $j++) {
                                $seat = chr($letter).$i;
                                $color = checkStatus($seat, $conn);
                                if($loggedIn && isset($_SESSION['seats']))
                                    $_SESSION['seats'][$seat] = $color;
                                if($loggedIn)
                                    echo '<td class="flight logged" id="'.$seat.'" style="background-color:'.$color.';" onclick="changeStatus(id);">';
                                else
                                    echo '<td class="flight" id="'.$seat.'" style="background-color:'.$color.';" onclick="pleaseLogIn()">';
                                echo $seat;
                                echo '</td>';
                                $letter++;
                            }
                            echo '</tr>';
                        }
                        closeConnection($conn);
                        ?>
                    </table><br><br><br>
                </div>
            </div>
            <div class="column side">
                <?php
                if(!$loggedIn) {
                    // Get seats statistics
                    $conn = dbConnect();
                    $totSeats = getTotalSeats($conn);
                    $reserved = getReservedSeats($conn);
                    $booked = $reserved[0];
                    $bought = $reserved[1];
                    $free = $totSeats - $booked - $bought;
                    closeConnection($conn);

                    echo '<br><div id="stat">';
                    echo '<p>Total seats: '.$totSeats.'<br>';
                    echo '<p style="color: green; display: inline-block;">Available</p> seats: '.$free.'<br>';
                    echo '<p style="color: orange; display: inline-block;">Booked</p> seats: '.$booked.'<br>';
                    echo '<p style="color: red; display: inline-block;">Purchased</p> seats: '.$bought.'<br>';
                    echo '</p></div>';
                } else {
                    ?>
                    <br>
                    <div id="buttons">
                        <form action="index.php?msg=refresh">
                            <button type="submit" class="home-button" name="refresh">REFRESH</button>
                        </form>
                        <form action="purchase.php" method="post">
                            <button type="submit" class="home-button" name="purchase">PURCHASE</button>
                        </form>
                    </div>
                <?php
                }
                ?>
            </div>

        </div>
        <footer class="footer"><a id="about"></a>
            <p>Website created by Debora Caldarola s263626<br>
                Programmazione Distribuita I<br>
                Politecnico di Torino<br></p>
        </footer>
    </body>
    </html>
