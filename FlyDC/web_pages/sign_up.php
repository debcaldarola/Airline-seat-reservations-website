<?php
include_once ('../utilities/functions.php');
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
handleInactivity();
checkHTTPS();
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
    <div class="container">
        <header id="header-signup">
            <h1 id="welcome-signup" class="welcome">FlyDC
                <img src="../images/airplane.png" class="header-logo" alt="">
            </h1>
        </header>
        <div class="column side">
            <div class="nav_bar">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="#about">About</a></li>
                </ul>
            </div>
        </div>

<?php

if(!empty($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'invalidInfo': /*echo '<script type="text/javascript">alert("Attention! Invalid information")</script>';
                            echo '<noscript>Sorry: Your browser does not support or has disabled javascript.</noscript>';*/
                            echo '<div class="column middle box-msg failure">Attention! Invalid information.</div>';
                            unset($_GET['msg']);
                            break;
        case 'missingField': /*echo '<script type="text/javascript">alert("Attention! Missing field")</script>';
                            echo '<noscript>Sorry: Your browser does not support or has disabled javascript.</noscript>';*/
                            echo '<div class="column middle box-msg failure">Attention! Missing field!</div>';
                            unset($_GET['msg']);
                            break;
        case 'existingUser': /*echo '<script type="text/javascript">alert("Attention! Username already in use")</script>';
                            echo '<noscript>Sorry: Your browser does not support or has disabled javascript.</noscript>';*/
                            echo '<div class="column middle box-msg failure">Attention! Username already in use.</div>';
                            unset($_GET['msg']);
                            break;
        case 'success': echo '<div class="box-msg success column middle">Registration successful! Please log in <a href="login.php">here</a>.</div>';
                        unset($_GET['msg']);
                        exit();
                        //redirect('login.php', '');
                        break;
    }
}

if(isset($_SESSION['userLoggedIn']) && isset($_SESSION['user']) && $_SESSION['userLoggedIn']==true) {
?>
    <div class="user-loggedin">
        <p>Hi! You are already logged in. Go back to homepage or proceed to logout in order to register a new account.</p>
    </div>
<?php
    echo '</body></html>';
    exit();
}
?>
        <br>
        <div class="signup">
            <h2 class="signup-header">Sign up</h2>
            <form action="check_signup.php" method="post" class="signup-form" onsubmit="return checkSignUp()">
                <table class="table-form">
                    <tr>
                        <td class="wrapper">
                            <label for="email">Username: </label>
                            <p class="text">Username must be a valid email address.</p>
                        </td>
                        <td><input type="email" id="email" name="email" maxlength="30"></td>
                    </tr>
                    <tr>
                        <td class="wrapper">
                            <label for="psw">Password: </label>
                            <p class="text">Password must contain at least a lower case character,<br>
                                and an upper case one or a number.</p>
                        </td>
                        <td><input type="password" id="psw" name="psw" maxlength="30"></td>
                    </tr>
                    <tr>
                        <td><label for="confirm_psw">Confirm password: </label></td>
                        <td><input type="password" id="confirm_psw" name="confirm_psw" maxlength="30"></td>
                    </tr>
                </table>
                <button type="submit" class="signup-button">SIGN UP</button>
            </form>
        </div>
    </div>
    <footer class="footer"><a id="about"></a>
        <p>Website created by Debora Caldarola s263626<br>
            Programmazione Distribuita I<br>
            Politecnico di Torino<br></p>
    </footer>
</body>
</html>
