<?php
    $confname = 'conf.json';
    getConf($confname);

    function getConf($confname){
        $counter = 0;
        $conf = file_get_contents($confname);
        $json = json_decode($conf);
        // print_r($json);
        foreach($json as $campaign){
            $url = $json->campaigns[$counter]->url;
            $originalfilename = $json->campaigns[$counter]->originalFileName;
            $finalfilename = $json->campaigns[$counter]->finalFileName;
            $refids = $json->campaigns[$counter]->refIds;
            $linkcolumn = $json->campaigns[$counter]->linkColumn;
            $separator = $json->campaigns[$counter]->separator;
            $urlparams = $json->campaigns[$counter]->urlParams;
            $cwlink = $json->campaigns[$counter]->link;
            if($separator == '\"|'){
                $separator = str_replace('\"', '"', $separator);
            }
            $campaignid = $json->campaigns[$counter]->campaignId;
            // downloader($url, $originalfilename);
            $counter++;
            foreach($refids as $refid){
                getFinalFile($originalfilename, $finalfilename, $refid, $linkcolumn, $separator, $campaignid, $urlparams, $cwlink);
            }
        }
    }

    function downloader($url, $originalfilename){
        $file = file_get_contents($url);
        $savedfile = file_put_contents($originalfilename, $file);
    }

    function getFinalFile($originalfilename, $finalfilename, $refid, $linkcolumn, $separator, $campaignid, $urlparams, $cwlink){
        $finalfilename = str_replace('{refid}', $refid, $finalfilename);
        $file = file_get_contents($originalfilename);
        $products = explode(PHP_EOL, $file);
        $columns = $products[0];
        $columnseparator = $separator;
        //Special condition if TD campaign:
        if($campaignid == '1d509ac9'){
            $columnseparator = '|';
        }
        $columnsarray = explode($columnseparator, $columns);
        // print_r($columnsarray);
        unset($products[0]);
        $_products = $products;
        unset($products);
        $products = array();
        foreach($_products as $product){
            array_push($products, $product);
        }
        // print_r($products);
        foreach($products as $product){
            $productproperties = explode($separator, $product);
            // print_r($productproperties);
            $linkkey = array_search($linkcolumn, $columnsarray);
            $params = str_replace('{refid}', $refid, $urlparams);
            $link = $productproperties[$linkkey].$params;
            if($campaignid == '1d509ac9'){
                $link = str_replace('"', '', $link);            
            }    
            $link = urlencode($link);
            $cwlink = str_replace('{refid}', $refid, $cwlink);
            $finalink = $cwlink.$link;
            echo $finalink.PHP_EOL;
            overWritter($finalink, $products);
        }
    }

    function overWritter($finalink){

    }


?>