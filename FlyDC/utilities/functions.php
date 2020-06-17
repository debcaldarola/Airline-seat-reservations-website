<?php
include_once ('db.php');

function redirect($url, $msg){
    header('HTTP/1.1 307 temporary redirect');
    if ($msg != '')
        header("Location: ".$url."?msg=".urlencode($msg));
    else
        header("Location: ".$url);
    exit;
}

function checkEnabledCookies() {
    setcookie('test', 1, time()+3600);
    if (count($_COOKIE)<=0) {
        $_SESSION['cookies'] = false;
    } else
        $_SESSION['cookies'] = true;
}

function handleInactivity() {
    $t = time();

    if (isset($_SESSION['time']) && isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn']==true) {
        $t0 = $_SESSION['time'];
        $inactivity = $t-$t0;
        if($inactivity > 120) {
            //azzero contenuto di $_SESSION
            $_SESSION = array();
            if(ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                // settare cookie scaduto
                setcookie(session_name(), '', time()-7*24*3600, $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]);
            }
            session_destroy();
            echo "<script type='text/javascript'>alert('Session expired! Relog to continue')</script>";
            // redirect homepage
            redirect("index.php", "SessionTimeOut");
            exit;
        } else {
            // update ultimo accesso
            $_SESSION['time'] = $t;
        }
    }
}

function destroySession() {
    if (session_id() != "" || isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
}

function checkHTTPS() {
    if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
        // Redirect su HTTPS
        $redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: '.$redirect);
        exit();
    }
}

function checkLogIn() {
    if(!isset($_SESSION) || (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn']==false)) {
        redirect('index.php', 'SessionTimeOut');
        exit();
    }
}

function checkInactivity() {
    $t = time();

    if (isset($_SESSION['time']) && isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn']==true) {
        $t0 = $_SESSION['time'];
        $inactivity = $t - $t0;
        if ($inactivity > 120) {
            $_SESSION = array();
            destroySession();
            return true;
        }
    }
    return false;
}

function sanitizeString($conn, $str) {
    $str = strip_tags($str);
    $str = htmlentities($str);
    if (get_magic_quotes_gpc())
        $str = stripslashes($str);
    $str = mysqli_real_escape_string($conn, $str);
    return $str;
}

function checkValidEmail($email) {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        return false;
    return true;
}

function checkValidPsw($psw) {
    $lowerFound = false;
    $upperOrNumber = false;

    for($i=0; $i<strlen($psw) && (!$lowerFound || !$upperOrNumber); $i++) {
        $c = $psw[$i];
        if(ord($c) >= 97 && ord($c) <= 122)
            $lowerFound = true;
        else if ((ord($c) >= 65 && ord($c) <= 90) || (ord($c) >= 48 && ord($c) <= 57))
            $upperOrNumber = true;
    }
    if(!$lowerFound || !$upperOrNumber) {
        return false;
    }
    return true;
}

function checkStatus($seat, $conn) {
    if(seatIsFree($conn, $seat)) {
        return 'lightgreen';
    } else {
        $res = array();
        $res = seatIsBooked($conn, $seat);
        if($res[0] == true) {   // booked
            if($res[1] == true) // my booking
                return 'yellow';
            else
                return 'orange';
        } else {
            return 'indianred';
        }
    }
}


