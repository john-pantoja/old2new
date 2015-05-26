<?php
require("../src/isdk.php");

$app = new iSDK;
$app->enableLogging(1);
echo "created object!<br/>";

if ($app->cfgCon("APPNAME", "APIKEY")) {
    $total_execution_time = 0;
    $returnFields = array('Address1Type', 'Address2Street1', 'Address2Street2', 'Address2Type', 'Address3Street1', 'Address3Street2', 'Address3Type', 'Anniversary', 'AssistantName', 'AssistantPhone', 'BillingInformation', 'Birthday', 'City', 'City2', 'City3', 'Company', 'AccountId', 'CompanyID', 'ContactNotes', 'ContactType', 'Country', 'Country2', 'Country3', 'CreatedBy', 'DateCreated', 'Email', 'EmailAddress2', 'EmailAddress3', 'Fax1', 'Fax1Type', 'Fax2', 'Fax2Type', 'FirstName', 'Groups', 'Id', 'JobTitle', 'LastName', 'LastUpdated', 'LastUpdatedBy', 'Leadsource', 'LeadSourceId', 'MiddleName', 'Nickname', 'OwnerID', 'Password', 'Phone1', 'Phone1Ext', 'Phone1Type', 'Phone2', 'Phone2Ext', 'Phone2Type', 'Phone3', 'Phone3Ext', 'Phone3Type', 'Phone4', 'Phone4Ext', 'Phone4Type', 'Phone5', 'Phone5Ext', 'Phone5Type', 'PostalCode', 'PostalCode2', 'PostalCode3', 'ReferralCode', 'SpouseName', 'State', 'State2', 'State3', 'StreetAddress1', 'StreetAddress2', 'Suffix', 'Title', 'Username', 'Validated', 'Website', 'ZipFour1', 'ZipFour2', 'ZipFour3');
	$query = array('Id' => '%');
    $time_start = microtime(true);
    $time_end = microtime(true);
    $count = $app->dsCount("Contact", array('Id' => '%'));
    $limit = 1000;
    $pages = ceil($count / $limit);
    $page = 0;
    $results = array();
    $total_execution_time = 0;
    do {
        $time_start = microtime(true);
        $contacts = $app->dsQuery("Contact", $limit, $page++, $query, $returnFields);
        $time_end = microtime(true);
        foreach ($contacts as $contact) {
            $results[] = $contact;
        }

        //dividing with 60 will give the execution time in minutes other wise seconds
        $execution_time = ($time_end - $time_start);
        $total_execution_time = $total_execution_time + $execution_time;

        echo '<b>Total Execution Time for Page# ' . $page . '</b> ' . $execution_time . ' secs';

    } while ($page < $pages);
    echo "<pre>";
     if($total_execution_time >= 60){
         $total_execution_time = $total_execution_time/60;
     }
     echo '<b>Total Execution Time:</b> ' . $total_execution_time . ' secs';
    echo "<br />";
    print_r(($contacts));
    echo "</pre>";


} else {
    echo "connection failed!<br/>";
}

?>