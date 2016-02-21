<?php
    date_default_timezone_set('America/Los_Angeles');

    $date           = date('Y-m-d-His');
    $filename       = $_FILES['file']['name'];
    $file           = normalizeString($filename);
    $destination    = '../files/' . $date . '_' . $file;

    move_uploaded_file($_FILES['file']['tmp_name'] , $destination);
    print_r($date . '_' . $file);

    function normalizeString ($str = '') {
        $str = strip_tags($str); 
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }
?>