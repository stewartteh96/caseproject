<?php
header("Content-Type:application/json");
require("tweet.php");

if(isset($_POST["query"])) {
  $keyword = $_POST["query"];
  $tweets = $twitter->get("https://api.twitter.com/1.1/search/tweets.json?q=".$keyword."&lang=en&result_type=recent&count=8");

  echo json_encode($tweets);
}
?>
