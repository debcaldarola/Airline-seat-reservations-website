var AjaxReq;
var id;

function checkSignUp() {
    var email = document.getElementById("email").value;
    var psw = document.getElementById("psw").value;
    var confirmed = document.getElementById("confirm_psw").value;

    regex = new RegExp('^[^@]+@[^@]+\\.[^@]+$');
    /* any char except '@' (one or more), followed by '@', one or more char except '@', '.', any char except '@', end line */
    if(!regex.test(email)) {
        window.alert("Wrong email address!");
        return false;
    }

    lowerFound = false;
    upperOrNumber = false;

    for(i=0; i<psw.length && (!lowerFound || !upperOrNumber); i++) {
        c = psw.charAt(i);
        if(c.charCodeAt(0) >= 97 && c.charCodeAt(0) <= 122)
            lowerFound = true;
        else if ((c.charCodeAt(0) >= 65 && c.charCodeAt(0) <= 90) || (c.charCodeAt(0) >= 48 && c.charCodeAt(0) <= 57))
            upperOrNumber = true;
    }
    if(!lowerFound || !upperOrNumber) {
        window.alert("Wrong password: at least a lower case character and an upper case one or a number are required");
        return false;
    }

    if(psw !== confirmed) {
        window.alert("Confirmed password does not match inserted password!");
        return false;
    }

    return true;
}

function checkLogin() {
    var email = document.getElementById("login-email").value;
    var psw = document.getElementById("login-psw").value;

    regex = new RegExp('^[^@]+@[^@]+\\.[^@]+$');
    /* any char except '@' (one or more), followed by '@', one or more char except '@', '.', any char except '@', end line */
    if(!regex.test(email)) {
        window.alert("Invalid email address!");
        return false;
    }

    lowerFound = false;
    upperOrNumber = false;

    for(i=0; i<psw.length && (!lowerFound || !upperOrNumber); i++) {
        c = psw.charAt(i);
        if(c.charCodeAt(0) >= 97 && c.charCodeAt(0) <= 122)
            lowerFound = true;
        else if ((c.charCodeAt(0) >= 65 && c.charCodeAt(0) <= 90) || (c.charCodeAt(0) >= 48 && c.charCodeAt(0) <= 57))
            upperOrNumber = true;
    }
    if(!lowerFound || !upperOrNumber) {
        window.alert("Invalid password!");
        return false;
    }
}

function ajaxRequest() {
    try {   // IE browser?
        var request = new XMLHttpRequest();
    } catch (e1) {  // no
        try {   // IE 6+?
            request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e2) {  // no
            try {   // IE 5?
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e3) {  // No AJAX support
                request = false;
            }
        }
    }
    return request;
}

// Handler
function f(){
    if (AjaxReq.readyState==4 && (AjaxReq.status==0 || AjaxReq.status==200)) {
        status = AjaxReq.responseText;
        switch (status) {
            case 'free':
                document.getElementById(id).style.background = "lightgreen";
                document.getElementById("result").innerHTML = "<p style='color: green'>Reservation removed</p>";
                setTimeout(function(){
                    document.getElementById("result").innerHTML = '';
                }, 1000);
                break;
            case 'booked':
                document.getElementById(id).style.background = "orange";
                break;
            case 'bought':
                document.getElementById(id).style.background = "indianred";
                document.getElementById("result").innerHTML = "<p style='color: red'>Attention: unavailable seat!</p>";
                setTimeout(function(){
                    document.getElementById("result").innerHTML = '';
                }, 1000);
                break;
            case 'my_booking':
                document.getElementById(id).style.background = "yellow";
                document.getElementById("result").innerHTML = "<p style='color: green'>Reservation succeeded</p>";
                setTimeout(function(){
                    document.getElementById("result").innerHTML = '';
                }, 1000);
                break;
            case 'notLogged':
                document.location.replace('index.php?msg=SessionTimeOut');
                break;
        }
    }
}

function startAjax(url) {
    AjaxReq = ajaxRequest();
    if (AjaxReq == false) {
        document.getElementById(id).innerHTML = "Sorry, your browser does not support Ajax";
    }
    AjaxReq.onreadystatechange = f;
    AjaxReq.open("GET", url, true);     // true => asincrono
    AjaxReq.send();
}

function changeStatus(seat) {
    id = seat;
    startAjax("check_seat.php?seat="+seat);
}

function pleaseLogIn() {
    window.alert("Please log in to book a seat!");
}