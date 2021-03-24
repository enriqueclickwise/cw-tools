<?php
    /* 
        DESCRIPTION: SCRIPT THAT CONVERTS A W3U LIST (WISEPLAY) TO M3U LIST (ALL OTHERS) AND VICEVERSA.
        AUTHOR: @chechumr
        DATE: 2020-02-28
    */
    class w3u{
        function loadw3u($original_lists_dir, $converted_lists_dir, $file_basename){
            $original_file_path = $original_lists_dir.'/'.$file_basename;
            $json = file_get_contents($original_file_path);
            $json_array = json_decode($json);
            // print_r($json_array);
            $this->parsem3u($converted_lists_dir, $file_basename, $json_array);
        }
        function parsem3u($converted_lists_dir, $file_basename, $json_array){
            $converted_file_path = $converted_lists_dir.'/'.$file_basename;
            $initial_tag = '#EXTM3U';
            $tag = '#EXTINF:-1';
            $file = fopen($converted_file_path, "a");
            fwrite($file, $initial_tag.PHP_EOL);
            foreach($json_array as $item){
                if(is_array($item)){
                    fwrite($file, $tag.PHP_EOL.$current_url);
                }
            }
            fclose($file);
        }
    }

    class m3u{
        function loadm3u($original_lists_dir, $converted_lists_dir, $file_basename){
        }    
    }
    $original_lists_dir = 'original_lists';
    $converted_lists_dir = 'converted_lists';
    $original_lists = scandir($original_lists_dir);
    unset($original_lists[0], $original_lists[1]);
    // print_r($original_lists);
    foreach($original_lists as $current_list){
        $file_data = pathinfo($current_list);
        // print_r($file_data);
        $file_extension = $file_data['extension'];
        $file_basename = $file_data['basename'];
        if($file_extension == 'w3u'){
            $w3u = new w3u();
            $w3u->loadw3u($original_lists_dir, $converted_lists_dir, $file_basename);
        }
        else{
            $m3u = new m3u();
            $m3u->loadm3u($original_lists_dir, $converted_lists_dir, $file_basename);
        }
    }
?>