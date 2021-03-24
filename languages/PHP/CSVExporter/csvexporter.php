<?php
    include_once '../libs/Curl.php';
    $csv = 'commissions.csv';
    exportCSV($csv);
    function exportCSV($csv){
        // var_dump($csv);
        $data = file_get_contents($csv);
        $transactions = explode(PHP_EOL, $data);
        unset($transactions[0]);
        // var_dump($transactions);
        $transactioncounter = 0;
        foreach ($transactions as $transaction){
            $action = explode(';', $transaction);
            $campaignid = $action[0];
            $orderid = $action[1];
            $totalcost = round(str_replace(',', '.', $action[2]), 2);
            $commission = round(str_replace(',', '.', $action[3]), 2);
            $date = $action[4];
            $refid = substr($action[5], 0, 13);
            $extradata = $action[6];
            $countrycode = $action[7];
            $currency = 'USD';
            $url = 'http://track.clickwise.net/pb?CountryCode='.$countrycode.'&TotalCost='.$totalcost.'&OrderId='.$orderid.'&Commission='.$commission.'&CampaignID='.$campaignid.'&RefId='.$refid.'&Date='.$date.'&Currency='.$currency.'&ExtraData1='.$extradata;
            if($campaignid != ''){
                echo $url.PHP_EOL;
                // $s2s = new Curl($url);
                // $s2s->request("GET");
                $transactioncounter++;
            }
        }
        echo 'Número de conversiones: '.$transactioncounter.PHP_EOL;
    }
?>