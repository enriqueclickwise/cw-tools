<?php
    $cw_separator = ';';
    $root_path = dirname(__FILE__);
    $implementations_path = $root_path.'/implementations/';
    $csv_path = $root_path.'/csv/';
    include_once 'classes/feed_class.php';
    function insertHeaders($current_csv_path, $cw_separator){
        $headers = array(   'ID',
                            'Name',
                            'Description',
                            'Link',
                            'Status',
                            'Price',
                            'Available',
                            'ImageUrl',
                            'Gtin',
                            'Mpn',
                            'Brand');
        // echo 'Headers insertion'.PHP_EOL;
        foreach($headers as $header){
            file_put_contents($current_csv_path, $header.$cw_separator, FILE_APPEND);
        }
        // echo 'Headers insertion done'.PHP_EOL;
        file_put_contents($current_csv_path, PHP_EOL, FILE_APPEND);
    }
    $implementation_directory_files = scandir($implementations_path);
    unset($implementation_directory_files[0],$implementation_directory_files[1]);
    foreach($implementation_directory_files as $file_name){
        include_once $implementations_path.$file_name;
        $class_instance_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
        $current_csv_path = $csv_path.$class_instance_name.'.csv';
        $feed = new $class_instance_name();
        $feed_info = $feed->getFeedInfo();
        // echo '================='.PHP_EOL.'Feed ID: '.$feed_info->Id.PHP_EOL.'Feed Name: '.$feed_info->Name.PHP_EOL.'Feed URL: '.$feed_info->URL.PHP_EOL.'Feed Separator: "'.$feed_info->Separator.'"'.PHP_EOL.'================='.PHP_EOL;
        $csv_data = $feed->getFeedData($feed_info);
        $csv_array = $feed->readCsv($csv_data, $feed_info->Separator);
        if(file_exists($current_csv_path) == true){
            file_put_contents($current_csv_path, ''); // Remove previous file content
        }
        insertHeaders($current_csv_path, $cw_separator);
        $feed->insertProduct($csv_array, $current_csv_path, $cw_separator);
        echo 'File "'.$current_csv_path.'" has been created'.PHP_EOL;
    }
?>