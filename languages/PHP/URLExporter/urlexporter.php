<?php
    set_include_path('../libs');
    include_once 'Curl.php';
    $counter = 0;
    $filename = 'urls.txt';
    $urls = file_get_contents($filename);
    $splitted = explode(PHP_EOL, $urls);
    foreach($splitted as $url){
        // $s2s = new Curl($url);
        // $s2s->request("GET");
        echo $url.PHP_EOL;
        $counter++;
    }
    echo PHP_EOL.'Total: '.$counter.PHP_EOL;
?>