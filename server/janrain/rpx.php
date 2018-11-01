<?php
// Below is a very simple and verbose PHP script that implements the Engage
// token URL processing and some popular Pro/Enterprise examples. The code below
// assumes you have the CURL HTTP fetching library with SSL.
require('helpers.php');

ob_start();

// PATH_TO_API_KEY_FILE should contain a path to a plain text file containing
// only your API key. This file should exist in a path that can be read by your
// web server, but not publicly accessible to the Internet.
$janrain_api_key = trim(file_get_contents('apiKey.txt'));

// Set this to true if your application is Pro or Enterprise.
$social_login_pro = false;

// Step 1: Extract token POST parameter
$token = $_POST['token'];

if ($token) { 
  $post_data = array( // Step 2: Use the token to make the auth_info API call.
    'token' => $token,
    'apiKey' => $janrain_api_key,
    'format' => 'json'
  );

  if ($social_login_pro) {
    $post_data['extended'] = 'true';
  }

  $curl = curl_init();
  $url = 'https://rpxnow.com/api/v2/auth_info';
  $result = curl_helper_post($curl, $url, $post_data);

  if ($result == false) {
    curl_helper_error($curl, $url, $post_data);
    die();
  }
  curl_close($curl);

  $auth_info = json_decode($result, true); // Step 3: Parse the JSON auth_info response

  if ($auth_info['stat'] == 'ok') {
    echo "\n auth_info:";
    echo "\n"; var_dump($auth_info);

    // Pro and Enterprise API examples
    if ($social_login_pro) {
      include('social_login_pro_examples.php');
    }

    // Step 4: Your code goes here! Use the identifier in
    // $auth_info['profile']['identifier'] as the unique key to sign the
    // user into your system.

    // echo "<pre>" . print_r($auth_info) . "</pre>";

    session_start();
    $_SESSION["profile"] = "something";

    if (isset($_SESSION["profile"])) {
      unset($_SESSION["profile"]);

      $profileData = array(
        "name" => $auth_info["profile"]["displayName"],
        "loggedIn" => true,
        "userType" => "member"
      );
      $_SESSION["profile"] = $profileData;

      $redirect = "http://localhost/cm0677-assignment/index.php";
      header("Location:" . $redirect);
    }
  } 
  else { // Handle the auth_info error.
    output('An error occurred', $auth_info);
    output('result', $result);
  }
} //End if (token) is available
else { //No token obtained
  echo 'No authentication token.';
}

$debug_out = ob_get_contents();
ob_end_clean();
?>
