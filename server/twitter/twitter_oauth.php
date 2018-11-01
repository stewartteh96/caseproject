<?php
require("../../oauth/twitteroauth.php");
require("config.php");
session_start();

//Get 'oauth_verifier' & 'tweetText' from $.get parameters
//Get 'oauth_token_secret' & 'oauth_token_secret' from session passed from twitter_login.php
if(!empty($_GET['oauth_verifier']) && !empty($_GET['tweetText']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])){
  // We've got everything we need
  $tweetText= $_GET['tweetText'];
} 
else { // Something's missing, go back to square 1
  header('Location: twitter_login.php');
}

// TwitterOAuth instance, with two new parameters we got in twitter_login.php
$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']); // Let's request the access token
$_SESSION['access_token'] = $access_token; // Save it in a session var
$user_info = $twitteroauth->get('account/verify_credentials'); // Let's get the user's info

if(isset($user_info->error)) { // Something's wrong, go back to square 1
  header('Location: twitter_login.php');
} 
else {
  if(strlen($tweetText) == 0) {
    echo 'Tweet failed because length is 0';
    header('Location: member.php?action=tweetNotSuccessful');
  }
  else{
    $result = $twitteroauth->post('statuses/update', array('status' => $tweetText));
    if (isset($result->error)) { // Tweet failed
      echo 'Tweet to be updated. Error message:'. $result->error;
      //		header('Location: member.php?action=tweetNotSuccessful');
    } 
    else { // Tweet posted successfully, and $result contains the tweet data
      echo $result->text . '<br />Tweeted by @' . $result->user->screen_name;
      //		header('Location: member.php?action=tweetSuccessful');
    }
  }
}
?>
