<?php
require_once("config.php");

class pdoDB { 
  private static $dbConnection = null; //Private static to hold the connection

  public static function getConnection() {
    if (!self::$dbConnection) { //If there isn't a connection already then create one
      try {
        //The constant DB isn't defined use the book database otherwise use the one that's defined
        if (!defined('DB')) { 
			    $db = new PDO(DRIVER . ":host=" . HOST . ";dbname=" . DATABASE, USERNAME, PASSWORD);
        }
        else {
          $db = DB;
        }
        self::$dbConnection = $db;
        self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch(PDOException $e) {
        //In a production system you would log the error not display it
        echo $e->getMessage(); //Hint: Do not echo error message, hackers can know the database used etc. Suggestion: save the error message in log file so developer can read
      }
    }
    //Return the connection
    return self::$dbConnection;
  }
}
?>
