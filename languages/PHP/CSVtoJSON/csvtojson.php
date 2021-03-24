<?php
  $csvname = 'example.csv';
  $separator = '|';
  $csvcontent = file_get_contents($csvname);
  $csvcontent = explode(PHP_EOL, $csvcontent);
  $columns = explode($separator, $csvcontent[0]);
  // print_r($columns);
  unset($csvcontent[0]);
  $data = array();
  foreach($csvcontent as $line){
    $values = explode($separator, $line);
    $csv = array_combine($columns, $values);
    // print_r($c);
    $filename = 'results.json';
    $json = json_encode($csv).','.PHP_EOL;
    $file = file_put_contents($filename, $json, FILE_APPEND);
  }
?>