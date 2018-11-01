<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <script src='../js/jquery-3.2.1.min.js'></script>
    <script src="../js/parsley.min.js"></script>
    <link href="../css/bootstrap.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
    
    <script type="text/javascript">
        (function () { //Janrain login
            if (typeof window.janrain !== 'object')
                window.janrain = {};

            if (typeof window.janrain.settings !== 'object')
                window.janrain.settings = {};

            janrain.settings.tokenUrl = 'http://localhost/cm0677-assignment/server/janrain/rpx.php'; //Location of janrainEngage folder

            function isReady() {
                janrain.ready = true;
            };

            if (document.addEventListener) {
                document.addEventListener("DOMContentLoaded", isReady, false);
            }
            else {
                window.attachEvent('onload', isReady);
            }

            var e = document.createElement('script');
            e.type = 'text/javascript';
            e.id = 'janrainAuthWidget';

            if (document.location.protocol === 'https:') {
                e.src = 'https://rpxnow.com/js/lib/sjm-app-rpxnow/engage.js';
            }
            else {
                e.src = 'http://widget-cdn.rpxnow.com/js/lib/sjm-app-rpxnow/engage.js';
            }

            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(e, s);
        })();
    </script>
</head>

<body>
    <header class='pageHeader'>
        <h1 class='pageTitle'>Coast City Sports Centre</h1>
    </header>

    <?php
    session_start();
    
    if (isset($_SESSION["profile"]) && $_SESSION["profile"]["loggedIn"]) { //User already logged in
        header("Location: ../index.php");
    }
    else { //Show login form
    ?>

    <form class="form-signin" method="post" data-parsley-validate>
        <h1 class="h3 mb-3 font-weight-normal">Please login: </h1>

        <label for="txtUsername" class="sr-only">Username</label>

        <input type="text" id="txtUsername" name="txtUsername" class="form-control" placeholder="Username" data-parsley-required="true"
        data-parsley-required-message="Username cannot be empty!" /> <br /><br />

        <label for="txtPassword" class="sr-only">Password</label>

        <input type="password" id="txtPassword" name="txtPassword" class="form-control" placeholder="Password" data-parsley-required="true"
        data-parsley-required-message="Password cannot be empty!" /> <br /><br />

        <button class="btn btn-lg btn-primary btn-block" name="btnLogin" type="submit">Login</button>

        <a class='janrainEngage' href='#'>Login using Social Media</a>
    </form>

    <?php
    }
    ?>
</body>

</html>

<?php
require_once("../db/recordset.class.php");

if (isset($_POST['btnLogin'])) { 
    //Obtain user input from textbox
    $username = filter_has_var(INPUT_POST, 'txtUsername') ? $_POST['txtUsername']: null;
    $password = filter_has_var(INPUT_POST, 'txtPassword') ? $_POST['txtPassword']: null;

    //Trim white space
    $username = trim($username);
    $password = trim($password);

    //Sanitize user input
    $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $password = filter_var($password, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $password = sha1($password);

    if ($username != "" && $password != "") { //Got both username & password
        $rs = new JSONRecordSet();
        $sql = "SELECT password FROM cc_admin WHERE cc_adminID = :username";
        $params = array(':username' => $username);
        $results = $rs->getRecordSet($sql, $params);
        $rowCount = $results[1];

        if ($rowCount > 0) { //User is an admin
            $dbPassword = $results[0][0]["password"]; //Password is stored in an associative array within an array

            if ($password == $dbPassword) { //Enrypted password matches database password
                $profileData = array(
                    "name" => $username,
                    "loggedIn" => true,
                    "userType" => "admin"
                );
                
                $_SESSION["profile"] = $profileData;

                $redirect = "http://localhost/cm0677-assignment/server/admin/admin.php";
                header("Location:" . $redirect);
            }
            else { //Passwords do not match
                echo "<script>alert('Incorrect username/password!')</script>";
            }
        }
        else { //Check if user is a member/does not exist in database
            $sql = "SELECT password, surname, forename FROM cc_member WHERE cc_memberID = :username";
            $params = array(':username' => $username);
            $results = $rs->getRecordSet($sql, $params);
            $rowCount = $results[1];

            if ($rowCount > 0) { //User is a member
                $dbPassword = $results[0][0]["password"];

                if ($password == $dbPassword) { //Enrypted password matches database password
                    $name = $results[0][0]["forename"] . " " . $results[0][0]["surname"];

                    $profileData = array(
                        "name" => $name,
                        "loggedIn" => true,
                        "userType" => "member"
                    );

                    $_SESSION["profile"] = $profileData;

                    $redirect = "http://localhost/cm0677-assignment/index.php";
                    header("Location:" . $redirect);
                } 
                else { //Passwords do not match
                    echo "<script>alert('Incorrect username/password!')</script>";
                }
            }
            else { //User does not exist in database
                echo "<script>alert('Incorrect username/password!')</script>";
            }
        }
    }
    else { //Username/password empty
        echo "<script>alert('Incorrect username/password!')</script>";
    }
}
?>