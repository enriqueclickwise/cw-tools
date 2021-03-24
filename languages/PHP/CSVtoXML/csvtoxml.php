<?php
    $conf = 'csvtoxmlconf.json';
    getConf($conf);
    function getConf($conf){
        $xml = array();
        $configurationfile = file_get_contents($conf);
        $json = json_decode($configurationfile);
        $campaigns = $json->campaigns;
        foreach($campaigns as $campaign){
            $campaignid = $campaign->campaignId;
            $merchantname = $campaign->merchantName;
            $network = $campaign->network;
            $separator = $campaign->separator;
            if(strpos($separator, '"') != false){
                $separator = str_replace('\"', '"', $separator);
            }
            $includedcolumns = $campaign->includedColumns;
            $csvpathname = $campaign->csvPathName;
            $feedurl = $campaign->feedUrl;
            $confname = $campaign->confName;
            $_xmlpath = $campaign->xmlPath;
            $refids = $campaign->refIds;
            $_deeplink = $campaign->deepLink;
            $active = $campaign->active;
            if($active != false){
                downloader($feedurl, $csvpathname);
                foreach($refids as $refid){
                    echo '========================================================================='.PHP_EOL;
                    echo 'Final XML Name: '.$xmlpath.PHP_EOL.'Used CSV: '.$csvpathname.PHP_EOL.'Used conf: '.$confname.PHP_EOL;    
                    $deeplink = str_replace('{refid}', $refid, $_deeplink);
                    $xmlpath = str_replace('{$refid}', $refid, $_xmlpath);
                    readCsv($csvpathname, $separator, $includedcolumns, $confname, $xml, $xmlpath, $_deeplink, $campaignid, $network);
                }
            }
            else{
                echo PHP_EOL.'Campaign "'.$merchantname.'" with ID "'.$campaignid.'" is not active and this program wont generate the XML.'.PHP_EOL;
            }
        }
    }

    function downloader($feedurl, $csvpathname){
        echo 'Downloading feed from '.$feedurl.' and saving into '.$csvpathname.'. Please wait until it downloads completely...';
        $file = file_get_contents($feedurl);
        $savedfile = file_put_contents($csvpathname, $file);
    }

    function readCsv($csvpathname, $separator, $includedcolumns, $confname, $xml, $xmlpath, $_deeplink, $campaignid, $network){
        $columncounter = 0;
        $csvdata = file_get_contents($csvpathname);
        $lines = explode(PHP_EOL, $csvdata);
        if($includedcolumns != false){
            $columns = $lines[0];
            $_separator = $separator;
            if($network == 'TD'){
                $separator = '|';
            }
            else{
                $separator = $_separator;
            }
            $columnsarray = explode($separator, $columns);
            // Deleted first line due to they are columns
            unset($lines[0]);
            $_lines = $lines;
            unset($lines);
            $lines = array();
            foreach($_lines as $line){
                array_push($lines, $line);
            }
        }
        else{
            $columnsarray = array('ID','Name','Description','Link','Price','Image','LastPrice','Category','Brand');
        }
        $columncounter = count($columnsarray);
        $linescounter = count($lines);
        file_put_contents($xmlpath, '<?xml version="1.0"?>');
        file_put_contents($xmlpath, PHP_EOL, FILE_APPEND);
        file_put_contents($xmlpath, '<CWXML>', FILE_APPEND);
        file_put_contents($xmlpath, PHP_EOL, FILE_APPEND);
        for ($i = 0; $i < $linescounter; $i++) {
            $productdata = explode($_separator, $lines[$i]);
            $product = array();
            $finalproduct = array_combine($columnsarray, $productdata);
            $productobject = (object)$finalproduct;
            // print_r($productobject);
            // print_r($columnsarray);
            // print_r($productdata);
            $conf = readConf($confname);
            $xml = setXML($conf, $productobject, $xml, $_deeplink);
        }
        if(file_put_contents($xmlpath, $xml, FILE_APPEND) != false){
            file_put_contents($xmlpath, '</CWXML>', FILE_APPEND);
            echo 'XML "'.$xmlpath.'" generated successfully!'.PHP_EOL;
            echo '========================================================================='.PHP_EOL;
        }
        else{
            echo 'XML "'.$xmlpath.'" coudnt be generated!'.PHP_EOL;
            echo '========================================================================='.PHP_EOL;
        }
    }
    function readConf($confname){
        $json = file_get_contents($confname);
        $conf = json_decode($json);
        $conf = (array)$conf;
        return($conf);
    }
    function setXML($conf, $productobject, $xml, $_deeplink){
        $csvid = $conf['id'];
        $csvname = $conf['name'];
        $csvproductname = str_replace( '"', '', $productobject->$csvname);
        $csvdescription = $conf['description'];
        $csvlink = $conf['url'];
        $csvdeeplink = str_replace('{url}', urlencode($productobject->$csvlink), $_deeplink);
        $csvdeeplink = urlencode($csvdeeplink);
        $csvprice = $conf['price'];
        $csvpricekey = array_search($csvprice, $conf);
        $csvimage = $conf['image'];
        $csvlastprice = $conf['lastprice'];
        $csvlastpricekey = array_search($csvlastprice, $conf);
        if($csvpricekey == $csvlastpricekey){
            $csvlastpricekey = 'lastprice';
        }
        $csvcategory = $conf['category'];
        $csvbrand = $conf['brand'];
        // XML writter:
        $cwxml = new SimpleXMLElement('<xml/>');
        $track = $cwxml->addChild('product');
        $track->addChild(array_search($csvid, $conf), $productobject->$csvid);
        $track->addChild(array_search($csvname, $conf), htmlspecialchars($csvproductname));
        $track->addChild(array_search($csvdescription, $conf), htmlspecialchars($productobject->$csvdescription));
        $track->addChild(array_search($csvlink, $conf), $csvdeeplink);
        $track->addChild(array_search($csvprice, $conf), $productobject->$csvprice);
        $track->addChild(array_search($csvimage, $conf), $productobject->$csvimage);
        $track->addChild($csvlastpricekey, $productobject->$csvlastprice);
        $track->addChild(array_search($csvcategory, $conf), htmlspecialchars($productobject->$csvcategory));
        $track->addChild(array_search($csvbrand, $conf), htmlspecialchars($productobject->$csvbrand));
        array_push($xml, $track->asXML());
        array_push($xml, PHP_EOL);
        return $xml;
    }