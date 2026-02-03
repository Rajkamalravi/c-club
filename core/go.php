<?php
header("Location: /hires/login");taoh_exit();

echo $_SERVER[ 'REQUEST_URI' ];taoh_exit();

$taoh_call = "cognizeus.get";
$taoh_call_type = "get";
$taoh_vals = array(
  "mod" => "referral",
  "code" => $ret[ 'token' ],
  "ctype" => "token",
  "app" => "referral",
  "ops" => "add",
  "var" => "invites",
  "val" => "1",
  
);
$referral = (array) json_decode(taoh_apicall_get( $taoh_call, $taoh_vals ));
//$referral = (array) json_decode(taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals ));