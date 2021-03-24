<?php

class Curl{

    public $url = null;
    public $response = null;
    public $status_code = 0;
    public $error = "";
    public $headers = array();
    public $obj;

    public function __construct($url=null, $customHeaders = array()){
        if($url != null){
            $this->url = $url;
            $this->headers = $customHeaders;
        }


    }

    public function request($type = "GET",$vars=null){
        $curl = curl_init();
        $resp;
        curl_setopt($curl,CURLOPT_HTTPHEADER,$this->headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        switch($type){
            case "GET":
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $this->url,
                    CURLOPT_USERAGENT => "Clickwise Browser Agent"
                ));
                $resp = curl_exec($curl);
                $this->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->error = curl_error($curl);
                curl_close($curl);
                $this->response = $resp;
                break;
            case "POST":
                if(is_array($vars)){
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => $vars,
                        CURLOPT_URL => $this->url,
                        CURLOPT_USERAGENT => "Clickwise Browser Agent"
                    ));
                    $resp = curl_exec($curl);
                    $this->response = $resp;
                }else{
                    // throw new Exception("POST var should be arrray");
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => $vars,
                        CURLOPT_URL => $this->url,
                        CURLOPT_USERAGENT => "Clickwise Browser Agent"
                    ));
                    $resp = curl_exec($curl);
                    $this->response = $resp;
                }
                $this->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->error = curl_error($curl);
                curl_close($curl);
                break;
            case "POST-PAYLOAD":
                if(is_array($vars)){
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => 1,
                        CURLOPT_POSTFIELDS => http_build_query($vars),
                        CURLOPT_URL => $this->url,
                        CURLOPT_USERAGENT => "Clickwise Browser Agent"
                    ));
                    $resp = curl_exec($curl);
                    $this->response = $resp;
                }else{
                    // throw new Exception("POST var should be arrray");
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_POST => $vars,
                        CURLOPT_URL => $this->url,
                        CURLOPT_USERAGENT => "Clickwise Browser Agent"
                    ));
                    $resp = curl_exec($curl);
                    $this->response = $resp;
                }

                $this->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->error = curl_error($curl);
                curl_close($curl);
                break;
            case "POST-JSON":
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => json_encode($vars),
                    CURLOPT_URL => $this->url,
                    CURLOPT_USERAGENT => "Clickwise Browser Agent"
                ));

                $resp = curl_exec($curl);
                $this->response = $resp;
                $this->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->error = curl_error($curl);
                curl_close($curl);
                break;
            case "PUT":
                curl_setopt_array($curl, array(
                    CURLOPT_CUSTOMREQUEST  => "PUT",
                    CURLOPT_POSTFIELDS     => json_encode($vars),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $this->url,
                    CURLOPT_USERAGENT => "Clickwise Browser Agent"
                ));
                $resp = curl_exec($curl);
                $this->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->error = curl_error($curl);
                curl_close($curl);
                $this->response = $resp;
                break;
        }

        // try to decode into obj;
        @$this->decode();
        // echo '<pre>'.print_r($resp,true).'</pre>';
        return $resp;
    }

    function decode() {
      $this->obj = json_decode($this->response);
    }
}
