<?php

function taoh_apicall_get_debug_1( $taoh_call, $taoh_vals, $prefix = TAOH_API_PREFIX ){

    $file_mod = $taoh_vals[ 'mod' ];
    $cache_file_build = http_build_query( $taoh_vals );
    if(isset($taoh_vals['cache_name'])){
        $cache_file_name = $taoh_vals['cache_name'];
        unset($taoh_vals['cache_name']);
    }else{
        $cache_file_name = $file_mod.'_'.hash('crc32',$cache_file_build);
    }
    
    if($file_mod != 'rooms' && $file_mod != ''){
        //$taoh_vals['cache'] = $cache_file_name;
        //$taoh_vals['caching'] = $cache_file_name;
        if (!isset($taoh_vals['cache_required']) || $taoh_vals['cache_required'] != 0) {
            $taoh_vals['cache'] = array("name" => $cache_file_name);
        }
      }
    
    $source = TAOH_SITE_URL_ROOT;
    $taoh_vals['source'] = $source;
    if(isset($taoh_vals['sub_secret'])){
        $taoh_vals['sub_secret_token'] = $taoh_vals['sub_secret'];
    }else{
        $taoh_vals['sub_secret_token'] = TAOH_ROOT_PATH_HASH;
    }

    $postdata = http_build_query( $taoh_vals );
    $url = $prefix.'/'.$taoh_call;
    $url_print = str_replace('&amp;', '&', $url . '?' .html_entity_decode( $postdata ));
    
    if(isset($_GET['debug']) && $_GET['debug'] == 1){
        $filename = TAOH_PLUGIN_PATH.'/cache/log/'.date("y-m-d").'_logs.cache';
        
       
       if ( file_exists( $filename ) ){
            $fp = fopen ($filename, 'a');
            fwrite($fp, "\n". time().'-'.$url_print);
            fclose($fp);
       }
       else{
        $fp = fopen ($filename, 'w');       
        fputcsv($fp, $data);
        fclose($fp);
       }
    }

    return $url_print;
}



function taoh_apicall_get_debug_log( $taoh_call, $taoh_vals){
    
    
    $postdata = http_build_query( $taoh_vals );
    $url = TAOH_API_PREFIX.'/'.$taoh_call;
    $url_print = str_replace('&amp;', '&', $url . '?' .html_entity_decode( $postdata ));
   
    $data[0] = $url_print;
    
    $filename = TAOH_PLUGIN_PATH.'/cache/logs/'.date("y-m-d").'_logs.cache';
        
       
       if ( file_exists( $filename ) ){
            $fp = fopen ($filename, 'a');
            fwrite($fp, "\n". time().'-'.$url_print);
            fclose($fp);
       }
       else{
        $fp = fopen ($filename, 'w');       
        fputcsv($fp, $data);
        fclose($fp);
       }

    

    return $url_print;
}


function taoh_apicall_post_debug_1( $taoh_call, $taoh_vals, $prefix = TAOH_API_PREFIX ){
    $source = TAOH_SITE_URL_ROOT;
    $taoh_vals['source'] = $source;
    $taoh_vals['sub_secret_token'] = TAOH_ROOT_PATH_HASH;
    $postdata = http_build_query( $taoh_vals );
    /*
    $opts = array( 'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $postdata,
      ),
      "ssl"=>array(
          "verify_peer"=>false,
          "verify_peer_name"=>false,
      ),
    );
    $context  = stream_context_create( $opts );
    $result = file_get_contents( $url, false, $context );
    */
    $url = $prefix.'/'.$taoh_call;

    echo "URL: [$url]; PostData: [$postdata];";
    return 0;
}
  
function tao_debug( $endpoint, $title="null" ) {
	if ( @$_GET[ 'debug' ] ) {
    $data = json_decode( taoh_url_get_content( $endpoint ) );
		echo '<div class="alert alert-warning" style="word-break: break-all;">{
			"Title": <b>'.$title.'</b><br>
			"Endpoint": <b>'.print_r( $endpoint ).'</b>,<br>
			"Result": '.json_encode( $data ).'<br>
		}</div>';
	}
}

function taoh_apicall_get_debug($taoh_call, $taoh_vals, $prefix = TAOH_API_PREFIX, $cache_enable = 0)
{
    /*if ($prefix == '') $prefix = TAOH_API_PREFIX;
    $taoh_vals['source'] = TAOH_SITE_URL_ROOT;
    if (isset($taoh_vals['sub_secret'])) {
        $taoh_vals['sub_secret_token'] = $taoh_vals['sub_secret'];
    } else {
        $taoh_vals['sub_secret_token'] = TAOH_ROOT_PATH_HASH;
    }
    $return = taoh_api_url($taoh_call, $taoh_vals, 'GET', $prefix, $cache_enable);
    return $return;*/

    $taoh_vals['debug_api'] = 1;
    $data = taoh_api_request($taoh_call, $taoh_vals, 'GET', $prefix);
    return $data['response'] ?? '';
}

function taoh_apicall_post_debug( $taoh_call, $taoh_vals, $prefix = TAOH_API_PREFIX ){

    
    $taoh_vals['debug_api'] = 1;
    $data = taoh_api_request($taoh_call, $taoh_vals, 'POST', $prefix);
    return $data['response'] ?? '';

}


?>