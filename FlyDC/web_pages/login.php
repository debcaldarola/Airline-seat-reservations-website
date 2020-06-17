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
        <header id="header-login">
            <h1 id="welcome-login" class="welcome">FlyDC
                <img src="../images/airplane.png" class="header-logo" alt="">
            </h1>
        </header>
        <div class="column side">
            <div class="nav_bar">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#about">About</a></li>
                </ul>
            </div>
        </div>

<?php
if(!empty($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'missingField':
            echo '<div class="column middle box-msg failure">Attention! Missing field.</div>';
            unset($_GET['msg']);
            break;
        case 'userNotExists':
            echo '<div class="column middle box-msg failure">Invalid username.</div>';
            unset($_GET['msg']);
            break;
        case 'wrongPsw':
            echo '<div class="column middle box-msg failure">Wrong password.</div>';
            unset($_GET['msg']);
            break;
        case 'success':
            echo '<div class="column middle box-msg success">Login successful. Redirecting to homepage...</div>';
            echo '</div></body></html>';
            unset($_GET['msg']);
            header("refresh:1;url=index.php");
            exit();
            break;
    }
}

if(isset($_SESSION['userLoggedIn']) && isset($_SESSION['user']) && $_SESSION['userLoggedIn']==true) {
    ?>
    <div class="user-loggedin">
        <p>Hi! You are already logged in.</p>
    </div>
    <?php
    echo '</div></body></html>';
    exit();
}
?>
        <br>
        <div class="signup">
            <h2 class="signup-header">Login</h2>
            <form action="check_login.php" method="post" class="signup-form" onsubmit="return checkLogin()">
                <table class="table-form">
                    <tr>
                        <td class="wrapper">
                            <label for="login-email">Username: </label>
                            <p class="text">Username must be a valid email address.</p>
                        </td>
                        <td><input type="email" id="login-email" name="login-email" maxlength="30"></td>
                    </tr>
                    <tr>
                        <td class="wrapper">
                            <label for="login-psw">Password: </label>
                            <p class="text">Password must contain at least a lower case character,<br>
                                and an upper case one or a number.</p>
                        </td>
                        <td><input type="password" id="login-psw" name="login-psw" maxlength="30"></td>
                    </tr>
                </table>
                <button type="submit" class="signup-button">LOGIN</button>
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