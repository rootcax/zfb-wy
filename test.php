<?php

$dir_path = $_SERVER['DOCUMENT_ROOT'];
if (!is_dir($dir_path)) {
    mkdir($dir_path, 0777, true);
}
$bank = file_get_contents($dir_path . "/bank.txt");
$bank_arr = explode(",", $bank);
foreach ($bank_arr as $arr){
    $bank_info = explode("-", $arr);
    print_r($bank_info);
}
