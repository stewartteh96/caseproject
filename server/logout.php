<?php
session_start();

if (isset($_SESSION["profile"]) && $_SESSION["profile"]["loggedIn"]) {
    unset($_SESSION["profile"]);
}

header("Location: ../index.php");
?>
