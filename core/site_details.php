<?php

error_reporting(E_ALL);
header('Content-Type: application/json');

$code = taoh_parse_url(1);

if(!isset($code) || $code != TAOH_OPS_CODE){

    
    $result = array(
        'status' => 'false',
        'message' => 'Invalid code'
    );
    
    echo json_encode($result);
}
else{

    $return = site_config();
    $result = array(
        'status' => 'true',
        'output' => $return
    );
    echo json_encode($result);
}

?>