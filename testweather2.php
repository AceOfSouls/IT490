#!/usr/bin/php
<?php
    $zipCode = "07103";
    $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = "select * from weather.forecast where woeid in (select woeid from geo.places(1) where text={$zipCode})";
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";

    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);

    // Convert JSON to PHP object
     $phpObj =  json_decode($json);
    var_dump($phpObj);

    $originalDate = "20181031";
    $Date = date("d M Y", strtotime($originalDate));

    $result = "";
    for ($x = 0; $x <= 9; $x++)
    {
        $s = $phpObj->query->results->channel->item->forecast[$x]->date;
        if ($Date == $s)
        {
	    $s = $phpObj->query->results->channel->item->forecast[$x]->text;
            $result = $s;
        }
    }
    if ($result != "")
    {
        echo $result;
	return $result;
    }
    else
    {
        $result = "No weather at this time";
	echo $result;
	return $result;
    } 
?>
