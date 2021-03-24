<?php
    function exportcsv($columnsvars, $vars, $filename, $linecounter){
        $columns = implode(';', $columnsvars);
        $columns = $columns.';';
        if(file_exists($filename) && $linecounter == 0){
            $file = fopen($filename, "w");
            fwrite($file, $columns.PHP_EOL);
            fclose($file);
        }
        else{
            $message = "File $filename has been updated.";
            $file = fopen($filename, "a");
            $line = implode(';', $vars);
            $line = $line.';';
            fwrite($file, $line.PHP_EOL);
            fclose($file);
        }
    }
?>