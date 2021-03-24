<?php
    include_once './classes/feed_class.php';
    include_once './classes/feedproduct_class.php';
    include_once './interfaces/feed_interface.php';
    class Onlymovil{
        public function getFeedInfo(){
            $feed = new Feed();
            $feed->setId(1);
            $feed_info = $feed->globalGetFeedInfo($feed->getId());
            return $feed_info;
        }
        public function getFeedData($feed_info){
            $feed = new Feed();
            $feed_data = $feed->globalGetFeedData($feed_info->URL, 'GET');
            return $feed_data;
        }
        public function readCsv($csv_data, $separator){
            $feed = new Feed();
            $csv_array = $feed->globalReadCsv($csv_data, $separator);
            return $csv_array;
        }
        public function insertProduct($csv_array, $current_csv_path, $cw_separator){
            $csv_product = new FeedProduct();
            $last_element = array_key_last($csv_array);
            unset($csv_array[0]);
            unset($csv_array[$last_element]);
            // print_r($csv_array);
            foreach($csv_array as $csv_product_line){
                $redirect_param = '&u=';
                // print_r($csv_product_line);
                $csv_product->setId($csv_product_line[0]);
                $csv_product->setTitle($csv_product_line[1]);
                $csv_product->setDescription($csv_product_line[8]);
                $link = explode($redirect_param,$csv_product_line[5]);
                $link = urldecode($link[1]);
                $csv_product->setLink($link);
                $csv_product->setStatus($csv_product_line[10]);
                $price = $csv_product_line[3].' '.$csv_product_line[2];
                $csv_product->setPrice($price);
                $csv_product->setAvailable($csv_product_line[11]);
                $csv_product->setImageUrl($csv_product_line[6]);
                $csv_product->setGtin($csv_product_line[13]);
                $csv_product->setMpn($csv_product_line[14]);
                $csv_product->setBrand($csv_product_line[15]);
                $csv_product->globalInsertProduct($csv_product, $current_csv_path, $cw_separator);
            }        
        }
    }
    ?>