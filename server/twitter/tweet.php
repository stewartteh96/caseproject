<?php
require("../../oauth/twitteroauth.php");

$consumerKey = "AdbTr1QxgOVF1qkfvDW409KDh";
$consumerSecret = "2Ay70Ci69hVOdqxC8PVrJ5egymP4ejx2v0Oeebc98bSJ0it32c";
$accessToken = "288143225-B2mZFnX1WQgdFc6IwDYsgRxjfSci9gvcaKj95rWf";
$accessTokenSecret = "h51a6V6Mu1DpptUy73HFQXFiR8icStYzBn6D7rcEEGVWP";

$twitter = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
?>
