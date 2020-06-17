<?php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 's263626');
define('DB_PSW', 'linfizil');
define('DB_NAME', 's263626');

define('N_ROWS', 10);
define('N_COLUMNS', 6);

define('FLIGHT_ID', 'DC123');
define('MODEL_ID', 'AAAAA');

define('SALE', 'dkowqpnanheuiaklsafonnebeurspfnej');

include_once ('functions.php');

/*$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PSW, DB_NAME);
if(mysqli_connect_error()) { die("Fatal error"); }*/

function dbConnect() {
    $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PSW, DB_NAME);
    if(mysqli_connect_error()) {
        die("Ops! Something happened...");
    }
    return $conn;
}

function mysqlQuery($conn, $query) {
    $ris = mysqli_query($conn, $query);
    if ($ris==false) {
        echo mysqli_error($conn);
        mysqli_rollback($conn);
        die("Fatal error: ".mysqli_error($conn));
    }
    return $ris;
}

function userAlreadyExists($conn, $email, $forupdate) {
    $exists = false;

    $sql = "SELECT COUNT(*) as total FROM users WHERE email='".$email."'";
    if($forupdate)
        $sql = $sql." FOR UPDATE";
    $ris = mysqlQuery($conn, $sql);
    $data = mysqli_fetch_assoc($ris);
    if($data['total'] > 0)
        $exists = true;
    mysqli_free_result($ris);
    if($exists)
        return true;
    else return false;
}

function insertUser($conn, $email, $hash) {
    $length = 5;
    try {
        if (function_exists(random_bytes($length))) {
            $sale = bin2hex(random_bytes($length));
        } else
            $sale = substr(str_shuffle(SALE), 0, 10);
    } catch (Exception $e) {
        $sale = substr(str_shuffle(SALE), 0, 10);
    }
    $psw = $hash.$sale;
    $sql = "INSERT INTO users(email, psw) VALUES('$email', '$psw')";
    $ris = mysqlQuery($conn, $sql);
    if (!mysqli_commit($conn))
        die("Ops! Something went wrong...");
    //mysqli_free_result($ris);
}

function closeConnection($conn) {
    mysqli_close($conn);
}

function pswIsCorrect($conn, $hash, $email) {

    $sql = "SELECT psw FROM users WHERE email='".$email."'";
    $ris = mysqlQuery($conn, $sql);
    if(mysqli_num_rows($ris) > 0) {
        $row = mysqli_fetch_row($ris);
        $psw = $row[0];
        mysqli_free_result($ris);
        $psw = substr($psw, 0, -10); // remove last 10 characters (sale)
        if ($psw === $hash)
            return true;
        else return false;
    } else {
        mysqli_free_result($ris);
        die("Ops! Something went wrong...");
    }
}

function seatIsFree($conn, $seat) {
    $free = false;
    $sql = "SELECT COUNT(*) as total FROM reservations WHERE seatID='".$seat."' AND flightID='".FLIGHT_ID."' FOR UPDATE";
    $ris = mysqlQuery($conn, $sql);
    if(mysqli_num_rows($ris) > 0) {
        $data = mysqli_fetch_assoc($ris);
        if($data['total'] == 0)
            $free = true;
    }
    mysqli_free_result($ris);
    return $free;
}

function seatIsBooked($conn, $seat) {
    $booked = false;
    $myBooking = false;
    $user = "";
    $sql = "SELECT email, paid FROM reservations WHERE seatID='".$seat."'AND flightID='".FLIGHT_ID."' FOR UPDATE";
    $ris = mysqlQuery($conn, $sql);
    if(mysqli_num_rows($ris) > 0) {
        $row = mysqli_fetch_row($ris);
        $user = $row[0];
        if(!empty($_SESSION['user']) && $user == $_SESSION['user'])
            $myBooking = true;
        $paid = $row[1];
        if($paid == 0)
            $booked = true;
    }
    mysqli_free_result($ris);
    return array($booked, $myBooking, $user);
}

function bookSeat($seat, $conn) {
    if(!empty($_SESSION['user'])) {
        $email = sanitizeString($conn, $_SESSION['user']);
        $sql = "INSERT INTO reservations(email, flightID, seatID, paid) VALUES('".$email."', '".FLIGHT_ID."', '".$seat."', 0)";
        $ris = mysqlQuery($conn, $sql);
        if (!mysqli_commit($conn)) {
            die("Ops! Something went wrong...");
        }
        //mysqli_free_result($ris);
        return true;
    } else return false;
}

function removeBooking($seat, $conn, $email) {
        $sql = "DELETE FROM reservations WHERE flightID='".FLIGHT_ID."' AND seatID='".$seat."' AND email='".$email."'";
        mysqlQuery($conn, $sql);
        /*if (!mysqli_commit($conn)) {
            die("Ops! Something went wrong...");
        }*/
}

function buyNewSeat($conn, $seat, $email) {
    $sql = "INSERT INTO reservations(email, flightID, seatID, paid) VALUES('".$email."', '".FLIGHT_ID."', '".$seat."', 1)";
    mysqlQuery($conn, $sql);
}

function buyBookedSeat($conn, $seat, $email) {
    $sql = "UPDATE reservations SET paid=1 WHERE email='".$email."' AND flightID='".FLIGHT_ID."' AND seatID='".$seat."'";
    mysqlQuery($conn, $sql);
}

function getTotalSeats($conn) {
    $sql = "SELECT totSeats FROM flights WHERE flightID='".FLIGHT_ID."' AND modelID='".MODEL_ID."'";
    $ris = mysqlQuery($conn, $sql);
    if(mysqli_num_rows($ris) > 0) {
        $row = mysqli_fetch_row($ris);
        $tot = $row[0];
    } else $tot = 0;
    mysqli_free_result($ris);
    return $tot;
}

function getReservedSeats($conn) {
    $sql = "SELECT COUNT(*) FROM reservations WHERE paid=0 AND flightID='".FLIGHT_ID."'";
    $ris = mysqlQuery($conn, $sql);
    $sql = "SELECT COUNT(*) FROM reservations WHERE paid=1 AND flightID='".FLIGHT_ID."'";
    $ris2 = mysqlQuery($conn, $sql);
    if(mysqli_num_rows($ris) > 0) {
        $row = mysqli_fetch_row($ris);
        $booked = $row[0];
    } else $booked = 0;
    if(mysqli_num_rows($ris2) > 0) {
        $row = mysqli_fetch_row($ris2);
        $bought = $row[0];
    } else $bought = 0;
    mysqli_free_result($ris);
    mysqli_free_result($ris2);
    return array($booked, $bought);
}

function getReservations($conn, $email) {
    $sql = "SELECT seatID, paid FROM reservations WHERE email='".$email."' AND flightID='".FLIGHT_ID."'";
    $ris = mysqlQuery($conn, $sql);
    $reserv = array();
    $i = 0;
    if(mysqli_num_rows($ris) > 0) {
        while($row = mysqli_fetch_assoc($ris)) {
            $reserv[$i] = array();
            $reserv[$i][0] = $row["seatID"];
            $reserv[$i][1] = $row["paid"];
            $i++;
        }
    }
    mysqli_free_result($ris);
    return $reserv;
}
