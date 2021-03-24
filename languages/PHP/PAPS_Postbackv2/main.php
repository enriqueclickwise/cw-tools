<?php
    function setConf($confname){
        $confpath = dirname(__FILE__).'/confs/'.$confname;
        $confile = file_exists($confpath) or exit('File '.$confpath.' does not exist.');
        $json = file_get_contents($confpath);
        $credentials = json_decode($json);
        // print_r($credentials); //-> To see all the credentials from config files
        return $credentials;
    }

    function setDate($dateformat){
        switch ($dateformat){
            case 'Y-m-dTH:i:sZ':
                $dateformat = 'Y-m-d H:i:s';
                $today = date($dateformat);
                $yesterday = date($dateformat, strtotime('-2 day'));
                $startdate = urlencode(substr($yesterday, 0, 10).'T'.substr($yesterday, 11).'Z');
                $enddate = urlencode(substr($today, 0, 10).'T'.substr($today, 11).'Z');
                break;
            default:
                $startdate = urlencode(date($dateformat, strtotime('-2 day')));
                $enddate = urlencode(date($dateformat));
                break;
        }
        $formatteddates = array($startdate, $enddate);
        return $formatteddates;
    }

    function authenticate($filename, $refid){
        $file = file_get_contents($filename, true);
        $csv = explode(";", $file);
        $key = array_search($refid, $csv);
        $refidkey = intval($key)-1;
        $authtoken = $csv[$refidkey];
        // print_r($csv);
        return $authtoken;
    }

    function export($startdate, $enddate, $cwurl, $options, $refid, $postbackdate, $finalurl, $campaignid, $merchantname, $user){
        echo PHP_EOL.PHP_EOL.'-----------------------------------------------------------------'.PHP_EOL.'CampaignId: '.$campaignid.PHP_EOL.'Campaign Name: '.$merchantname.PHP_EOL.'Refid: '.$refid.PHP_EOL.'User: '.$user.PHP_EOL.'Date: '.$startdate.PHP_EOL.'Postback Date: '.$postbackdate.PHP_EOL.PHP_EOL;
        $data = new Curl($cwurl, $options);
        $data->request('PUT');
        // print_r($data);
        $counter = 0;
        foreach ($data->obj->transactions as $transaction){
            $orderid = $transaction->orderid;
            $commission = round($transaction->affcommission,2);
            $totalcost = round($transaction->totalcost, 2);
            $cwcampaignid = $transaction->cid;
            $extradata = $transaction->sid1;
            $date = substr($transaction->created, 0, 10);
            $currency = $transaction->currency;
            $realcurrency = $transaction->realCurrency;
            $url = $finalurl;
            $url = str_replace('{commission}', $commission, $url);
            $url = str_replace('{orderid}', $orderid, $url);
            $url = str_replace('{currency}', $currency, $url);
            $url = str_replace('{extradata}', $extradata, $url);
            getDuplicate($orderid, $url, $postbackdate, $refid, $campaignid, $cwcampaignid);
        }
        echo PHP_EOL.'Total: '.$counter.PHP_EOL;
    }

    function getDuplicate($orderid, $url, $postbackdate, $refid, $campaignid, $cwcampaignid){
        $fichero = file_get_contents('Postback.csv', true);
        $csv = explode(";", $fichero);
        //print_r($csv);
        $counter = 0;
        if(!in_array($orderid, $csv)){
            if($cwcampaignid == $campaignid){
                $counter++;
                echo $url.PHP_EOL;
                // $s2s = new Curl($url);
                // $s2s->request("GET");    
                // exportcsv($orderid, $postbackdate, $url, $refid, $campaignid);
            }
        }
        elseif($cwcampaignid == $campaignid){
            echo 'The transaction with orderid = '. $orderid.' for affiliate with refid = '.$refid.' is already sent.'.PHP_EOL;
        }
    }

    function exportcsv($orderid, $postbackdate, $url, $refid, $campaignid){
        $filename = 'Postback.csv';
        $columns = 'Date;CampaignId;OrderId;URL;RefId;';

        if(file_exists($filename))
        {
            $message = "El Archivo $filename se ha modificado.";
        }
        else
        {
            $message = "El Archivo $filename se ha creado.";
            $file = fopen($filename, "a");
            fwrite($file, $columns.PHP_EOL);
            fclose($file);    
        }
        // echo $message.PHP_EOL;
        $file = fopen($filename, "a");
        fwrite($file, $postbackdate.';'.$campaignid.';'.$orderid.';'.$url.';'.$refid.';'.PHP_EOL);
        fclose($file);
    }

    function deleteFile($postbackdate){
        $maxlines = 8000;
        $deletedlines = $maxlines/2;
        $backupfilename = 'Postback.csv';
        $finaldate = str_replace(' ', '_', $postbackdate);
        $filename = 'backup/'.$finaldate.'_Postback.csv';
        $file = fopen ($backupfilename, "r"); 
        $lines = 0;
        while (!feof ($file)) { 
            if ($linea = fgets($file)){ 
            $lines++; 
            }
        }
        fclose ($file);
        // echo 'Lines: '.$lines;
        if($lines >= $maxlines){
            backupFile($filename, $backupfilename);
            exec("sed -i '2,".$deletedlines."d' ".$backupfilename);
            // echo PHP_EOL."sed -i '2,".$deletedlines."d' ".$backupfilename;
            echo PHP_EOL.$deletedlines.' have been deleted.'.PHP_EOL;
        }
    }

    function backupFile($filename, $backupfilename){
        $data = file_get_contents($backupfilename);
        if(file_exists($filename))
        {
            $message = "El Archivo $filename se ha modificado.";
        }
        // echo $message.PHP_EOL;
        file_put_contents($filename, $data);
    }
?>