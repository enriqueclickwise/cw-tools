<?php
    include_once './libs/Curl.php';
    class Feed{
        //PARAMS
        public $id = 0;
        public $name = '';
        public $url = '';
        public $separator = '';
        //PARAMS

        //VALIDATIONS
        private function validateString($var, $limit){
            if(is_string($var) != true || strlen($var) > $limit){
                throw new Exception('The var "'.$var.'" is not a string or it is more than '.$limit.' chars long');
                var_dump($var);
            }
            return true;
        }
        private function validateUniqueId($var){
            if(is_int($var) != true){
                throw new Exception('The var "'.$var.'" is not a integer');
                var_dump($var);
            }
            return true;
        }
        //VALIDATIONS

        //GETTERS & SETTERS
        public function setId($id){
            $validation = self::validateUniqueId($id);
            if($validation != false){
                $this->id = $id;
            }
        }
        public function getId(){
            return $this->id;
        }
        public function setName($name){
            $validation = self::validateString($name, 50);
            if($validation == true){
                $this->name = $name;
            }
        }
        public function getName(){
            return $this->name;
        }
        public function setFeedUrl($url){
            $validation = self::validateString($url, 500);
            if($validation == true){
                $this->feed_url = $url;
            }
        }
        public function getFeedUrl(){
            return $this->feed_url;
        }
        public function setSeparator($separator){
            $validation = self::validateString($separator, 1);
            if($validation == true){
                $this->separator = $separator;
            }
        }
        public function getSeparator(){
            return $this->separator;
        }
        //GETTERS & SETTERS

        //SHARED METHODS
        public function globalGetFeedInfo($current_feed_id){
            $conf = 'conf.json';
            // $conf = '../conf.json';
            $json = file_get_contents($conf);
            $conf_data = json_decode($json);
            // print_r($conf_data);
            return $conf_data[$current_feed_id-1];
        }
        public function globalGetFeedData($current_feed_url, $method){
            $r = new Curl($current_feed_url);
            $r->request($method);
            $csv_data = $r->response;
            // print_r($json);
            return $csv_data;
        }
        public function globalReadCsv($csv_data, $separator, $enclosure = '"'){
            $lines = explode(PHP_EOL, $csv_data);
            $csv_array = array();
            foreach ($lines as $line) {
                $csv_array[] = str_getcsv($line, $separator, $enclosure);
            }
            return $csv_array;
        }
        //SHARED METHODS
    }
?>