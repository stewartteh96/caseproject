<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Coast City Sports Centre</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script src="js/moment.min.js"></script>
</head>

<body>
    <?php
    session_start();
    
    echo "<header class='pageHeader'>";
    echo "<h1 class='pageTitle'>Coast City Sports Centre</h1>";
            
    if (isset($_SESSION["profile"]) && $_SESSION["profile"]["loggedIn"]) {
        echo "<div class='loginDiv'>";
        echo "<p>";
        echo "Welcome, " . $_SESSION["profile"]["name"] . "! &nbsp;";
        echo "<input type='button' value='Logout' onclick='location.href=\"server/logout.php\";' />";
        echo "</p>";
        echo "</div>";
    } 
    else {
        echo "<div class='loginDiv'>";
        echo "<input type='button' value='Login' onclick='location.href=\"./server/login.php\";' />";
        echo "</div>";
    }
    ?>

    </header>

    <div id='divWelcome'>
        <h3>Welcome to Coast City Sports Centre!</h3> 

        <p>We offer: </p>

        <p>- Various types of different facilities (i.e. swimming pool, gym, racket courts for tennis, squash and badminton)</p>

        <p>- Individualised membership deals varying in prices to suit your needs</p>

        <p>And many more!</p>

        <p style='font-weight: bold;'>Become a member and join the family!</p>
    </div>

    <div id='divWeather'>
        <p>Weather Forecast: </p>
    </div>

    <?php
    if (isset($_SESSION["profile"]) && $_SESSION["profile"]["loggedIn"]) {
    ?>
    <!-- Only accessible after login -->
    <div id='divTweet'>
        <fieldset>
            <legend>Post a Tweet</legend>

            <div id='divTweetMessage'>
                Tweet Message:
                <textarea id='tweetText' cols="40" rows="3"></textarea> 
                <input id='tweetBtn' type='button' value='Tweet' />
            </div> 

            <div id='divTokenKey'>
                Token Key: 
                <input id='tokenKey' style="width: 250px;" type='text' /> 
                <input id='requestBtn' type='button' value='Request Token' />
            </div>
        </fieldset>
    </div>

    <div id='divTweetSearch'>
        Search for Tweet: 
        <input id='keyword' style="width: 250px;" type='text' />
        <input id='search' type='button' value='Search' /> 
        <div id='tweetResult'></div>
        <div id='hoverdiv'></div>
    </div>

    <div id='mapCanvas'></div>
    <?php
    }
    else { //Hide map if user is not logged in
        echo "<div id='mapCanvas' style='display: none;'></div>";
    }
    ?>

    <script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyAzIrmEOVquLJKJaCOAV1YrpzcVgHZSn68'></script>
    <script src='js/jquery-3.2.1.min.js'></script>
    <script src='js/tweet.js'></script>
    <script src='js/weather.js'></script>
</body>

</html>