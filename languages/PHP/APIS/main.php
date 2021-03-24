<?php
    include_once 'API.php';
    // set_include_path('/srv/apis/libs');
    set_include_path('../libs');
    include_once 'Curl.php';

    function API($confname, $dateformat, $apiname, $requestformat, $globalurl){
        echo PHP_EOL.'=============================================================================='.PHP_EOL;
        $campaigncounter = 0;
        $api = new API($confname, $dateformat, $apiname);
        $credentials = $api->setConf($confname);
        $formatteddates = $api->setDate($dateformat);
        $startdate = $formatteddates[0];
        $urlsandcampaignids = $api->setAPIURL($credentials, $formatteddates, $apiname);
        $urls = $urlsandcampaignids[0];
        $campaignsinfo = $urlsandcampaignids[1];
        $merchantsinfo = $urlsandcampaignids[2];
        $merchantkeys = array_keys($merchantsinfo);
        foreach ($campaignsinfo as $campaignid => $actioncodes){
            $bonusaffs = $urlsandcampaignids[3][$campaignid];
            $url = $urls[$campaigncounter];
            $merchantid = $merchantkeys[$campaigncounter];
            $merchantname = $merchantsinfo[$merchantid];
            echo PHP_EOL.'API: '.$apiname.PHP_EOL.'Campaign: '.$merchantname.PHP_EOL.'Campaign ID: '.$campaignid.PHP_EOL.'URL: '.$url.PHP_EOL.'From date: '.urldecode($startdate).' to today'.PHP_EOL.PHP_EOL;
            if($globalurl == 'true'){
                $data = $api->getTransactions($url, $requestformat, $startdate);
                $globalurl = '';
            }
            if($globalurl == 'false'){
                $data = $api->getTransactions($url, $requestformat, $startdate);
            }
            $variables = $api->setTrackingVariables($data, $apiname, $campaignid, $bonusaffs);
            $trackingurl = $api->setTrackingURL($apiname, $merchantname, $variables, $actioncodes, $campaignid, $merchantid);
            $campaigncounter++;
            echo '=============================================================================='.PHP_EOL;
        }
    }
?>