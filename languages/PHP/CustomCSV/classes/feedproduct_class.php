<?php
    class FeedProduct{
        // PARAMS
        public $id = '';
        public $title = '';
        public $description = '';
        public $link = '';
        public $status = '';
        public $price = '';
        public $available = '';
        public $image_url = '';
        public $gtin = '';
        public $mpn = '';
        public $brand = '';
        // PARAMS
        
        //VALIDATIONS
        private function validateString($var, $limit){
            if(is_string($var) != true || strlen($var) > $limit){
                throw new Exception('The var "'.$var.'" is not a string or it is more than '.$limit.' chars long');
                var_dump($var);
            }
            return true;
        }
        private function validateLink($var){
            if(is_string($var) != true || str_contains($var, 'http') != true){
                throw new Exception('The var "'.$var.'" is not a string or does not contain "http" inside the URL');
                var_dump($var);
            }
            return true;
        }
        private function validateOptions($var, $limit, $valid_options){
            if(is_string($var) != true || strlen($var) > $limit || in_array($var, $valid_options) == false){
                throw new Exception('The var "'.$var.'" is not a string or it is more than '.$limit.' chars long or does not belong to any options set: '.print_r($valid_options));
                var_dump($var);
            }
            return true;
        }
        private function validatePrice($var){
            $pattern = "/^([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(\.[0-9][0-9])?\ ([A-Za-z]{3})$/";
            if(preg_match($pattern, $var) != true){
                throw new Exception('The var "'.$var.'" is not a valid price value.');
                var_dump($var);
            }
            return true;
        }
        private function validateDescription($var, $limit){
            $invalid_chars = array('\n', ';');
            foreach ($invalid_chars as $invalid_char){
                if(strpos($var, $invalid_char) != false){
                    str_replace($invalid_char, '', $var);
                }
            }
            if(is_string($var) != true || strlen($var) > $limit){
                throw new Exception('The var "'.$var.'" is not a string or it is more than '.$limit.' chars long');
                var_dump($var);
            }
            return true;
        }
        //VALIDATIONS

        //GETTERS & SETTERS
        public function setId($id){
            $validation = self::validateString($id, 50);
            if($validation != false){
                $this->id = $id;
            }
        }
        public function getId(){
            return $this->id;
        }
        public function setTitle($title){
            $validation = self::validateString($title, 150);
            if($validation == true){
                $this->title = $title;
            }
        }
        public function getTitle(){
            return $this->title;
        }
        public function setDescription($description){
            $validation = self::validateDescription($description, 5000);
            if($validation == true){
                $this->description = $description;
            }
        }
        public function getDescription(){
            return $this->description;
        }
        public function setLink($link){
            $validation = self::validateLink($link, 5000);
            if($validation == true){
                // $link = urlencode($link); Only needed if primary URL is not encoded
                $this->link = $link;
            }
        }
        public function getLink(){
            return $this->link;
        }
        public function setStatus($status){
            $validation = self::validateOptions($status, 16, array('new','refurbished','used', 'nuevo', 'reacondicionado', 'usado'));
            if($validation == true){
                $this->status = $status;
            }
        }
        public function getStatus(){
            return $this->status;
        }
        public function setPrice($price){
            $validation = self::validatePrice($price);
            if($validation == true){
                $this->price = $price;
            }
        }
        public function getPrice(){
            return $this->price;
        }
        public function setAvailable($available){
            $validation = self::validateOptions($available, 30, array('in_stock','out_of_stock','preorder', 'en stock', 'fuera de stock', 'preventa'));
            if($validation == true){
                $this->available = $available;
            }
        }
        public function getAvailable(){
            return $this->available;
        }
        public function setImageUrl($image_url){
            $validation = self::validateLink($image_url, 5000);
            if($validation == true){
                $this->image_url = $image_url;
            }
        }
        public function getImageUrl(){
            return $this->image_url;
        }
        public function setGtin($gtin){
            $validation = self::validateString($gtin, 50);
            if($validation == true){
                $this->gtin = $gtin;
            }
        }
        public function getGtin(){
            return $this->gtin;
        }
        public function setMpn($mpn){
            $validation = self::validateString($mpn, 70);
            if($validation == true){
                $this->mpn = $mpn;
            }
        }
        public function getMpn(){
            return $this->mpn;
        }
        public function setBrand($brand){
            $validation = self::validateString($brand, 70);
            if($validation == true){
                $this->brand = $brand;
            }
        }
        public function getBrand(){
            return $this->brand;
        }
        //GETTERS & SETTERS
        //SHARED METHODS
        public function globalInsertProduct($csv_product, $current_csv_path, $cw_separator){
            // print_r($csv_product);
            // echo 'Product insertion'.PHP_EOL;
            foreach($csv_product as $field){
                file_put_contents($current_csv_path, $field.$cw_separator, FILE_APPEND);
            }
            // echo 'Product insertion done'.PHP_EOL;
            file_put_contents($current_csv_path, PHP_EOL, FILE_APPEND);
        }
    }
?>