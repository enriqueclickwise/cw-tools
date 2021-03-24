<?php
include_once 'setconf.php';
    $jsonname = 'conf.json';
    readConf($jsonname);
    function readConf($jsonname){
        $jsontext = file_get_contents($jsonname);
        $jsonconf = json_decode($jsontext);
        // print_r($jsonconf);
        $i = 0;
        foreach($jsonconf as $conf){
            $structcounter = 0;
            $xmlfilename = $conf[$i]->xmlfilename;
            $xslfilename = $conf[$i]->xslfilename;
            $xslfinalname = $conf[$i]->xslfinalname;
            $xmltemplate = file_get_contents($xslfilename);
            $finalxmlfilename = $conf[$i]->finalxmlfilename;
            $structure = $conf[$i]->structure;
            $xmltemplate = str_replace('xslpath', $conf[$i]->xslpath, $xmltemplate);          
            foreach($structure[$i] as $key => $stru){
                $xmltemplate = str_replace($key, $stru, $xmltemplate);
            }
            $i++;
        }
        file_put_contents($xslfinalname, $xmltemplate);
        parse($xmlfilename, $xslfinalname, $finalxmlfilename);
    }
    function parse($xmlfilename, $xslfinalname, $finalxmlfilename){
        $xml = file_get_contents($xmlfilename);
        $finalxml = str_replace('& ','&amp; ',$xml);
        file_put_contents($xmlfilename, $finalxml);
        transform($xmlfilename, $xslfinalname, $finalxmlfilename);
    }
    function transform($xmlfilename, $xslfinalname, $finalxmlfilename){
        exec('xsltproc '.$xslfinalname.' '.$xmlfilename.' > '.$finalxmlfilename);
        echo 'XML CREATED SUCCESSFULY!'.PHP_EOL;
    }
    
?>