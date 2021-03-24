<?php
    function errorlog($message){
        $errorlog = "error_log/error_log.txt"; 
        if(file_exists($errorlog)){
            $mensaje = "El archivo $errorlog se ha modificado.";
        }
        else{
            $mensaje = "El archivo $errorlog se ha creado.";
        }
        if($errorlog = fopen($errorlog, "a"))
        {
            if(fwrite($errorlog, date("Y-m-d H:m:s"). " ". $message. PHP_EOL)){
                echo PHP_EOL . "Error registrado correctamente.". PHP_EOL . PHP_EOL;
            }
            else{
                echo PHP_EOL . "Ha habido un problema al crear el archivo." . PHP_EOL . PHP_EOL;
            }
            fclose($errorlog);
        }
    }
?>