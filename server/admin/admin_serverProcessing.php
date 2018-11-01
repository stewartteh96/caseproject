<?php
require_once("../../db/recordset.class.php");
header('content-type: application/json');

$function = filter_has_var(INPUT_POST, 'action') ? $_POST['action']: null;
$function = trim($function);
$function = filter_var($function, FILTER_SANITIZE_STRING);

if ($function == "loadAll") { //Load all members
    $memberListSQL = "SELECT cc_memberID, surname, forename, address, cc_gradeID FROM cc_member";
    $rs = new JSONRecordSet();
    $results = $rs->getRecordSet($memberListSQL);
    $rowCount = $results[1];
    $memberList = $results[0];

    echo json_encode($memberList, JSON_PRETTY_PRINT); //Convert results into JSON format
}
else if ($function == "updateDetails") { //Update member's details
    //Obtain passed value, trim whitespace & sanitize value
    $surname = filter_has_var(INPUT_POST, 'surname') ? $_POST['surname']: null;
    $surname = trim($surname);
    $surname = filter_var($surname, FILTER_SANITIZE_STRING);

    $forename = filter_has_var(INPUT_POST, 'forename') ? $_POST['forename']: null;
    $forename = trim($forename);
    $forename = filter_var($forename, FILTER_SANITIZE_STRING);

    $address = filter_has_var(INPUT_POST, 'address') ? $_POST['address']: null;
    $address = trim($address);
    $address = filter_var($address, FILTER_SANITIZE_STRING);

    $membership = filter_has_var(INPUT_POST, 'membership') ? $_POST['membership']: null;
    $membership = trim($membership);
    $membership = filter_var($membership, FILTER_SANITIZE_STRING);

    $cc_memberID = filter_has_var(INPUT_POST, 'memberID') ? $_POST['memberID']: null;
    $cc_memberID = trim($cc_memberID);
    $cc_memberID = filter_var($cc_memberID, FILTER_SANITIZE_STRING);

    if ($surname == "" || $forename == "" || $address == "" || $membership == "" || $cc_memberID == "") {
            echo json_encode("2"); //Certain parameters are empty
    }
    else { //Got all parameters, update database
        $rs = new JSONRecordSet();
        $updateSQL = "UPDATE cc_member SET surname = :surname, forename = :forename, address = :address, 
                    cc_gradeID = :membership WHERE cc_memberID = :cc_memberID";
        $params = array(":surname" => $surname, ":forename" => $forename, ":address" => $address, 
                        ":membership" => $membership, ":cc_memberID" => $cc_memberID);
        $results = $rs->getRecordSet($updateSQL, $params);
        $rowCount = $results[1];

        if ($rowCount > 0) { //Update successful
            echo json_encode("1");
        }
        else {
            echo json_encode("0");
        }
    }
} 
?>