<?php
    include_once 'main.php';
    include_once 'API.php';

    $confname = 'confs.json';
    function runAPI($confname){
        $confs = new API($confname);
        $credentials = $confs->setConf($confname);
        foreach($credentials as $apiconf){
            $confname = $apiconf->path;
            $dateformat = $apiconf->dateformat;
            $apiname = $apiconf->apiname;
            $requestformat = $apiconf->requestformat;
            $globalurl = $apiconf->globalurl;
            API($confname, $dateformat, $apiname, $requestformat, $globalurl);
        }
    }
    runAPI($confname);
?>