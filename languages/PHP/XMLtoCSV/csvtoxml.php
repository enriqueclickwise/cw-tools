<?php
    $conf = 'conf.json';
    getConf($conf);
    function getConf($conf){
        $xml = array();
        $configurationfile = file_get_contents($conf);
        $json = json_decode($configurationfile);
        $campaigns = $json->campaigns;
        foreach($campaigns as $campaign){
            $campaignid = $campaign->campaignId;
            $merchantname = $campaign->merchantName;
            $separator = $campaign->separator;
            $includedcolumns = $campaign->includedColumns;
            $csvpathname = $campaign->csvPathName;
            $confname = $campaign->confName;
            $xmlpath = $campaign->xmlPath;
            $active = $campaign->active;
            if($active != false){
                echo '========================================================================='.PHP_EOL;
                echo 'Final XML Name: '.$xmlpath.PHP_EOL.'Used CSV: '.$csvpathname.PHP_EOL.'Used conf: '.$confname.PHP_EOL;
                readCsv($csvpathname, $separator, $includedcolumns, $confname, $xml, $xmlpath);
            }
            else{
                echo 'Campaign "'.$merchantname.'" with ID "'.$campaignid.'" is not active and this program wont generate the XML.'.PHP_EOL;
            }
        }
    }
    function readCsv($csvpathname, $separator, $includedcolumns, $confname, $xml, $xmlpath){
        $columncounter = 0;
        $csvdata = file_get_contents($csvpathname);
        $lines = explode(PHP_EOL, $csvdata);
        if($includedcolumns != false){
            $columns = $lines[0];
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
        $filechecker = file_exists($xmlpath);
        if($filechecker != false){
            echo 'File "'.$xmlpath.'" already exists. If you want to overwrite the content of this file type "Y", if not type other:'.PHP_EOL;
            $answer = readline();
            if($answer == 'Y' || $answer == 'y'){
                file_put_contents($xmlpath, '<?xml version="1.0"?>');
                file_put_contents($xmlpath, PHP_EOL, FILE_APPEND);
                for ($i = 0; $i < $linescounter; $i++) {
                    $productdata = explode($separator, $lines[$i]);
                    $product = array();
                    $finalproduct = array_combine($columnsarray, $productdata);
                    $productobject = (object)$finalproduct;
                    // print_r($productobject);
                    $conf = readConf($confname);
                    $xml = setXML($conf, $productobject, $xml);
                }
                if(file_put_contents($xmlpath, $xml, FILE_APPEND) != false){
                    echo 'XML "'.$xmlpath.'" generated successfully!'.PHP_EOL;
                    echo '========================================================================='.PHP_EOL;
                }
                else{
                    echo 'XML "'.$xmlpath.'" coudnt be generated!'.PHP_EOL;
                    echo '========================================================================='.PHP_EOL;
                }        
            }
            else{
                echo 'File "'.$xmlpath.'" wont be overwritten.'.PHP_EOL;
                echo '========================================================================='.PHP_EOL;
            }
        }
        else{
            file_put_contents($xmlpath, '<?xml version="1.0"?>');
            file_put_contents($xmlpath, PHP_EOL, FILE_APPEND);
            for ($i = 0; $i < $linescounter; $i++) {
                $productdata = explode($separator, $lines[$i]);
                $product = array();
                $finalproduct = array_combine($columnsarray, $productdata);
                $productobject = (object)$finalproduct;
                // print_r($productobject);
                $conf = readConf($confname);
                $xml = setXML($conf, $productobject, $xml);
            }
            if(file_put_contents($xmlpath, $xml, FILE_APPEND) != false){
                echo 'XML "'.$xmlpath.'" generated successfully!'.PHP_EOL;
                echo '========================================================================='.PHP_EOL;
            }
            else{
                echo 'XML "'.$xmlpath.'" coudnt be generated!'.PHP_EOL;
                echo '========================================================================='.PHP_EOL;
            }
        }
    }
    function readConf($confname){
        $json = file_get_contents($confname);
        $conf = json_decode($json);
        $conf = (array)$conf;
        return($conf);
    }
    function setXML($conf, $productobject, $xml){
        $csvid = $conf['Id'];
        $csvname = $conf['Name'];
        $csvdescription = $conf['Description'];
        $csvlink = $conf['Link'];
        $csvprice = $conf['Price'];
        $csvimage = $conf['Image'];
        $csvlastprice = $conf['LastPrice'];
        $csvcategory = $conf['Category'];
        $csvbrand = $conf['Brand'];
        // XML writter:
        $cwxml = new SimpleXMLElement('<xml/>');
        $track = $cwxml->addChild('product');
        $track->addChild(array_search($csvid, $conf), $productobject->$csvid);
        $track->addChild(array_search($csvname, $conf), $productobject->$csvname);
        $track->addChild(array_search($csvdescription, $conf), htmlspecialchars($productobject->$csvdescription));
        $track->addChild(array_search($csvlink, $conf), $productobject->$csvlink);
        $track->addChild(array_search($csvprice, $conf), $productobject->$csvprice);
        $track->addChild(array_search($csvimage, $conf), $productobject->$csvimage);
        $track->addChild(array_search($csvlastprice, $conf), $productobject->$csvlastprice);
        $track->addChild(array_search($csvcategory, $conf), htmlspecialchars($productobject->$csvcategory));
        $track->addChild(array_search($csvbrand, $conf), htmlspecialchars($productobject->$csvbrand));
        array_push($xml, $track->asXML());
        array_push($xml, PHP_EOL);
        return $xml;
    }