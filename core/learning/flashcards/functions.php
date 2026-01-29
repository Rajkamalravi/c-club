<?php
function taoh_get_blog_categories2( $type = 'all' ) {
 if ( $type == 'flash' ){
    //$api = TAOH_INFOFETCH_GET.'?ops=flashcat&token='.taoh_get_dummy_token();
    //$response = json_decode( taoh_url_get_content ( $api ), true );
     $taoh_call = "infofetch.get";
     $taoh_vals = array(
         'ops' => 'flashcat',
         'token' => taoh_get_dummy_token(),
         'cache' => array("name" => TAOH_API_TOKEN . '_' . taoh_p2us($taoh_call) . '_flashcat'),
     );
    $taoh_call_type = "get";
    $response = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );

    $response = $response[ 'output' ][ 'category' ];
  } else {
    $api = TAOH_CONTENT_CATEGORY;
    $response = json_decode( taoh_url_get_content( $api ), true );
  }
  if(isset($response) && is_array($response)) {
    return $response;
  }
  return array();
}
?>
