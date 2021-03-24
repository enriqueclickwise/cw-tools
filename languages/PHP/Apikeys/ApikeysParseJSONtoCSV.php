<?php
include_once 'csvexporter.php';
    $apikeysfile = 'apikeys.json';
    $apikeysfilecontent = file_get_contents($apikeysfile);
    $apikeysarray = json_decode($apikeysfilecontent);
    // print_r($apikeysarray);
    $columnsvars = array('Token','Refid');
    $filedate = date("Ymd");
    $filename = 'apikeys/'.$filedate."_apikeys.csv";
    $linecounter = 0;
    foreach($apikeysarray as $apikey){
        $refid = $apikey->key;
        $apikey = $apikey->apikey;
        $vars = array($apikey, $refid);
        exportcsv($columnsvars, $vars, $filename, $linecounter);
        $linecounter++;
    }
    echo 'Total lines: '.$linecounter.PHP_EOL;
?>