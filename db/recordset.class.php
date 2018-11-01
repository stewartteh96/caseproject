<?php
include 'pdoDB.class.php';
/**
* abstract super that creates a database connection and returns a record set
* @author Rob Davis
*
*/
abstract class R_RecordSet {
  protected $db;
  protected $stmt;

  function __construct() {
    $this->db = pdoDB::getConnection();
  }

  /**
  * @param string $sql    The sql for the recordset
  * @param array $params  An optional associative array if you want a prepared statement
  * @return PDO_STATEMENT
  */
  function getRecordSet($sql, $params = null) {
    if (is_array($params)) {
      $this->stmt = $this->db->prepare($sql);
      // execute the statement passing in the named placeholder and the value it'll have
      $this->stmt->execute($params);
    }
    else {
      $this->stmt = $this->db->query($sql);
    }
    return $this->stmt;
  }
}

/**
* specialisation class that returns a record set as an json string
* @author Rob Davis
*/
class JSONRecordSet extends R_RecordSet {
  /**
  * function to return a record set as a json encoded string
  * @param $sql         string with sql to execute to retrieve the record set
  * @param $params      is an array that, if passed, is used for prepared statements, it should be an assoc array of param name => value
  * @return string      a json object showing the status, number of records and the records themselves if there are any
  */
  function getRecordSet($sql, $params = null) {
    $stmt = parent::getRecordSet($sql, $params);

    //Check for occurance of SELECT (I.e. Indicating that it is a select statement)
    //Note the use of ===.  Simply == would not work as expected
    if (strpos(strtoupper($sql), 'SELECT') === false) { //Get rowCount() if sql statement involves INSERT, UPDATE, DELETE
      $nRecords = $stmt->rowCount(); //rowCount > 0 = sql statement successfully executed
      $recordSet = "";
    }
    else {
      $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC); //NOTE: fetchAll() will return 0 for INSERT, UPDATE, DELETE statements as nothing is retrieved
      $nRecords = count($recordSet);
    }
    
    $result = array($recordSet, $nRecords);
    return $result;
  }
}
?>
