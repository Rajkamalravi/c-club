<?php

/* function.php
This file only for custom taoh related functions
Function name should start with taoh_
You can use CONSTANT from config.php, and helper functions from helper_functions.php
Do not define any new constant or redefine any CONSTANT here
Do not define any global varaiable
Do not implement the redirection here.
Do not include any Dependancy for this file here. use index.php instead
*/
function taoh_is_wp()
{
    if (function_exists('wp_head')) {
        return true;
    }
    return false;
}

function taoh_get_array($input){
    if(!$input) return [];

    if ( is_array( $input ) ){
        $return = $input;
    } else if ( is_object( $input ) ){
        $return = (array) $input;
    } else {
        $return = json_decode($input, true);
        if ( ! is_array( $return ) ) $return = serialize( $return );
    }
    return $return;
}

/**
 * @throws Exception
 */
function generate_csrf_token($key = '', $must = false) {
    if (empty($key)) $key = 'csrf_token';

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($must || empty($_SESSION[$key])) {
        $_SESSION[$key] = bin2hex(random_bytes(32));
    }

    return $_SESSION[$key];
}

function get_csrf_token($key = ''): string {
    if (empty($key)) $key = 'csrf_token';

    return $_SESSION[$key] ?? '';
}

function verify_csrf_token($token, $key = ''): bool {
    if (empty($key)) $key = 'csrf_token';

    return isset($_SESSION[$key]) && hash_equals($_SESSION[$key], $token);
}

function taoh_url_get_content($url, $type = 'get')
{
    switch ($type) {
        case 'get':
            $opts = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $context = stream_context_create($opts);
            $return = file_get_contents($url, false, $context);
            break;
        case 'post':
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_POST, true);
            //curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Hires@TAO');
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_handle, CURLOPT_SSLVERSION, 3);
            $certificate_location = TAOH_PLUGIN_PATH . "/certi/cacert.pem"; // modify this line accordingly (may need to be absolute)
            curl_setopt($curl_handle, CURLOPT_CAINFO, $certificate_location); // <------
            curl_setopt($curl_handle, CURLOPT_CAPATH, $certificate_location); // <------
            $return = curl_exec($curl_handle);
            //$query = file_get_contents($url);
            curl_close($curl_handle);
            break;
        default:
            $return = file_get_contents($url);
            break;
    }
    return $return;
}

if (!function_exists('taoh_isjson')) {
    function taoh_isjson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('taoh_const')) {
    function taoh_const(string $name)
    {
        return defined($name) ? constant($name) : null;
    }
}

function taoh_cacheops($ops, $value = '', $key = '')
{
    $return = false;

    if (isset($ops) && $ops) {
        switch (strtolower($ops)) {
            case 'metricspush':
                $taoh_vals = array(
                    'ops' => $ops,
                    'value' => $value,
                    'code' => TAOH_OPS_CODE,
                    //'debug' => 1,
                );

                $return = taoh_post(TAOH_CACHEOPS_PREFIX, $taoh_vals);
                break;
            case 'logpush':
                //echo "logpush";
                if (defined('TAOH_LOGGING_ENABLE') && TAOH_LOGGING_ENABLE === 1) {
                    $log = array(
                        'tkn' => taoh_get_dummy_token(),
                        'sec' => TAOH_API_SECRET,
                        'lcn' => $_SERVER['REQUEST_URI'],
                        'tm' => time(),
                        'sess' => (session_status() !== PHP_SESSION_NONE) ? session_id() : '',
                        'dev' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', // Use "Unknown" if not set error log
                    );
                    if (isset($misc) && $misc && strlen($misc) >= 5) $log['misc'] = $misc;
                    $log_line = serialize($log);
                    $taoh_vals = array(
                        'ops' => $ops,
                        'value' => $log_line,
                        'code' => TAOH_OPS_CODE,
                    );
                    //echo TAOH_CACHEOPS_PREFIX."?".http_build_query( $taoh_vals ); exit();
                    $return = taoh_post(TAOH_CACHEOPS_PREFIX, $taoh_vals);

                }
                break;
            default:
                //$return = taoh_remote_cache( $var_arr$value );
                $return = taoh_remote_cache($value);
                break;
        }

    }
    return $return;
}

if ( ! function_exists( 'taoh_scope_key_encode' ) ){
    function taoh_scope_key_encode( $key, $scope = TAOH_CACHE_SCOPE ){
        switch ( strtolower( $scope ) ){
            case 'private':
                $return = hash( 'sha256', $key.TAOH_API_TOKEN );
                break;
            case 'local':
                $return = hash( 'sha256', $key.TAOH_API_SECRET );
                break;
            default:
                $return = hash( 'sha256', $key );
                break;
        }
        return $return;
    }
}

function taoh_cache_remove_local($string)
{
    foreach (glob(TAOH_PLUGIN_PATH . "/cache/general/*" . $string . "*") as $f) {
        unlink($f);
    }
    return 1;
}

function taoh_cache_cleaner()
{
    if (defined('TAOH_API_TOKEN') && !rand(0, 100)) {
        foreach (glob(TAOH_PLUGIN_PATH . "/cache/general/*") as $f) {
            if (stristr($f, '.cache')) {
                $mtime = filemtime($f);
                $expiry = 60 * 60 * 6;
                if ((time() - $mtime) >= $expiry) {
                    unlink($f);
                }
            }
        }
    }
    return 1;
}

if (!function_exists('taoh_cache_remove')) {
    function taoh_cache_remove($string)
    {
        taoh_cache_remove_local($string);
        taoh_file_get_contents(TAOH_API_PREFIX . '/cache/beam?name=' . $string . '&ops=remove');
        //$url = array();
        //$url[] = TAOH_API_PREFIX.'/cache/beam?name='.$string.'&ops=remove';
        //taoh_apicall_post( "scripts/multiurl.php?url=add", array('url' => $url), TAOH_OPS_PREFIX );
        return 1;
    }
}


function taoh_remote_cache($var_arr)
{
    $return = false;

    if (isset($var_arr['ops']) && $var_arr['ops']) {

        $taoh_url = TAOH_CACHE_PREFIX;
        if (isset($var_arr['url']) && $var_arr['url']) {
            $taoh_url = $var_arr['url'];
        }
        $taoh_vals = array();

        switch (strtolower($var_arr['ops'])) {
            case 'get':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'get',
                    'code' => TAOH_OPS_CODE,
                );
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                //$out = json_decode($return, true);
                // $return = json_decode( $out[ 'output' ], true );
                break;
            case 'get_mstamp':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'get_mstamp',
                    'code' => TAOH_OPS_CODE,
                );
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                //$out = json_decode($return, true);
                // $return = json_decode( $out[ 'output' ], true );
                break;
            case 'keys':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'keys',
                    'code' => TAOH_OPS_CODE,
                );
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                //$out = json_decode($return, true);
                // $return = json_decode( $out[ 'output' ], true );
                break;
            case 'delete':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'delete',
                    'code' => TAOH_OPS_CODE,
                );
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                $return = array("success" => true, "output" => true);
                break;
            case 'set':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'set',
                    'code' => TAOH_OPS_CODE,
                    'value' => $var_arr['value'],
                );
                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;
            case 'lpush':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'lpush',
                    'code' => TAOH_OPS_CODE,
                    'value' => $var_arr['value'],
                );
                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;
            case 'lpushu':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'lpushu',
                    'code' => TAOH_OPS_CODE,
                    'value' => $var_arr['value'],
                );
                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;
            case 'lrange':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'lrange',
                    'code' => TAOH_OPS_CODE,
                );
                if (isset($var_arr['start']) && $var_arr['start']) {
                    $taoh_vals['start'] = $var_arr['start'];
                }
                if (isset($var_arr['end']) && $var_arr['end']) {
                    $taoh_vals['end'] = $var_arr['end'];
                }

                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                //$out = json_decode($return, true);
                // $return = json_decode( $out[ 'output' ], true );
                break;

            case 'ldelete':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'ldelete',
                    'code' => TAOH_OPS_CODE,
                );
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                $return = array("success" => true, "output" => true);
                break;
            case 'lpos':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'lpos',
                    'code' => TAOH_OPS_CODE,
                    'value' => $var_arr['value'],
                );
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                //$return = array( "success" => true, "output" => true );
                break;
            case 'append':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'append',
                    'code' => TAOH_OPS_CODE,
                    'value' => $var_arr['value'],
                );
                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;

            case 'livepost':
                //print_r($var_arr);
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'livepost',
                    'code' => TAOH_OPS_CODE,
                    'value' => $var_arr['value'],
                    'ptoken' => $var_arr['ptoken'],
                    'latitude' => $var_arr['latitude'],
                    'longitude' => $var_arr['latitude'],
                );
                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;
            case 'livesearch':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'livesearch',
                    'code' => TAOH_OPS_CODE,
                    'ptoken' => $var_arr['ptoken'],
                    'latitude' => $var_arr['latitude'],
                    'longitude' => $var_arr['longitude'],

                );
                if (isset($var_arr['search']) && $var_arr['search']) $taoh_vals['search'] = $var_arr['search'];
                if (isset($var_arr['radius']) && $var_arr['radius']) $taoh_vals['radius'] = $var_arr['radius'];
                if (isset($var_arr['unit']) && $var_arr['unit']) $taoh_vals['unit'] = $var_arr['unit'];
                if (isset($var_arr['offset']) && $var_arr['offset']) $taoh_vals['offset'] = $var_arr['offset'];
                if (isset($var_arr['limit']) && $var_arr['limit']) $taoh_vals['limit'] = $var_arr['limit'];


                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;

            case 'chatrooms':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'chatrooms',
                    'code' => TAOH_OPS_CODE,
                    'ptoken' => $var_arr['ptoken'],
                    //'latitude'  => $var_arr['latitude'],
                    // 'longitude' => $var_arr['longitude'],

                );


                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;

            case 'roomadd':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'roomadd',
                    'code' => TAOH_OPS_CODE,
                    'ptoken' => $var_arr['ptoken'],
                    'addptoken' => $var_arr['addptoken'],
                    //'latitude'  => $var_arr['latitude'],
                    // 'longitude' => $var_arr['longitude'],

                );


                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;

            case 'chatadd':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'chatadd',
                    'code' => TAOH_OPS_CODE,
                    'ptoken' => $var_arr['ptoken'],
                    'toptoken' => $var_arr['toptoken'],
                    'chat' => $var_arr['chat'],

                );


                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;

            case 'chatget':
                $taoh_vals = array(
                    'key' => $var_arr['key'],
                    'ops' => 'chatget',
                    'code' => TAOH_OPS_CODE,
                    'ptoken' => $var_arr['ptoken'],
                );


                if (isset($var_arr['offset']) && $var_arr['offset']) {
                    $taoh_vals['offset'] = $var_arr['offset'];
                }
                if (isset($var_arr['limit']) && $var_arr['limit']) {
                    $taoh_vals['limit'] = $var_arr['limit'];
                }
                if (isset($var_arr['ttl']) && $var_arr['ttl']) {
                    $taoh_vals['ttl'] = $var_arr['ttl'];
                }
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;

            case 'uuid':
                $taoh_url = TAOH_CACHEOPS_PREFIX;
                $taoh_vals = array(
                    'code' => $var_arr['code'],
                    'ops' => 'uuid',
                    'value' => $var_arr['value'],
                    'status' => $var_arr['status'],
                );
                //print_r($taoh_vals);die();
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                    echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);//exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;
            case 'subsecret':
                $taoh_url = TAOH_CACHEOPS_PREFIX;
                $taoh_vals = array(
                    'code' => $var_arr['code'],
                    'ops' => 'subsecret',
                    'value' => $var_arr['value'],
                    'status' => $var_arr['status'],
                    //'debug' => 1,
                );
                //print_r($taoh_vals);die();
                if (isset($var_arr['debug']) && $var_arr['debug']) {
                  //  echo "<br>" . $taoh_url . "?" . http_build_query($taoh_vals);exit();
                }
                $return = taoh_post($taoh_url, $taoh_vals);
                break;
            default:
                break;

        }
        //echo $return;
        // $return = $out[ 'output' ];

    }
    return $return;
}


function taoh_file_get_contents($url)
{
    $opts = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    $context = stream_context_create($opts);
    return file_get_contents(str_replace('&amp;', '&', $url), false, $context);
}

function taoh_apicall_get($taoh_call, $taoh_vals, $prefix = TAOH_API_PREFIX, $cache_enable = 0)
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

    $data = taoh_api_request($taoh_call, $taoh_vals, 'GET', $prefix);
    return $data['response'] ?? '';
}

function event_time_display_lobby($input_date, $geo_enable = 0, $event_timezone = 'America/New_york', $input = 'date', $format = 'D, M d, Y h:i A')
{

    //geo_enable = 1 means event is local
    //geo_enable = 0 means event is global

    //echo "==========".$geo_enable;
    if (!$geo_enable) {
        $timezone1 = new DateTimeZone($event_timezone);
    } else { //local
        $timezone = taoh_user_timezone();
        $timezone1 = new DateTimeZone($timezone);
    }


    $local_tz = $timezone1;
    $local = new DateTime('now', $local_tz);
    $local_offset = $local->getOffset();

    $date = new DateTime($input_date);
    $regularDate = $date->format('Y-m-d H:i:s');

    //echo $regularDate;

    $final = strtotime($regularDate) + ($local_offset);


    //die();
    //echo"<br>=input=====".date("D, M d, Y h:i A", strtotime($input_date));
    return date($format, $final);


}

function testfile($taoh_url, $current_timestamp, $response_timestamp, $taoh_type = 'GET')
{
    $api_time = abs($response_timestamp - $current_timestamp) / 1000;
    if (TAOH_API_LOG_ENABLE && TAOH_API_LOG_ENABLE == 1 && defined('TAOH_API_TOKEN') && TAOH_API_TOKEN != '') {
        //$filename = TAOH_PLUGIN_PATH.'/cache/api_'.date('dmY').'.logs';
        $value_array = json_encode(
            array(
                'token' => TAOH_API_TOKEN,
                'taoh_url' => $taoh_url,
                'taoh_type' => $taoh_type,
                'start_time' => $current_timestamp,
                'end_time' => $response_timestamp,
                'api_process_time' => $api_time,
            )
        );
        $cache_value = $value_array . "\r\n ----------------------------------- \r\n";
        return $value_array;
        //file_put_contents($filename, $cache_value , FILE_APPEND | LOCK_EX);
    }
}

function logfile_write($taoh_call, $taoh_vals, $taoh_type, $prefix, $cache_file_name, $current_timestamp, $response_timestamp)
{
    $api_time = abs($response_timestamp - $current_timestamp) / 1000;
    if (TAOH_API_LOG_ENABLE && TAOH_API_LOG_ENABLE == 1) {
        $filename = TAOH_PLUGIN_PATH . '/cache/api_' . date('dmY') . '.logs';
        $value_array = json_encode(
            array(
                'taoh_call' => $taoh_call,
                'taoh_vals' => $taoh_vals,
                'taoh_type' => $taoh_type,
                'prefix' => $prefix,
                'cache_name' => $cache_file_name,
                'start_time' => $current_timestamp,
                'end_time' => $response_timestamp,
                'api_process_time' => $api_time,
            )
        );
        $cache_value = $value_array . "\r\n ----------------------------------- \r\n";
        file_put_contents($filename, $cache_value, FILE_APPEND | LOCK_EX);
    }
}

/*function taoh_api_url_old($taoh_call, $taoh_vals, $taoh_type = 'GET', $prefix = TAOH_API_PREFIX, $cache_enable = 0)
{
    $current_timestamp = time();
    //echo 'current time -------'.$current_timestamp;
    $file_mod = $taoh_vals['mod'] ?? '';
    $cache_file_build = http_build_query($taoh_vals);
    if (isset($taoh_vals['cache_name'])) {
        $cache_file_name = $taoh_vals['cache_name'];
        unset($taoh_vals['cache_name']);
    } else {
        $cache_file_name = $file_mod . '_' . hash('crc32', $cache_file_build);
    }

    //echo "==========".$taoh_type;
    if ($taoh_type == 'GET' && $file_mod != 'rooms' && $file_mod != 'tao_tao' && $file_mod != '') {
        //$taoh_vals['cache'] = $cache_file_name;
        //$taoh_vals['caching'] = $cache_file_name;
        if (!isset($taoh_vals['cache_required']) || $taoh_vals['cache_required'] != 0) {
            $taoh_vals['cache'] = array("name" => $cache_file_name);
        }
    }

    $cache_time = 0;
    if (isset($taoh_vals['cache_time'])) {
        $cache_time = $taoh_vals['cache_time'];
        unset($taoh_vals['cache_time']);
    }

    $taoh_vals = array(
        'taohapi' => $taoh_call,
        'taohapiserv' => $prefix,
        'taohcode' => TAOH_OPS_CODE,
        'code' => TAOH_OPS_CODE,
        'taohmethod' => $taoh_type,
        'value' => $taoh_vals,
    );
    $url = TAOH_CACHEAPI_PREFIX;

    //echo'<pre>-----taoh_vals------';print_r($cache_enable);echo'</pre>';


    //echo "$url?".http_build_query( $taoh_vals )."<br>";exit();
    //$return =  taoh_get( $url, $taoh_vals );

    //print_r($taoh_api_arr);exit();
    $postdata = http_build_query($taoh_vals);

    // echo'<pre>-----cache_file_name------';print_r($cache_file_name);echo'</pre>';

    $aaa = parse_url($postdata);
    //echo'<pre>-----parse_url------';print_r($aaa);echo'</pre>';
    //$url = $taoh_api_arr[ 'taohapiserv' ] ."/". $taoh_api_arr[ 'taohapi' ];
    if ($taoh_type == 'GET') {
        $opts = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $url = str_replace('&amp;', '&', $url . '?' . html_entity_decode($postdata));
        if($file_mod !=''){
            $fold = TAOH_PLUGIN_PATH . '/cache/general/' . $file_mod;
            if (!file_exists($fold)) {
                mkdir($fold, 0777, true);
            }
            $get_file_name = TAOH_PLUGIN_PATH . '/cache/general/' . $file_mod.'/'.$cache_file_name . '.cache';
        }
        else
        $get_file_name = TAOH_PLUGIN_PATH . '/cache/general/' . $cache_file_name . '.cache';

        if(isset($taoh_vals['cache_required']) && $taoh_vals['cache_required'] == 0){
           //no data come from local cache too
        }
        else{
            if (file_exists($get_file_name)) {
                if (time() - filemtime($get_file_name) >= $cache_time * 60) {
                    unlink($get_file_name);
                } else {
                    $get_return = file_get_contents($get_file_name);
                    //print_r('$get_return');die;
                    $response_timestamp = time();
                    logfile_write($taoh_call, $taoh_vals, $taoh_type, $prefix, $cache_file_name, $current_timestamp, $response_timestamp);
                    return $get_return;
                }
            }
        }

        $response_timestamp = time();
        //echo $url;die();
    } else {
        $opts = array('http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                "cfcache-disable: true\r\n",
            'content' => $postdata,
        ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
    }
    //echo"======context=======".$opts;
    //echo'<pre>';print_r($opts);echo'</pre>';
    //echo "***==============".$url;die();

    $context = stream_context_create($opts);
    //echo'<pre>';print_r($taoh_vals);//die();
    if (isset($taoh_vals['value']['debug']) && $taoh_vals['value']['debug'] == 1) {
        echo "=========" . $url;
        echo '<pre>';
        print_r($postdata);
        die();
    }
    $result = file_get_contents($url, false, $context);
    //if($taoh_type == 'POST'){
    $response_timestamp = time();
    //}
    //echo 'end time -------'.$response_timestamp;
    //echo 'api response -------'.$response_timestamp - $current_timestamp;//die();
    logfile_write($taoh_call, $taoh_vals, $taoh_type, $prefix, $cache_file_name, $current_timestamp, $response_timestamp);

    //if ($taoh_type == 'GET' && $cache_enable) {

    $write_cache = 1;
    if(isset($taoh_vals['cache_required']) && $taoh_vals['cache_required'] == 0){
        $write_cache = 0;
    }


    if (taoh_isjson($result)) {
        $return = json_decode($result, true);
    } else {
        $return = $result;
    }
    //if ($taoh_type == 'GET' && $cache_enable) {
    if ($taoh_type == 'GET' && $write_cache) {


        if (taoh_isjson($result)) {
            $return = json_decode($result, true);
        } else {
            $return = $result;
        }

        //echo "==========".$get_file_name;die();

        if (isset($return['success']) && isset($return['output']) && $return['success'] == 1) {
            //print_r($return['success']);die();
            if (!file_exists($get_file_name)) {
                $fp = fopen($get_file_name, 'w');
                fwrite($fp, $result);
                fclose($fp);
            }
        }
    }

    return $result;
}*/

function taoh_api_url($taoh_call, $taoh_vals, $taoh_type = 'GET', $prefix = TAOH_API_PREFIX, $cache_enable = 0)
{
    $current_timestamp = time();
    $file_mod = $taoh_vals['mod'] ?? '';
    $write_cache = !isset($taoh_vals['cache_required']) || $taoh_vals['cache_required'] != 0;
    $cache_time = $taoh_vals['cache_time'] ?? 7200;
    $cache_file_build = http_build_query($taoh_vals);

    $local_cache = defined('TAOH_LOCAL_CACHE_ENABLE') && TAOH_LOCAL_CACHE_ENABLE ? 1 : 0;

    // Determine cache file name
    $cache_file_name = $taoh_vals['cache_name'] ?? $file_mod . '_' . hash('crc32', $cache_file_build);
    $cache_file_path = $local_cache
        ? TAOH_PLUGIN_PATH . '/cache/general/' . ($file_mod ? "$file_mod/" : '') . $cache_file_name . '.cache'
        : TAOH_PLUGIN_PATH . '/cache/general/' . $cache_file_name . '.cache';

    if ($local_cache && $file_mod && !file_exists(dirname($cache_file_path))) {
        mkdir(dirname($cache_file_path), 0777, true);
    }

    // Clean up unused parameters
    unset($taoh_vals['cache_name'], $taoh_vals['cache_time']);

    // Add cache parameters for GET requests
    if ($taoh_type === 'GET' && $write_cache && $file_mod && !in_array($file_mod, ['rooms', 'tao_tao'], true)) {
        $taoh_vals['cache'] = array("name" => $cache_file_name, "cf_name" => $cache_file_name,  "ttl" => $cache_time);

    }

    // Prepare API request parameters
    $taoh_vals = [
        'taohapi' => $taoh_call,
        'taohapiserv' => $prefix,
        'taohcode' => TAOH_OPS_CODE,
        'code' => TAOH_OPS_CODE,
        'taohmethod' => $taoh_type,
        'value' => $taoh_vals,
    ];
    $url = TAOH_CACHEAPI_PREFIX;
    $postdata = http_build_query($taoh_vals);

    // Handle caching for GET requests
    if ($taoh_type === 'GET' && $write_cache && $local_cache && file_exists($cache_file_path)) {
        $cache_age = time() - filemtime($cache_file_path);
        if ($cache_age < $cache_time * 60) {
            logfile_write($taoh_call, $taoh_vals, $taoh_type, $prefix, $cache_file_name, $current_timestamp, time());
            return file_get_contents($cache_file_path);
        }
    }


    // Set HTTP context options
    $opts = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];

    if ($taoh_type === 'POST') {
        $opts['http'] = [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\ncfcache-disable: true\r\n",
            'content' => $postdata,
        ];
    } else {
        $url = str_replace('&amp;', '&', $url . '?' . html_entity_decode($postdata));
    }

    if (isset($taoh_vals['value']['debug']) && $taoh_vals['value']['debug'] == 1) {
        echo "=========" . $url . "======= <pre>";
        print_r($postdata);
        die();
    }

    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    $response_timestamp = time();

    logfile_write($taoh_call, $taoh_vals, $taoh_type, $prefix, $cache_file_name, $current_timestamp, $response_timestamp);

    // Cache the response for GET requests
    if ($taoh_type === 'GET' && $write_cache && $local_cache) {
        $response_data = taoh_isjson($result) ? json_decode($result, true) : $result;
        if (!empty($response_data['success']) && $response_data['success'] == 1 && isset($response_data['output'])) {
            file_put_contents($cache_file_path, $result);
        }
    }

    return $result;
}


function taoh_session_save($key, $value, $debug = false)
{
    if ($debug) {
        echo "Session Key: " . $key . " Value: " . $value;
        die;
    }
    $_SESSION[$key] = $value;
}

function taoh_session_get($key, $debug = false)
{
    if($key == TAOH_ROOT_PATH_HASH){
        if(!taoh_user_is_logged_in()){
            return null;
        }
    }
    if ($debug) {
        echo "Session Key: " . $key . " Value: " . $_SESSION[$key];
        die;
    }
    if (isset($_SESSION[$key]) && (is_object($_SESSION[$key]) || is_array($_SESSION[$key]))) {
        $session_return = $_SESSION[$key];
    } else {
        $session_return = null;
    }
    return $session_return ?? null;
}

function taoh_delete_local_cache($mods,$remove_array)
{
    return 1;
    $dir = TAOH_PLUGIN_PATH . '/cache/general/';
    foreach ($remove_array as $pattern) {


        if($mods == ''){
            if (strpos($pattern, 'event') !== false) $mods = 'events';
            if (strpos($pattern, 'job') !== false) $mods = 'jobs';
            if (strpos($pattern, 'ask') !== false) $mods = 'asks';
            if (strpos($pattern, 'profile') !== false) $mods = 'tao_tao';
            if (strpos($pattern, 'read') !== false) $mods = 'core';
            if (strpos($pattern, 'recipe') !== false) $mods = 'core';
            if (strpos($pattern, 'tags') !== false) $mods = 'core';

        }
        $dir = $dir.$mods.'/'; //kalpana added


        $hasWildcard = str_contains($pattern, '*');
        $hasExtension = pathinfo($pattern, PATHINFO_EXTENSION) !== '';

        $searchPattern = $dir . $pattern;

        if (!$hasWildcard && !$hasExtension) {
            if (is_file($searchPattern)) {
                unlink($searchPattern);
            }
            $searchPattern .= '.cache';
            if (is_file($searchPattern)) {

                unlink($searchPattern);
            }
        }

        $files = glob($searchPattern);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

function taoh_get($url, $taoh_vals)
{
    $postdata = http_build_query($taoh_vals);

    if(isset($taoh_vals['debug']) && $taoh_vals['debug'] == 1) {
        echo $url . "?" . $postdata;
        die;
    }

    $opts = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    $context = stream_context_create($opts);
    $url = str_replace('&amp;', '&', $url . '?' . html_entity_decode($postdata));
    $result = file_get_contents($url, false, $context);

    return $result;
}

function taoh_apicall_post($taoh_call, $taoh_vals, $prefix = TAOH_API_PREFIX, $cache_enable = 0)
{
    /*//$url = $prefix.'/'.$taoh_call;
    //return taoh_post( $url, $taoh_vals );
    $taoh_vals['source'] = TAOH_SITE_URL_ROOT;
    //$taoh_vals['sub_secret_token'] = TAOH_ROOT_PATH_HASH;
    $taoh_vals['sub_secret_token'] = TAOH_ROOT_PATH_HASH;
    //hash('crc32',TAOH_SITE_URL_ROOT);
    $return = taoh_api_url($taoh_call, $taoh_vals, 'POST', $prefix, $cache_enable);
    return $return;*/

    $data = taoh_api_request($taoh_call, $taoh_vals, 'POST', $prefix);
    return $data['response'] ?? '';
}


if (!function_exists('taoh_logfile_write')) {
    function taoh_logfile_write($log_file_path, $data)
    {
        if (!empty($log_file_path) && !file_exists(dirname($log_file_path))) {
            mkdir(dirname($log_file_path), 0777, true);
        }

        if(!is_string($data)) $data = json_encode($data);

        $log_data = "\r\n -----------------" . date('d-m-Y H:i:s') . "------------------ \r\n";
        $log_data .= $data;
        file_put_contents($log_file_path, $log_data, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('taoh_api_request')) {
    /**
     * Make an API request to the Taoh API
     * @param string $taoh_call (required)
     * @param array $taoh_vals (optional) Default is an empty array
     * @param string $method (GET, POST, PUT, DELETE) (optional) Default is GET
     * @param string $prefix (optional) Default is TAOH_API_PREFIX
     * @param array $headers (optional) Default is an empty array
     * @param array $options (optional) Default is an empty array
     * @param array $extra (optional) Default is an empty array
     *
     * @return mixed $result
     * @throws Exception
     * @since 1.0
     */
    function taoh_api_request($taoh_call, $taoh_vals = [], $method = 'GET', $prefix = TAOH_API_PREFIX, $headers = [], $options = [], $extra = [])
    {
        $is_api_log_enable = defined('TAOH_API_LOG_ENABLE') && !empty(TAOH_API_LOG_ENABLE);

        if (empty($prefix)) $prefix = TAOH_API_PREFIX;

        $file_mod = $taoh_vals['mod'] ?? '';
        $write_cache = !isset($taoh_vals['cache_required']) || $taoh_vals['cache_required'] != 0;
        $cache_time = $taoh_vals['ttl'] ?? 7200;
        $cache_file_build = http_build_query($taoh_vals);
        $cache_file_name = $taoh_vals['cache_name'] ?? $file_mod . '_' . hash('crc32', $cache_file_build);


        // Clean up unused parameters
        unset($taoh_vals['cache_name'], $taoh_vals['cache_time'], $taoh_vals['ttl']);

        // Add cache parameters for GET requests
        if ($method === 'GET' && $write_cache && $file_mod && !in_array($file_mod, ['rooms', 'tao_tao'], true)) {
            $taoh_vals['cache'] = array("name" => $cache_file_name, "cf_name" => $cache_file_name, "ttl" => $cache_time);
        }

        $taoh_vals['source'] = TAOH_SITE_URL_ROOT;
        $taoh_vals['sub_secret_token'] = TAOH_ROOT_PATH_HASH;

        // Prepare API request parameters
        $data = [
            'taohapi' => $taoh_call,
            'taohapiserv' => $prefix,
            'taohcode' => TAOH_OPS_CODE,
            'code' => TAOH_OPS_CODE,
            'taohmethod' => $method,
            'value' => $taoh_vals,
        ];

        $url = TAOH_CACHEAPI_PREFIX;

        if ($is_api_log_enable && (TAOH_API_LOG_ENABLE == 1 || TAOH_API_LOG_ENABLE == 2)) {
            $log_file_path = TAOH_PLUGIN_PATH . '/cache/api_' . date('dmY') . '.logs';
            taoh_logfile_write($log_file_path, ['url' => $url, 'method' => $method, 'data' => $data]);
        }

        if (isset($taoh_vals['debug']) && $taoh_vals['debug'] == 1) {
            $postdata = http_build_query($data);
            $url_print = str_replace('&amp;', '&', $url . '?' . html_entity_decode($postdata));

            echo $url_print;
            //die();
        }

        if (isset($taoh_vals['debug_api']) && $taoh_vals['debug_api'] == 1) {
            $postdata = http_build_query($taoh_vals);
            $url_api = TAOH_API_PREFIX . '/' . $taoh_call;
            $url_print = str_replace('&amp;', '&', $url_api . '?' . html_entity_decode($postdata));

            echo $url_print;
            // die();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                break;
            default:
                if (!empty($data)) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                    curl_setopt($ch, CURLOPT_URL, $url);
                }
                break;
        }

        // Default headers
        $default_headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        // Set headers
        $final_headers = array_merge($default_headers, $headers);
        if (!empty($final_headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $final_headers);
        }

        // Set additional options
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36";

        // Set options to return the response and not output it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log('cURL error: ' . $error);
            // throw new Exception('cURL error: ' . $error);
        }

        // Get HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($is_api_log_enable && TAOH_API_LOG_ENABLE == 2) {
            $log_file_path = TAOH_PLUGIN_PATH . '/cache/api_' . date('dmY') . '.logs';
            taoh_logfile_write($log_file_path, ['http_code' => $httpCode, 'response' => $response]);
        }

        return ['http_code' => $httpCode, 'response' => $response];
    }

}

if (!function_exists('taoh_post')) {
    function taoh_post($url, $taoh_vals, $headers = [], $debug = 0, $post_data_type = "http")
    {
        if($post_data_type == "http") {
            $postdata = http_build_query($taoh_vals);
        } else {
            $postdata = json_encode($taoh_vals);
        }

        if(isset($taoh_vals['debug']) && $taoh_vals['debug'] == 1) {
            echo $url . "?" . $postdata;
             die;
        }

        // Default headers
        $default_headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Content-Length' => strlen($postdata),
            'cfcache-disable' => 'true'
        ];

        // Combine default headers with function parameter headers
        $final_headers = array_merge($default_headers, $headers);

        $header_string = '';
        foreach ($final_headers as $key => $value) {
            $header_string .= "$key: $value\r\n";
        }

        // Set up the context options
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => $header_string,
                'content' => $postdata,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);

        // Debug logging
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            $url_print = "URL: [$url]; PostData: [$postdata];";
            $data = [$url_print];
            $filename = TAOH_PLUGIN_PATH . '/cache/log/' . date("y-m-d") . '_logs.cache';
            $fp = fopen($filename, file_exists($filename) ? 'a' : 'w');
            if ($fp) {
                if (file_exists($filename)) {
                    fwrite($fp, "\n" . time() . '-' . $url_print);
                } else {
                    fputcsv($fp, $data);
                }
                fclose($fp);
            }
        }

        return $result;
    }
}

function taoh_apicall($taoh_call, $taoh_call_type, $taoh_vals, $prefix = TAOH_API_PREFIX)
{
    if (isset($taoh_call_type) && $taoh_call_type) {
        switch (strtolower($taoh_call_type)) {
            case 'post':
                $return = taoh_apicall_post($taoh_call, $taoh_vals, $prefix);
                break;
            case 'get':
            default:
                $return = taoh_apicall_get($taoh_call, $taoh_vals, $prefix);
                break;
        }
        //echo $return;taoh_exit();
        return $return;
    }
}

if (!function_exists('taoh_exit')) {
    function taoh_exit()
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        exit();
    }
}


function taoh_available_apps()
{
    $appfolder = dirname(__FILE__) . '/app';
    $iterator = new DirectoryIterator($appfolder);
    $applist = array();
    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isDir() && !$fileinfo->isDot()) {
            array_push($applist, $fileinfo->getFilename());
        }
    }
    return $applist;
}

function taoh_parse_url($level = 0, $caselower = 1)
{
    $full_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $pre_url = TAOH_SITE_URL_ROOT;
    //echo " $full_url  $pre_url ";exit();
    list($pre, $post) = explode($pre_url, $full_url);
    $url = explode('/', trim($post, '/'));
    if (is_array($url)) {
        $d = $url;
        $d = array_filter($d); //remove empty
        //print_r($d);die();
        if ($caselower) {
            if (isset($d[$level])) {
                if (str_contains($d[$level], '?')) {
                    $d[$level] = explode('?', $d[$level])[0];
                }
                //return strtolower($d[$level]);
                return strtolower($d[$level]);
            } else {
                return '';
            }
        }
        return $d[$level] ?? '';
    }
    return '';
}

function taoh_parse_url_lp($level = 0, $caselower = 1)
{
    $full_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $pre_url = TAOH_TEMP_SITE_FILE_PARSE;
    //echo " $full_url  $pre_url ";exit();
    list($pre, $post) = explode($pre_url, $full_url);
    if ($post && $post != '') {
        $url = explode('/', trim($post, '/'));
        if (is_array($url)) {
            $d = $url;
            $d = array_filter($d); //remove empty
            //print_r($d);die();
            if ($caselower) {
                if (isset($d[$level])) {
                    if (str_contains($d[$level], '?')) {
                        $d[$level] = explode('?', $d[$level])[0];
                    }
                    //return strtolower($d[$level]);
                    return strtolower($d[$level]);
                } else {
                    return '';
                }
            }
            return $d[$level];
        }
    }
    return '';
}

function taoh_parse_url_parse()
{
    $full_url = $_SERVER['REQUEST_URI'];
    $pre_url = TAOH_PLUGIN_PATH_NAME;
    //echo " $full_url  $pre_url ";exit();
    list($pre, $post) = explode($pre_url, $full_url);
    $url = explode('/', trim($post, '/'));
    //print_r ($d);
    return $url;

}

function taoh_user_is_logged_in()
{
    if (taoh_get_api_token()) {
        return true;
    }
    return false;
}

function taoh_get_dummy_token($force = false)
{
    if ($force) {

        return TAOH_API_TOKEN_DUMMY;

    }
    else{
        if (taoh_user_is_logged_in()) {
            return taoh_get_api_token();
        }
    }
     return TAOH_API_TOKEN_DUMMY;

}

function taoh_get_user_info($ptoken, $ops = 'public',$fresh=0)
{
    $return = array();
    if (isset($ptoken) && $ptoken != '') {

        $mod = 'users';
        $taoh_call = 'users.user.get';
        //$cache_name = $mod.'_'.$ops.'_' . $ptoken . '_' . taoh_scope_key_encode( $ptoken, 'global' );
        $cache_name = 'users'.'_'.$mod.'_'.$ops.'_' . $ptoken;
        if($fresh){
            $cache_name = $cache_name .'_'.time();
        }
        $taoh_vals = array(
            'token' => taoh_get_dummy_token(1),
            'ops' => $ops,
            'mod' => $mod,
            'cache_name' => $cache_name,
            'cache_time' => 7200,
            'ptoken' => $ptoken,
            //'debug'=>1

        );
       // $taoh_vals[ 'cfcache' ] = $cache_name;
        ksort($taoh_vals);

        //echo "====ss sss===";
        if($ptoken == 'z3mbltb5mrf1'){
           // $taoh_vals[ 'debug' ] = 1;
           //echo "========".taoh_apicall_get_debug($taoh_call, $taoh_vals);die();
        }
        // echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die();

        $return = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
        return $return;
        //print_r($return);die();
    }
    return $return;
}

//Getting User Timezone if guest get from cookie
function get_user_timezone($global, $event_timezone)
{
    if ($global) {
        $timezone = taoh_user_timezone();
    } else {
        $timezone = $event_timezone;
    }
    return $timezone;
}

function taoh_update_user_as_anonymous($token)
{

    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_anonymous']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_anonymous'] == 1) {
        $userAnonymousData = array();
        $user_data = taoh_user_all_info_settings($token);
        //echo'<pre>-------------';print_r($user_data);echo'</pre>';die;
        $files = glob(realpath('./assets/images/avatar/PNG/128') . '/anonymous-*.png');
        $file = array_rand($files);
        $url = $files[$file];
        $pathinfo = pathinfo($url);
        $image = $pathinfo['filename'];
        $username = explode('-', $pathinfo['filename']);
        //echo $username[0];

        //die();

        //'simple_login' => TAOH_SIMPLE_LOGIN
        $email = $user_data->email;
        $chat_name = explode('@', $email)[0];

        $userAnonymousData['simple_login'] = TAOH_SIMPLE_LOGIN;
        $userAnonymousData['profile_complete'] = 0;
        $userAnonymousData['created_via'] = 'email';

        $userAnonymousData['fname'] = TAOH_SITE_NAME_SLUG;
        $userAnonymousData['lname'] = 'member';
        $userAnonymousData['avatar'] = $image;
        $userAnonymousData['type'] = 'professional';
        $userAnonymousData['chat_name'] = $chat_name;

        /*$userAnonymousData['fname'] = $username[0];
        $userAnonymousData['lname'] = $username[1];


        $userAnonymousData['chat_name'] = $pathinfo['filename'] . '-' . $user_data->ptoken;*/


        $userAnonymousData['local_timezone'] = $_COOKIE['client_time_zone'];


        $taoh_call = 'users.tao.add';
        $taoh_call_type = 'POST';
        $taoh_vals = array(
            'token' => $token,
            'mod' => 'tao_tao',
            'toenter' => $userAnonymousData,

        );

        //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die;
        $result = taoh_apicall_post($taoh_call, $taoh_vals);
        taoh_user_all_info_settings($token);

        setcookie(TAOH_ROOT_PATH_HASH . '_anonymous', 0, strtotime('-2 days'), '/');
    }
}

function taoh_user_all_info($must = 0)
{
    if (!taoh_user_is_logged_in()) {
        return 0;
    }

    $sessionData = taoh_session_get(TAOH_ROOT_PATH_HASH) ?? [];

    if (empty($sessionData['USER_INFO']) || $must == 1) {
        $taoh_vals = [
            'mod' => 'tao_tao',
            'token' => taoh_get_api_token(),
            'cache_name' => 'profile_short_' . taoh_get_api_token(),
            'time' => time(),
        ];

        $user_data = json_decode(taoh_apicall_get("users.tao.get", $taoh_vals));
        taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => $user_data]);
    }

    return taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
}

function taoh_user_all_info_settings($token, $must = 0)
{
    //if(taoh_user_is_logged_in()) {
    $taoh_vals = array(
        'mod' => 'tao_tao',
        'token' => $token
    );

    if ($must == 1) {
        $taoh_vals['cache_required'] = 0;
    } else {
        $taoh_vals['cache_name'] = 'profile_settings_' . $token;
        //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
    }

    $taoh_call = "users.tao.get";
    $taoh_call_type = "get";

    $user_result = json_decode(taoh_apicall($taoh_call, $taoh_call_type, $taoh_vals));
    //taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => $user_result]);
    //}
    return $user_result;
    /* } else {
    return 0;
  } */

}

if (!function_exists('adjustTimezone')) {
    function adjustTimezone($timezone) {
        $timezoneMap = [
            'Asia/Calcutta' => 'Asia/Kolkata',
            'Asia/Katmandu' => 'Asia/Kathmandu',
            'US/Eastern' => 'America/New_York',
            'US/Central' => 'America/Chicago',
            'US/Mountain' => 'America/Denver',
            'US/Pacific' => 'America/Los_Angeles',
            'Etc/Greenwich' => 'Etc/GMT',
            'GMT+0' => 'Etc/GMT',
            'GMT-0' => 'Etc/GMT',
            'Etc/UCT' => 'Etc/UTC',
            'Australia/Canberra' => 'Australia/Sydney',
            'Pacific/Truk' => 'Pacific/Chuuk',
            'Pacific/Yap' => 'Pacific/Chuuk',
        ];

        return $timezoneMap[$timezone] ?? $timezone;
    }
}

if (!function_exists('isValidTimezone')) {
    function isValidTimezone($timezone) {
        return in_array($timezone, timezone_identifiers_list(), true);
    }
}

if (!function_exists('taoh_user_timezone')) {
    function taoh_user_timezone() {
        $timezone = !empty($_SESSION['user_timezone']) ? $_SESSION['user_timezone'] : null;

        if (!$timezone) {
            $user = taoh_user_all_info();
            $timezone = !empty($user->local_timezone) ? $user->local_timezone : null;
            $_SESSION['user_timezone'] = $timezone;
        }

        if (!$timezone && !empty($_COOKIE['client_time_zone'])) {
            $timezone = $_COOKIE['client_time_zone'];
        }

        if(empty($timezone)) $timezone = date_default_timezone_get();

        // Adjust mismatched timezones between PHP and JS
        return adjustTimezone($timezone);
    }
}

function taoh_user_type()
{
    $logged_user = taoh_user_all_info();
    if (isset($logged_user->type)) {
        return $logged_user->type;
    }
}

function taoh_user_nice_name()
{
    $logged_user = taoh_user_all_info();
    if (isset($logged_user->fname)) {
        return ucfirst($logged_user->fname);
    }
}

function taoh_user_full_name()
{
    $logged_user = taoh_user_all_info();
    if (isset($logged_user->fname)) {
        return ucfirst($logged_user->fname) . " " . ucfirst($logged_user->lname);
    }
}

function taoh_user_avatar()
{
    $logged_user = taoh_user_all_info();
    if (defined('TAOH_PAGE_AVATAR') && TAOH_PAGE_AVATAR) {
        return '<div class="avatar"><img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/' . TAOH_PAGE_AVATAR . '.png" alt=""></div>';
    } else if (isset($logged_user->avatar)) {
        return '<div class="avatar"><img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $logged_user->avatar . '.png" alt=""></div>';
    } else {
        return '<div class="avatar"><img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png" alt=""></div>';
    }
}

function taoh_user_record_history()
{
    if (taoh_parse_url(0) != 'login' && taoh_parse_url(0) != 'settings') {
        $_SESSION["history"] = TAOH_SITE_URL . $_SERVER['REQUEST_URI'];
        return "<script>localStorage.setItem('history', window.location.href)</script>";
    }
}

/*function taoh_get_api_token_old($default = 0)
{
    $return = 0;
    if (defined('TAOH_API_TOKEN')) {
        //echo"<br>=======1========>>".TAOH_API_TOKEN."<<----------<br>";
        $return = TAOH_API_TOKEN;
    } else {
        if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token'])) {
            $return = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token'];
            //echo"<br>=====2==========>>".$return."<<----------<br>";
        } else if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token'])) {
            $return = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token'];
            //echo"<br>=====2==========>>".$return."<<----------<br>";
        }
        if (isset($_GET['temp_api_token']) && $_GET['temp_api_token']) {
            setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $_GET['temp_api_token'], strtotime('+2 days'), '/');
            return $_GET['temp_api_token'];
        }
    }

    //echo"<br>=====3==========>>".$return."<<----------<br>";
    return $return;
}*/

function taoh_get_api_token($force = 0, $return_temp = 0)
{
    if (empty($return_temp)) {
        // Return predefined constant if set
        if (defined('TAOH_API_TOKEN')) {
            return TAOH_API_TOKEN;
        }

        $cookie_prefix = TAOH_ROOT_PATH_HASH . '_';
         $token = $_COOKIE[$cookie_prefix . 'taoh_api_token']
                        ?? $_COOKIE[$cookie_prefix . 'temp_api_token']
                        ?? null;
        // If temp token exists but not permanent token, set permanent token
        if (!isset($_COOKIE[$cookie_prefix . 'taoh_api_token']) && isset($_COOKIE[$cookie_prefix . 'temp_api_token'])) {
            $temp_token = $_COOKIE[$cookie_prefix . 'temp_api_token'];
            setcookie($cookie_prefix . 'taoh_api_token', $temp_token, strtotime('+30 days'), '/');
            $token = $temp_token;
        }
        // Check for a temporary API token in the query string
        if (!empty($_GET['temp_api_token'])) {
            $token = $_GET['temp_api_token'];
            setcookie($cookie_prefix . 'taoh_api_token', $token, strtotime('+2 days'), '/');
        }
    }

    // Return a dummy token if forced and no valid token is found
    if ($force && empty($token) && defined('TAOH_API_TOKEN_DUMMY')) {
        return TAOH_API_TOKEN_DUMMY;
    }

    return $token ?? 0;
}

function taoh_get_api_secret($force = 0)
{
    // Return predefined constant if set
    if (defined('TAOH_API_SECRET')) {
        return TAOH_API_SECRET;
    }

    // Return a dummy token if forced and no valid secret is found
    if ($force && defined('TAOH_API_DUMMY_SECRET')) {
        return TAOH_API_DUMMY_SECRET;
    }

    return null;
}

function taoh_set_error_message($message, $time = 5000,$delay=0)
{
    $time_delay_to_show = 0;
    if($delay==1){
        $time_delay_to_show = 5000;}
    ?>
    <script type="text/javascript">
         setTimeout(function () {
            $('#toast').show();
            $("#toast").addClass("toast_active");
            $("#toast_error").html('');


           $("#toast_error").html(`<div>
                    <div class="dojo toast-header toasterror_class shadow-none pl-3">

                        <h5 class="heading" id="">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="16" cy="16" r="16" fill="#D13D13"/>
                                <path d="M23.5265 10.7325C24.1512 10.1078 24.1512 9.09324 23.5265 8.46853C22.9018 7.84382 21.8872 7.84382 21.2625 8.46853L16 13.7361L10.7325 8.47353C10.1078 7.84882 9.09324 7.84882 8.46853 8.47353C7.84382 9.09824 7.84382 10.1128 8.46853 10.7375L13.7361 16L8.47353 21.2675C7.84882 21.8922 7.84882 22.9068 8.47353 23.5315C9.09824 24.1562 10.1128 24.1562 10.7375 23.5315L16 18.2639L21.2675 23.5265C21.8922 24.1512 22.9068 24.1512 23.5315 23.5265C24.1562 22.9018 24.1562 21.8872 23.5315 21.2625L18.2639 16L23.5265 10.7325Z" fill="white"/>
                            </svg>
                            Dojo Says !
                        </h5>

                        <!-- dojo svg -->
                        <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                            <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                            <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_2)"/>
                            <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_2)"/>
                            <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                            <defs>
                            <linearGradient id="paint0_linear_7568_2" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FDCC6E"/>
                            <stop offset="1" stop-color="#FF6600"/>
                            </linearGradient>
                            <linearGradient id="paint1_linear_7568_2" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                            <stop offset="0.1" stop-color="#FD1D1D"/>
                            <stop offset="0.9" stop-color="#FF6600"/>
                            </linearGradient>
                            </defs>
                        </svg>


                        <button type="button" class='btn toast_dismiss toast-v2-dismiss shadow-none' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</button>

                    </div>
                    <div class="toast-body px-3 pt-3 pb-2">
                        <p class="sm-text mb-2"><?php echo $message;?></p>
                    </div>
                </div>`);
                /*
                <div class="mt-2 pt-2 border-top">
                            <button type="button" class="btn btn-primary btn-sm">Take action</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Close</button>
                        </div>
                <a href="#" class="btn dojo-v1-btn">
                         Find People !
                        </a>
                */
            setTimeout(function () {

             $("#toast").removeClass("toast_active");
             $("#toast_container").removeClass("toast-middle-con");

            }, <?php echo $time; ?>);
        }, <?php echo $time_delay_to_show; ?>);
    </script>
    <?php
}

function taoh_set_success_message($message, $time = 5000)
{
    ?>
    <script type="text/javascript">
        $('#toast').show();
        $("#toast").addClass("toast_active");
        $("#toast_error").html('');

     $("#toast_error").html(`<div>


                    <div class="dojo toast-header success_class shadow-none pl-3">

                        <h5 class="heading" id="">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32ZM23.0625 13.0625L15.0625 21.0625C14.475 21.65 13.525 21.65 12.9438 21.0625L8.94375 17.0625C8.35625 16.475 8.35625 15.525 8.94375 14.9438C9.53125 14.3625 10.4812 14.3563 11.0625 14.9438L14 17.8813L20.9375 10.9375C21.525 10.35 22.475 10.35 23.0562 10.9375C23.6375 11.525 23.6437 12.475 23.0562 13.0562L23.0625 13.0625Z" fill="#0F9D58"/>
                            </svg>
                            Dojo Says !
                        </h5>

                        <!-- dojo svg -->
                        <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                            <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                            <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_2)"/>
                            <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_2)"/>
                            <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                            <defs>
                            <linearGradient id="paint0_linear_7568_2" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FDCC6E"/>
                            <stop offset="1" stop-color="#FF6600"/>
                            </linearGradient>
                            <linearGradient id="paint1_linear_7568_2" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                            <stop offset="0.1" stop-color="#FD1D1D"/>
                            <stop offset="0.9" stop-color="#FF6600"/>
                            </linearGradient>
                            </defs>
                        </svg>


                        <button type="button" class='btn toast_dismiss toast-v2-dismiss shadow-none' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</button>

                    </div>
                    <div class="toast-body px-3 pt-3 pb-2">
                        <p class="sm-text mb-2"><?php echo $message;?></p>

                    </div>
                </div>`);

        setTimeout(function () {
            $("#toast").removeClass("toast_active");
            $("#toast_container").removeClass("toast-middle-con");
        }, <?php echo $time; ?>);
    </script>
    <?php
}

function taoh_set_warning_message($message, $time = 5000)
{ ?>
    <script type="text/javascript">
        $('#toast').show();
        $("#toast").addClass("toast_active");
        $("#toast_error").html('');
        $("#toast_error").html(`<div class='info_class'><span><i class='las la-check-circle mr-2 info_icon'></i>
      <?php echo $message;?> &nbsp;
      <span class='toast_dismiss' aria-hidden='true'  data-dismiss='toast' aria-label='Close'>&times;</span></span></div>`)
        setTimeout(function () {
            $("#toast").removeClass("toast_active");
            $("#toast_container").removeClass("toast-middle-con");
        }, <?php echo $time; ?>);
    </script>
    <?php
}

function taoh_redirect($url = TAOH_SITE_URL_ROOT)
{
    //echo headers_sent()." - ".$url;exit();
    if (headers_sent()) {
        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
        echo '</noscript>';
    } else {
        header("Location: " . $url);
    }
    taoh_exit();
}

function taoh_timestamp($timezone = TAOH_DEFAULT_TIMEZONE)
{
    date_default_timezone_set($timezone);
    return date(TAOH_TIMEZONE_FORMAT);
}

function taoh_timestamp_from_now($from_now = 'now', $zone = TAOH_DEFAULT_TIMEZONE)
{
    date_default_timezone_set($zone);
    return date(TAOH_TIMEZONE_FORMAT, strtotime($from_now));
}

function taoh_get_nearest_timezone($cur_lat, $cur_long, $country_code = '')
{
    $return = TAOH_DEFAULT_TIMEZONE;
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
        : DateTimeZone::listIdentifiers();

    if ($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach ($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat = $location['latitude'];
                $tz_long = $location['longitude'];

                $theta = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                    + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance;

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone = $timezone_id;
                    $tz_distance = $distance;
                }

            }
        }
        $return = $time_zone;
    }
    return $return;
}

function taoh_get_header($hook = '')
{
    include_once('core/themes/header.php');
}

function taoh_get_header_for_events_landing($hook = '')
{
    include_once('core/themes/header_events_landing.php');
}

function taoh_get_header_mobile($hook = '')
{
    include_once('core/themes/header_mobile.php');
}

function taoh_get_header_iframe($hook = '')
{
    include_once('core/themes/header-iframe.php');
}

function taoh_get_header_new()
{
    include_once('core/themes/header_new.php');
}

function taoh_get_footer()
{
    global $footer_tracking_link;
    include_once('core/themes/footer.php');
}

function taoh_get_footer_events_landing()
{
    include_once('core/themes/footer_events_landing.php');
}

function taoh_site_ajax_url(int $site = 1)
{
    $ajax_url = match ($site) {
        2 => TAOH_DASH_AJAX_URL,
        default => TAOH_AJAX_URL,
    };

    if (taoh_is_wp() == 1) {
        return TAOH_SITE_URL . '/wp-admin/admin-ajax.php';
    }
    return $ajax_url;
}

function taoh_site_message_ajax_url()
{
    if (taoh_is_wp() == 1) {
        return TAOH_SITE_URL . '/wp-admin/admin-ajax.php';
    }
    return TAOH_SITE_URL_ROOT . '/message/ajax';
}

function taoh_site_url()
{
    return '';
}

function taoh_site_logo_url()
{
    //Take logo from config file
    if (defined('TAOH_LOGO_URL') && TAOH_LOGO_URL != '') {
        return TAOH_LOGO_URL;
    }
    //Take logo from wp if function exist

}

function taoh_site_title()
{
    //Take logo from config file
    if (TAOH_SITE_TITLE && TAOH_SITE_TITLE != '') {
        return TAOH_SITE_TITLE;
    }
    //Take title from wp if function exist

}

function taoh_site_description()
{
    //Take logo from config file
    if (TAOH_SITE_DESCRIPTION && TAOH_SITE_DESCRIPTION != '') {
        return TAOH_SITE_DESCRIPTION;
    }
    //Take description from wp if function exist

}

function taoh_app_info($app = TAOH_WERTUAL_SLUG)
{

    $_SESSION = array();
    if($app == 'asks') $app_data  = TAOH_ASKS_APP_DETAILS;
    else if($app == 'jobs') $app_data  = TAOH_JOBS_APP_DETAILS;
    else if($app == 'events') $app_data  = TAOH_EVENTS_APP_DETAILS;
    else if($app == 'club') $app_data  = TAOH_CLUB_APP_DETAILS;
    else if($app == 'hires') $app_data  = TAOH_HIRES_APP_DETAILS;
    else $app_data  = TAOH_HIRES_APP_DETAILS;


    if (taoh_session_get('app_info_' . $app) == '' || taoh_session_get('app_info_' . $app) == null) {
        //taoh_session_save('app_info_' . $app, json_decode(taoh_url_get_content(TAOH_CDN_PREFIX . "/app/" . $app . "/config")));

        $data  = (object)$app_data;
        taoh_session_save('app_info_' . $app,$data);
    }
    return taoh_session_get('app_info_' . $app);
}

function taoh_logout1()
{
    setcookie('tao_login_try', 1, strtotime('+1 days'), '/');
    setcookie('tao_login_stamp', time(), strtotime('-1 days'), '/');


    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'locked', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'enable_lock_screen', 1, strtotime('-2 days'), '/');


    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token', '', strtotime('-1 days'), '/');
        unset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']);
    }
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url']) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url', '', strtotime('-1 days'), '/');
        unset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url']);
    }
    if (isset($_COOKIE['tao_api_email']) && isset($_COOKIE['tao_api_email'])) {
        setcookie('tao_api_email', '', strtotime('-1 days'), '/');
        unset($_COOKIE['tao_api_email']);
    }
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email']) && isset($_COOKIE['tao_api_email'])) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email', '', strtotime('-1 days'), '/');
        unset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email']);
    }
    unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
    unset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']);
    //echo "======aaaaaaaa========".TAOH_ROOT_PATH_HASH.'_'.'taoh_api_token';die('-------------');
}

function taoh_url_vars()
{
    $folder_arr = (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['REDIRECT_URL'];
    return explode('/', $folder_arr);
}

function taoh_url_502($url)
{
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200') !== false) {
        return 0; // URL is accessible
    } else {
        return 1; // URL is not accessible
    }
}

function taoh_health_check()
{
    //die('--------------');
    $return = 0;
    return $return;
    $return = $return + taoh_url_502(TAOH_CDN_PREFIX . '/health.html');
    $return = $return + taoh_url_502(TAOH_API_PREFIX . '/health.html');
    $return = $return + taoh_url_502(TAOH_OPS_PREFIX . '/health.html');
    $return = $return + taoh_url_502(TAOH_DASH_PREFIX . '/health.html');
    $return = $return + taoh_url_502(TAOH_CACHE_PREFIX . '/health.html');
    $return = $return + taoh_url_502(TAOH_CACHE_CHAT_PREFIX . '/health.html');
    //echo"=============".$return;
    return $return;
}

function taoh_health_sync() {
    $health_file_path = __DIR__ . '/cache/logs/health.cache';

    if (stristr($_SERVER['REQUEST_URI'], 'ajax')) {
        $taoh_maintenance = 0;
        return;
    }

    // If the health file doesn't exist or it is outdated (older than 60 seconds)
    if (!file_exists($health_file_path) || filemtime($health_file_path) < time() - 60) {
        if (!taoh_health_check()) {
            if (!file_put_contents($health_file_path, '1')) {
                require_once('maintenance.php');
                taoh_exit();
            }
        } else {
            require_once('maintenance.php');
            taoh_exit();
        }
    }

    $taoh_maintenance = 0;
}

function taoh_bot_checker()
{
    $botlist = array("a6-indexer", "adsbot-google", "ahrefsbot", "aolbuild", "apis-google", "baidu", "bingbot", "bingpreview", "butterfly", "cloudflare", "duckduckgo", "embedly", "facebookexternalhit", "facebot", "googlebot", "ia_archiver", "linkedinbot", "mediapartners-google", "msnbot", "netcraftsurvey", "outbrain", "pinterest", "quora", "rogerbot", "showyoubot", "slackbot", "slurp", "sogou", "teoma", "tweetmemebot", "twitterbot", "uptimerobot", "urlresolver", "vkshare", "w3c_validator", "wordpress", "wp rocket", "yandex");
    foreach ($botlist as $botelem) {
        if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), $botelem)) {
            return 1;
        }
    }
    return 0;
}

function taoh_get_categories($type = 'all', $count = 0)
{
    if ($type == 'flash') {
        //$api = TAOH_INFOFETCH_GET.'?ops=flashcat&token='.taoh_get_dummy_token();
        //$response = json_decode( taoh_url_get_content ( $api ), true );
        $taoh_vals = array(
            'ops' => 'flashcat',
            'token' => taoh_get_dummy_token(1),
        );
        $taoh_call = "infofetch.get";
        //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
        $taoh_call_type = "get";
//    $response_arr = taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals );
        //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals ); exit();
        $response_arr = taoh_apicall_get($taoh_call, $taoh_vals);

        $response = json_decode($response_arr, true);
        $response = $response['output']['category'];
    } else {
        $api = TAOH_CONTENT_CATEGORY;
        $response = json_decode(taoh_url_get_content($api), true);
    }
    if (isset($response) && is_array($response)) {
        if ($type == 'shuffle') {
            shuffle($response);
        }
        if ($count) {
            $response = array_slice($response, 0, $count);
        }
        return $response;
    }
    return array();
}

function taoh_category_bucket($categories, $bucket)
{
    $return = array();
    foreach ($categories as $category) {
        if ($category['bucket'] == $bucket) {
            array_push($return, $category);
        }
    }
    return $return;
}

function taoh_category_info($taoh_cat_slug, $taoh_type = 'all')
{
    $categories = taoh_get_categories($taoh_type);
    foreach ($categories as $category) {
        if ($category['slug'] == $taoh_cat_slug) {
            return $category;
        }
    }
    return $categories[0];
}

function taoh_clean_cookie()
{
    if (isset($_COOKIE)) {
        foreach ($_COOKIE as $key => $value) {
            if (stristr($key, 'tao')) {
                setcookie(TAOH_ROOT_PATH_HASH . '_' . $key, '', strtotime('-1 days'), '/');
                unset($_COOKIE[$key]);
            }
        }
    }
    return 1;
}

function taoh_get_category_color($cat)
{
    $cat_arr = array(
        "interview" => '#3E54AC',
        "job-search" => '#4E6E81',
        "networking" => '#3795BD',
        "resume" => '#AD7BE9',
        "jobs-of-future" => '#C92C6D',
        "branding" => '#609EA2',
        "mentor-coach" => '#865DFF',
        "conflict-management" => '#537FE7',
        "growth-mindset" => '#183A1D',
        "handling-change" => '#F0A04B',
        "leadership" => '#5D9C59',
        "career-development" => '#2B3467',
        "learning" => '#3A98B9',
        "mindfulness" => '#E96479',
        "future-of-work" => '#4D455D',
        "organization" => '#060047',
        "general-work-strategy" => '#443C68',
        "productivity" => '#40513B',
        "upskilling" => '#8D7B68',
    );
    $return = '#000000';
    if (isset($cat_arr[$cat])) {
        $return = $cat_arr[$cat];
    }
    if ($cat == 'general') {
        shuffle($cat_arr);
        $return = $cat_arr[0];
    }
    return $return;
}

function taoh_string_split($string, $length = 1)
{
    $return = array();
    if ($length < 1) return false;
    $pieces = ceil(strlen($string) / $length);
    //echo $pieces;taoh_exit();
    $words = explode(" ", $string);
    $i = 0;
    $return [$i] = '';
    foreach ($words as $word) {
        if (strlen($return[$i] . $word) < $length) {
            $return[$i] .= $word . ' ';
        } else {
            $i++;
            $return[$i] .= $word . ' ';
        }
    }
    return $return;
}

function taoh_cache_filemtime_check($file_name, $servermtime = 0, $cache_time = 0)
{
    $file_name = TAOH_PLUGIN_PATH . "/cache/general/$file_name.cache";
    if (file_exists($file_name)) {
        $filemtime = filemtime($file_name);
        if ($cache_time) {
            if ($filemtime <= (time() - $cache_time)) {
                unlink($file_name);
                $servermtime = $filemtime + 1;
            }
        }
        if ($filemtime < $servermtime) {
            //return $filemtime;
            return 1;
        }
    }
    return 0;
}


function taoh_beam()
{
    // https://preapi.tao.ai/cache/beam/?name=thisisfilename&content=https%3A%2F%2Fjobsoffice.org%2Fhires&ops=keepalive
    $file_name = TAOH_PLUGIN_PATH . "/cache/general/" . TAOH_API_SECRET . "_keepalive.cache";
    $ping = 1;
    if (file_exists($file_name)) {
        $filemtime = filemtime($file_name);
        $ping = 0;
        if ((time() - $filemtime) >= 300) {
            unlink($file_name);
            $ping = 1;
        }
    }
    if ($ping) {
        $url = TAOH_API_PREFIX . '/cache/beam?name=' . TAOH_API_SECRET . '&content=' . urlencode(TAOH_SITE_URL_ROOT) . '&ops=keepalive';
        file_put_contents($file_name, '');
        taoh_url_get_content($url);
    }
    return 1;
}

function taoh_polling_beam($file_name)
{
    $return = false;
    if (defined('TAOH_SITE_POLL') && TAOH_SITE_POLL) {
        // e.g. https://preapi.tao.ai/cache/beam/?name=hT93oaWC_c887d69f_jobs_get&mtime=0&ops=pollcheck
        $file_path = TAOH_PLUGIN_PATH . "/cache/general/$file_name.cache";
        $return = 0;
        if (file_exists($file_path)) {
            $filemtime = filemtime($file_name);
            // echo 'local timestamp -'.$filemtime;
            $url = TAOH_API_PREFIX . "/cache/beam?name=$file_name&mtime=$filemtime";
            //echo $url;
            $state = json_decode(taoh_url_get_content($url), true);
            $return = $state['output'];
        }
    }
    return $return;
}

function taoh_rand_set($set, $min, $max)
{
    if (!isset($set)) $set = 1;
    $counter = 1;
    $return = array();
    $test = '';
    while ($counter <= $set) {
        $number = rand($min, $max);
        while (stristr($test, ':' . $number . ':')) {
            $number = rand($min, $max);
        }
        $test = $test . ':' . $number . ':';
        $return[] = $number;
        $counter++;
    }
    return $return;
}

function taoh_filename_generate($taoh_call, $taoh_vals)
{
    $temp_arr = $taoh_vals;
    if (isset($taoh_vals['cacheo_name'])) {
        $tocode = $taoh_vals['cacheo_name'];
    } else {
        if (isset($taoh_vals['cache_remove'])) {
            unset($temp_arr['cache_remove']);
        }
        if (isset($taoh_vals['cacheo'])) unset($temp_arr['cacheo']);
        if (isset($taoh_vals['token'])) unset($temp_arr['token']);
        $tocode = hash('crc32', http_build_query($temp_arr));
    }
    //return $tocode;
    $api_func = str_replace('.', '_', strtolower($taoh_call));
    return $taoh_vals['token'] . "_" . $tocode . "_" . $api_func;
}

function taoh_p2us($string)
{
    return str_replace('.', '_', $string);
}

function taoh_cache_stamp_check($file_name, $lastimportchatmtime)
{
    if (file_exists($file_name)) {
        $filemtime = '';
        if ($lastimportchatmtime < filemtime($file_name)) {
            return true;
        }
    }
    return false;
}

// Function to convert array to a csv string
function taoh_arr2csv($arr)
{
    $csv = '';
    foreach ($arr as $row) {
        $csv .= implode(',', $row) . PHP_EOL;
    }
    return $csv;
}

function taoh_room_stamp_check($file_name, $lastimportchatmtime)
{
    $file_name = TAOH_PLUGIN_PATH . "/cache/chat/" . $file_name;
    if (file_exists($file_name)) {
        $filemtime = 0;
        if ($lastimportchatmtime < filemtime($file_name)) {
            return true;
        }
    }
    return false;
}

function taoh_readable_stamp($tstamp)
{
    $now = time();
    $stamp_text = "";
    $diff = $now - $tstamp;
    $stamp_add = "";
    if ($now <= $tstamp) {
        $stamp_add = "";
        $diff = $tstamp - $now;
    }
    $year = 365 * 24 * 60 * 60;
    $month = 30 * 24 * 60 * 60;
    $week = 7 * 24 * 60 * 60;
    $day = 24 * 60 * 60;
    $hour = 60 * 60;
    $min = 60;
    if (floor($diff / $year)) {
        $stamp_text = floor($diff / $year) . " year(s)";
    } else if (floor($diff / $month)) {
        $stamp_text = floor($diff / $month) . " month(s)";
    } else if (floor($diff / $week)) {
        $stamp_text = floor($diff / $week) . " week(s)";
    } else if (floor($diff / $day)) {
        $stamp_text = floor($diff / $day) . " day(s)";
    } else if (floor($diff / $hour)) {
        $stamp_text = floor($diff / $hour) . " hour(s)";
    } else if (floor($diff / $min)) {
        $stamp_text = floor($diff / $min) . " min(s)";
    } else {
        $stamp_text = "Just now";
    }
    return $stamp_text . (($stamp_add) ? " " . $stamp_add : '');
}

function taoh_readable_stamp_short($tstamp)
{
    $now = time();
    $stamp_text = "";
    $diff = $now - $tstamp;
    $stamp_add = "";
    if ($now <= $tstamp) {

        $diff = $tstamp - $now;
    }
    $year = 365 * 24 * 60 * 60;
    $month = 30 * 24 * 60 * 60;
    $week = 7 * 24 * 60 * 60;
    $day = 24 * 60 * 60;
    $hour = 60 * 60;
    $min = 60;
    if (floor($diff / $year)) {
        $stamp_text = floor($diff / $year) . " yr(s)";
    } else if (floor($diff / $month)) {
        $stamp_text = floor($diff / $month) . " month(s)";
    } else if (floor($diff / $week)) {
        $stamp_text = floor($diff / $week) . " week(s)";
    } else if (floor($diff / $day)) {
        $stamp_text = floor($diff / $day) . " day(s)";
    } else if (floor($diff / $hour)) {
        $stamp_text = floor($diff / $hour) . " hrs(s)";
    } else if (floor($diff / $min)) {
        $stamp_text = floor($diff / $min) . " min(s)";
    } else {
        $stamp_text = "Just now";
    }
    return $stamp_text . (($stamp_add) ? " " . $stamp_add : '');
}


function taoh_read_json_files($folder, $extension = 'json')
{
    $return = array();
    $files = glob($folder . "/*." . $extension);
    foreach ($files as $file) {
        $return[] = json_decode(file_get_contents($file), true);
    }
    return $return;
}


function taoh_reads_json_files($folder, $extension = 'json')
{
    $return = array();
    $files = glob($folder . "/*." . $extension);
    foreach ($files as $file) {
        $return[] = file_get_contents($file);
    }
    return $return;
}

function taoh_clean_old_file_cron($directory, $time_threshold)
{
    // for nonphp files
    $now = time();
    $output = 0;
    $file = $directory . '/clean.cache';
    if (file_exists($file)) {
        if (filemtime($file) < $now - $time_threshold) {
            unlink($file);
            $output = taoh_remove_old_files($directory, $time_threshold);
        }
    } else {
        touch($file);
    }
    return $output;
}


function taoh_remove_old_files($directory, $time_threshold, $ignore_folder = '')
{
    $counter = 0;
    if (stristr($directory, TAOH_PLUGIN_PATH . "/cache") || stristr($directory, TAOH_SITE_URL_ROOT . "/cache")) {
        $files = glob($directory . "/*");
        $now = time();
        foreach ($files as $file) {
            if (is_dir($file)) {
                if ($ignore_folder && stristr($file, $ignore_folder)) {
                    continue;
                } else {
                    $counter = $counter + taoh_remove_old_files($file, $time_threshold, $ignore_folder); // Recursive call for subdirectories
                }
            } else {
                // Check if the file is not a PHP file and is older than the time threshold
                if (pathinfo($file, PATHINFO_EXTENSION) == 'cache' && filemtime($file) < $now - $time_threshold) {
                    unlink($file); // Delete the file
                    $counter++;
                    //echo ‘Deleted file: ’ . $file . PHP_EOL;
                }
            }
        }
    }
    return $counter;
}

function taoh_fwrite($data, $filename)
{
    $return = false;
    if (is_writable($filename)) {

        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence
        // that's where $data will go when we fwrite() it.
        if (!$fp = fopen($filename, 'a')) {
            echo "Cannot open file ($filename)";
            exit;
        }

        // Write $data to our opened file.
        if (fwrite($fp, $data) === FALSE) {
            echo "Cannot write to file ($filename)";
            exit;
        }
        fclose($fp);
        $return = true;
    }
    return $return;
}

function taoh_fread($filename)
{
    $return = '';
    if (file_exists($filename)) {
        $return = file_get_contents($filename);
    }
    return $return;
}


function taoh_slugify($string)
{
    return strtolower(custom_trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

function taoh_either_or($var1, $var2)
{
    return (isset($var1) && $var1) ? $var1 : $var2;
}

function create_dm_room($keyslug, $ptoken)
{
    $geo_enable = 0;

    $title = 'DM';
    $title_desc = 'Direct Message';

    $title_desc = '<span class="super_title">' . $title . '</span><br>';

    $room_info_arr = array(
        'keyslug' => $keyslug,
        'app' => 'message',
        'club' => array(
            'title' => $title,
            'description' => $title_desc,
            'short' => $title_desc,
            'image' => '',
            'square_image' => '',
            'links' => array(
                'club' => '/message/dm/' . $keyslug,
            ),
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '/',
                ),
                array(
                    'title' => 'Profile',
                    'link' => '/profile',
                ),
            ),
        ),
    );

    create_room_info($room_info_arr, $ptoken);
    return $room_info_arr;
}

function create_room_info($room_data, $ptoken = '', $data = array())
{
    if (empty($ptoken)) $ptoken = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'detail';

    $response = array('output' => false, 'success' => false);

    $taoh_vals = array(
        'ops' => 'status',
        'status' => 'postroom',
        'code' => TAOH_OPS_CODE,
        'key' => $ptoken,
        'keyslug' => $room_data['keyslug'],
        'type' => $type,
        'value' => addslashes(json_encode($room_data)),
        //'debug' => 1
    );

    if (isset($room_data['app']) && !empty($room_data['app'])) {
        $taoh_vals['app'] = $room_data['app'];
    }

    $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $room_info_arr = json_decode($room_data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'invalid_json_response';
        $response['message'] = 'Invalid JSON response: ' . json_last_error_msg();
        return $response;
    }

    if ($room_info_arr['success'] && $room_info_arr['output']) {
        $response['output'] = $room_info_arr['output'];
        $response['success'] = $room_info_arr['success'];
    }

    $cache_dir = TAOH_PLUGIN_PATH . '/cache/general/rooms/';
    $cache_file_name = 'room_info_' . $taoh_vals['keyslug'] . '_' . $taoh_vals['type'] . (isset($taoh_vals['app']) ? '_' . $taoh_vals['app'] : '') . '.cache';
    remove_cache_file($cache_dir, [$cache_file_name]);

    return $response;
}

function get_room_info($keyslug, $ptoken = '', $data = array())
{
    if (empty($ptoken)) $ptoken = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'detail';

    $response = array('output' => false, 'success' => false);

    $taoh_vals = array(
        'ops' => 'status',
        'status' => 'getroom',
        'code' => TAOH_OPS_CODE,
        'key' => $ptoken,
        'keyslug' => $keyslug,
        'type' => $type,
        //'debug'=>1
    );

    if (isset($data['app']) && !empty($data['app'])) {
        $taoh_vals['app'] = $data['app'];
    }

    $cache_dir = TAOH_PLUGIN_PATH . '/cache/general/rooms/';
    $cache_file = $cache_dir . 'room_info_' . $taoh_vals['keyslug'] . '_' . $taoh_vals['type'] . (isset($taoh_vals['app']) ? '_' . $taoh_vals['app'] : '') . '.cache';

    $is_cache_response = false;
    if (is_cache_valid($cache_file, 120 * 60)) { // 2 hours
        $room_data_json = read_cache_file($cache_file);
        $is_cache_response = true;
    } else {
        $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    }
    if ($is_cache_response === false) {
        // To update filetime on each call comment is_cache_response condition
        $room_info_arr = json_decode($room_data_json, true);
        if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
            update_cache_file($cache_file, $room_data_json);
        }
    }

    /*$room_info_arr = json_decode($room_data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'invalid_json_response';
        $response['message'] = 'Invalid JSON response: ' . json_last_error_msg();
        return $response;
    }

    if($room_info_arr['success'] && $room_info_arr['output']){
        $response['output'] = $room_info_arr['output'];
        $response['success'] = $room_info_arr['success'];
    }

    return $response;*/

    return $room_data_json;
}

function taoh_networking_postcell($club_info, $user = false)
{
    if (!$user) $user = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $taoh_vals = array(
        'key' => $user,
        'keyslug' => $club_info['keyslug'],
        'code' => TAOH_OPS_CODE,
        'cell' => json_encode($club_info),
        'ops' => 'status',
        'status' => 'postcell',
        //'ttl' => 18 * 60 * 60,
    );
//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);die();
    return taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
}

function taoh_networking_getcell($keyslug, $key = false)
{
    if (!$key) $key = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $taoh_vals = array(
        'key' => $key,
        'keyslug' => $keyslug,
        'code' => TAOH_OPS_CODE,
        'ops' => 'status',
        'status' => 'getcell',
        //'ttl' => 18 * 60 * 60,
    );
//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);die();
    return taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
}

function updateCellInfo($keyslug, $ptoken)
{
    $value = taoh_networking_getcell($keyslug, $ptoken);
    $value_arr = json_decode($value, true);
    if (isset($value_arr['output']) && $value_arr['output']) {
        $cell_info = $value_arr['output']['info'];
    } else {
        $userInfo_json = taoh_get_user_info($ptoken, 'info');
        $userInfo_array = json_decode($userInfo_json, true);

        $cell_info = [
            'keyslug' => $keyslug,
            'user' => [
                'ptoken' => $userInfo_array['output']['user']['full']['ptoken'],
                'chat_name' => $userInfo_array['output']['user']['full']['chat_name'],
                'avatar' => $userInfo_array['output']['user']['full']['avatar'],
                'full_location' => $userInfo_array['output']['user']['full']['full_location'],
                'coordinates' => $userInfo_array['output']['user']['full']['coordinates'],
                'geohash' => $userInfo_array['output']['user']['full']['geohash'],
                'local_timezone' => $userInfo_array['output']['user']['full']['local_timezone'],
                'profile_type' => $userInfo_array['output']['user']['full']['type'],
                'site' => [
                    'source' => '/',
                    'name' => TAOH_SITE_NAME_SLUG
                ]
            ]
        ];
        $coordinates = $cell_info['user']['coordinates'];
        if (!empty($coordinates)) {
            $co_array = explode('::', $coordinates);

            $cell_info['user']['latitude'] = $co_array[0];
            $cell_info['user']['longitude'] = $co_array[1];
        }

        taoh_networking_postcell($cell_info, $ptoken);
    }

    return $cell_info;
}

function taoh_extract_youtubeid($url)
{
    // This pattern is designed to match various YouTube URL formats
    $pattern =
        '%^# Match any youtube URL
      (?:https?://)?  # Optional scheme. Either http or https
      (?:www\.)?      # Optional www subdomain
      (?:             # Group host alternatives
        youtu\.be/    # Either youtu.be,
      | youtube\.com  # or youtube.com
        (?:           # Group path alternatives
          /embed/     # Either /embed/
        | /v/         # or /v/
        | /watch\?v=  # or /watch\?v=
        | /watch\?.+&v=  # or /watch\?other_param&v=
        )            # End path alternatives.
      )               # End host alternatives.
      ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
      ($|&|%|/|\#)    # Match end of URL or ampersand or hash.
      %x';

    $result = preg_match($pattern, $url, $matches);
    if (false !== $result && !empty($matches[1])) {
        return $matches[1];
    }

    return false;
}

function taoh_get_youtubeId($url)
{
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    if (isset($match[1]) && $match[1]) {
        return $match[1];
    }
    return false;
}

function wemetJoinApi($data)
{
    $url = 'https://wemet.tao.ai/api/join';

    //print_r($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = [
        'room' => $data['room'],
        'agentName' => $data['agentName'],
        'visitorName' => $data['visitorName'],
    ];
    $data_json = json_encode($data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);


    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . TAOH_WEMET_AUTHORIZATION_TOKEN,
        'accept: application/json',
        'Content-Length: ' . strlen($data_json)
    ]);


    $result = curl_exec($ch);
    curl_close($ch);
    //check the result
    return $result;
}

function checkReferral()
{


    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != ''
        && isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] != '') {

        $url = TAOH_SITE_URL_ROOT . '/refer/?already_refered=1';
        taoh_redirect($url);
        exit();
    }

}

function call_login_referral($link = TAOH_SITE_URL_ROOT, $place = 0)
{
    if (TAOH_REFER_ENABLE) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url', $link, strtotime('+1 days'), '/');
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'place', $place, strtotime('+1 days'), '/');
    }

}

function call_login_referral_action($tokenn = '')
{

    $email = $_COOKIE[TAOH_ROOT_PATH_HASH . '_tao_api_email'] ?? '';

    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token'])) {
        $tokenn = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token'];
    } else if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'])) {
        $tokenn = $_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'];
    }
    if (!isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']) && isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'])) {
        setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'], strtotime('+1 days'), '/');
    }

    if (!TAOH_REFER_ENABLE && isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'])) {
        setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'], strtotime('+1 days'), '/');
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] != '')
        $link = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'];
    else
        $link = TAOH_SITE_URL_ROOT;

    $toenter = array();
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != '') {
        $taoh_call = "core.refer.update";
    } else
        $taoh_call = "core.refer.put";
    $toenter['refer'] = json_encode(array());

    $current_app = taoh_parse_url(0);
    //echo "=============" . $current_app ;
    $actions_var = array(
        'action_url' => '',
        'action_page_blurb' => '',
        'action_email_vars' => array(
            'subject' => '',
            'supertitle' => '',
            'title' => '',
            'subtitle' => '',
        ),
        'extra_info' => array(
            'title' => '',
            'app_name' => '',
            'action' => 'visit',
            'link' => $link,
            'action_link' => $link,
            'site_name' => TAOH_SITE_NAME_SLUG,
        ),

    );

    $toenter['refer_data'] = json_encode(array(
            'requested_by_ptoken' => taoh_get_dummy_token(),
            'from_link' => $link,
            'to_link' => $link,
            //'to_link' => TAOH_SITE_URL_ROOT.'/settings',
            'for_email' => $email,
            'action_flag' => 0,
            'actions_var' => $actions_var,
            'referral_type' => 'login',
        )
    );
    $taoh_vals = array(
        'ops' => 'invite',
        'token' => $tokenn,
    );
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != '') {

        $taoh_vals['refer_token'] = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'];
        $taoh_vals['refer_key'] = 'login';
        $taoh_vals['for_email'] = $email;
    } else {
        $taoh_vals['toenter'] = $toenter;
    }


   // echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die();
    //$result = taoh_post( TAOH_CACHE_CHAT_PROC_URL, $taoh_vals );


    $result = json_decode(taoh_apicall_post($taoh_call, $taoh_vals));
    //echo "===========".$_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'];


    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != '') {
        $refer_token = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'];
    }
    //echo'<pre>';print_r($result);echo'</pre>';die();
    if ($result->success == 1) {
        if (isset($result->refer_token[0]) && $result->refer_token[0] != '') {
            $refer_token = $result->refer_token[0];
            setcookie(TAOH_ROOT_PATH_HASH . '_' . 'refer_token', $refer_token, strtotime('+1 days'), '/');
        }


    }
    //echo "======tokenn======".$tokenn;
    //echo "======refer_token======".$refer_token;die();

    if (isset($refer_token) && $refer_token != '') {


        if ($tokenn != '') {
            $taoh_vals = array(
                'mod' => 'tao_tao',
                'from' => 'profilecheck',
                'token' => $tokenn,
                'cache_name' => 'profile_short_' . taoh_get_api_token(),
            );
            $taoh_call = "users.tao.get";
            $user_data = json_decode(taoh_apicall_get($taoh_call, $taoh_vals));

            //echo'<pre>';print_r($user_data);die();
            if (isset($user_data->type) && isset($user_data->chat_name)) {
                if (isset($user_data->profile_complete) && $user_data->profile_complete == 0) {
                    //in complete profile
                } else {
                    $taoh_call = "core.refer.update";
                    $taoh_vals = array(
                        'ops' => 'invite',
                        'token' => taoh_get_dummy_token(),
                        'refer_token' => $refer_token,
                        'refer_key' => 'profile',
                    );
                    //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die();
                    $result = json_decode(taoh_apicall_post($taoh_call, $taoh_vals));
                }

            }
        }
       // print_r($result);die();
    }

   // echo "===========". $_COOKIE[TAOH_ROOT_PATH_HASH.'_temp_api_token'];die();
    if (!isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']) && $tokenn != '') {
        setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $tokenn, strtotime('+1 days'), '/');
        $_COOKIE[TAOH_ROOT_PATH_HASH . '_taoh_api_token'] = $tokenn; //add this for superglobal declaraiton to avoid first redirect
    }
    // die('-------------');
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] != '') {
        $url = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'];
        //delete_refer_token();

        taoh_redirect($url);
        exit();
    } else {
        //delete_refer_token();
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }


    //die();
}
function update_refer_for_profile_complete(){
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != '') {
        $refer_token = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'];


        $taoh_call = "core.refer.update";
        $taoh_vals = array(
            'ops' => 'invite',
            'token' => taoh_get_dummy_token(),
            'refer_token' => $refer_token,
            'refer_key' => 'profile',
        );
        //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die();
        $result = json_decode(taoh_apicall_post($taoh_call, $taoh_vals));

    }


}
function delete_refer_token()
{
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != '') {
        $refer_token = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'];
        $taoh_call = "core.refer.update";
        $taoh_vals = array(
            "mod" => "invite",
            'refer_id' => $refer_token,
            'refer_token' => $refer_token,
            'secret' => TAOH_API_SECRET,
            'token' => taoh_get_dummy_token(),
            'delete' => 1,

        );
        //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );exit();
        $res = taoh_apicall_post($taoh_call, $taoh_vals);
    }
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'refer_token', '', strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url', '', strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_data', '', strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'from_referral', '', strtotime('-2 days'), '/');

}

function site_config(){
    $return = array(
        'TAOH_SITE_URL_ROOT' => (defined('TAOH_SITE_URL_ROOT')) ? TAOH_SITE_URL_ROOT : false,
        'TAOH_SITE_ROOT_HASH' => (defined('TAOH_SITE_ROOT_HASH')) ? TAOH_SITE_ROOT_HASH : false,
        'TAOH_WERTUAL_NAME_SLUG' => (defined('TAOH_WERTUAL_NAME_SLUG')) ? TAOH_WERTUAL_NAME_SLUG : false,
        'TAOH_WERTUAL_SLUG' => (defined('TAOH_WERTUAL_SLUG')) ? TAOH_WERTUAL_SLUG : false,
        'TAOH_API_SECRET' => (defined('TAOH_API_SECRET')) ? TAOH_API_SECRET : false,
        'TAOH_SITE_NAME_SLUG' => (defined('TAOH_SITE_NAME_SLUG')) ? TAOH_SITE_NAME_SLUG : false,
        'TAOH_SITE_TITLE' => (defined('TAOH_SITE_TITLE')) ? TAOH_SITE_TITLE : false,
        'TAOH_SITE_DESCRIPTION' => (defined('TAOH_SITE_DESCRIPTION')) ? TAOH_SITE_DESCRIPTION : false,
        'TAOH_SITE_KEYWORDS' => (defined('TAOH_SITE_KEYWORDS')) ? TAOH_SITE_ROOT_HASH . ', Career Development, Job Search, Professional Networking, Career Events, Career Resources, Professional Growth, Connections, Wellness, Jobs, Work, Networking, Career Home, Career Growth Home' : false,
        'TAOH_SITE_PATH_ROOT' => (defined('TAOH_SITE_PATH_ROOT')) ? TAOH_SITE_DOC_ROOT : false,
        'TAOH_SITE_LOGO' => (defined('TAOH_SITE_LOGO')) ? TAOH_SITE_LOGO : false,
        //'TAOH_SITE_INFO_LOGO' => (defined('TAOH_SITE_LOGO')) ? TAOH_SITE_LOGO : false,
        //'TAOH_SITE_INFO_SQ_LOGO' => (defined('TAOH_SITE_FAVICON')) ? TAOH_SITE_FAVICON : false,
        'TAOH_SITE_FAVICON' => (defined('TAOH_SITE_FAVICON')) ? TAOH_SITE_FAVICON : false,
        'TAOH_CDN_PREFIX' => (defined('TAOH_CDN_PREFIX')) ? TAOH_CDN_PREFIX : false,
        'TAOH_API_PREFIX' => (defined('TAOH_API_PREFIX')) ? TAOH_API_PREFIX : false,
        'TAOH_OPS_PREFIX' => (defined('TAOH_OPS_PREFIX')) ? TAOH_OPS_PREFIX : false,
        'TAOH_DASH_PREFIX' => (defined('TAOH_DASH_PREFIX')) ? TAOH_DASH_PREFIX : false,
        'TAOH_WEMET_PREFIX' => (defined('TAOH_WEMET_PREFIX')) ? TAOH_WEMET_PREFIX : false,
        'TAOH_SITE_GA' => (defined('TAOH_SITE_GA')) ? TAOH_SITE_GA : false,
        'TAOH_CACHE_PREFIX' => (defined('TAOH_CACHE_PREFIX')) ? TAOH_CACHE_PREFIX : false,
        'TAOH_OBVIOUS_PREFIX' => (defined('TAOH_OBVIOUS_PREFIX')) ? TAOH_OBVIOUS_PREFIX : false,
        'TAOH_NETWORKPAGE_NAME' => (defined('TAOH_NETWORKPAGE_NAME')) ? TAOH_NETWORKPAGE_NAME : false,
        'TAOH_COMMUNITY_NAME' => (defined('TAOH_COMMUNITY_NAME')) ? TAOH_COMMUNITY_NAME : false,
        'TAOH_DEFAULT_TIMEZONE' => (defined('TAOH_DEFAULT_TIMEZONE')) ? TAOH_DEFAULT_TIMEZONE : false,
        'TAOH_TIMEZONE_FORMAT' => (defined('TAOH_TIMEZONE_FORMAT')) ? TAOH_TIMEZONE_FORMAT : false,
        'TAOH_API_TOKEN_DUMMY' => (defined('TAOH_API_TOKEN_DUMMY')) ? TAOH_API_TOKEN_DUMMY : false,
        'TAOH_SITE_DONATE_ENABLE' => (defined('TAOH_SITE_DONATE_ENABLE')) ? TAOH_SITE_DONATE_ENABLE : false,
        'TAOH_LOGIC_LOCK_CODE' => (defined('TAOH_LOGIC_LOCK_CODE')) ? TAOH_LOGIC_LOCK_CODE : false,
        'TAOH_LOGIC_LOCK_TEXT' => (defined('TAOH_LOGIC_LOCK_TEXT')) ? TAOH_LOGIC_LOCK_TEXT : false,
        'TAOH_WEMET_AUTHORIZATION_TOKEN' => (defined('TAOH_WEMET_AUTHORIZATION_TOKEN')) ? TAOH_WEMET_AUTHORIZATION_TOKEN : false,

        'TAOH_JOBS_ENABLE' => (defined('TAOH_JOBS_ENABLE')) ? TAOH_JOBS_ENABLE : true,
        'TAOH_JOBS_POST_LOCAL' => (defined('TAOH_JOBS_POST_LOCAL')) ? TAOH_JOBS_POST_LOCAL : false,
        'TAOH_JOBS_GET_LOCAL' => (defined('TAOH_JOBS_GET_LOCAL')) ? TAOH_JOBS_GET_LOCAL : false,
        'TAOH_ASKS_ENABLE' => (defined('TAOH_ASKS_ENABLE')) ? TAOH_ASKS_ENABLE : true,
        'TAOH_ASKS_POST_LOCAL' => (defined('TAOH_ASKS_POST_LOCAL')) ? TAOH_ASKS_POST_LOCAL : false,
        'TAOH_ASKS_GET_LOCAL' => (defined('TAOH_ASKS_GET_LOCAL')) ? TAOH_ASKS_GET_LOCAL : false,
        'TAOH_EVENTS_ENABLE' => (defined('TAOH_EVENTS_ENABLE')) ? TAOH_EVENTS_ENABLE : true,
        'TAOH_EVENTS_POST_LOCAL' => (defined('TAOH_EVENTS_POST_LOCAL')) ? TAOH_EVENTS_POST_LOCAL : false,
        'TAOH_EVENTS_GET_LOCAL' => (defined('TAOH_EVENTS_GET_LOCAL')) ? TAOH_EVENTS_GET_LOCAL : false,
        'TAOH_READS_ENABLE' => (defined('TAOH_READS_ENABLE')) ? TAOH_READS_ENABLE : true,
        'TAOH_READS_POST_LOCAL' => (defined('TAOH_READS_POST_LOCAL')) ? TAOH_READS_POST_LOCAL : false,
        'TAOH_READS_GET_LOCAL' => (defined('TAOH_READS_GET_LOCAL')) ? TAOH_READS_GET_LOCAL : false,
        'TAOH_MESSAGE_ENABLE' => (defined('TAOH_MESSAGE_ENABLE')) ? TAOH_MESSAGE_ENABLE : false,
        'TAOH_SCOUT_ENABLE' => (defined('TAOH_SCOUT_ENABLE')) ? TAOH_SCOUT_ENABLE : false,
        'TAOH_LEARNING_ENABLE' => (defined('TAOH_LEARNING_ENABLE')) ? TAOH_LEARNING_ENABLE : false,
        'TAOH_PAID_JOB_ENABLE' => (defined('TAOH_PAID_JOB_ENABLE')) ? TAOH_PAID_JOB_ENABLE : false,
        'TAOH_ENABLE_SEPARATE_EMPLOYER' => (defined('TAOH_ENABLE_SEPARATE_EMPLOYER')) ? TAOH_ENABLE_SEPARATE_EMPLOYER : false,
        'TAOH_ENABLE_OBVIOUSBABA' => (defined('TAOH_ENABLE_OBVIOUSBABA')) ? TAOH_ENABLE_OBVIOUSBABA : false,
        'TAOH_ENABLE_SIDEKICK' => (defined('TAOH_ENABLE_SIDEKICK')) ? TAOH_ENABLE_SIDEKICK : false,
        'TAOH_ENABLE_JUSASK' => (defined('TAOH_ENABLE_JUSASK')) ? TAOH_ENABLE_JUSASK : false,

        'TAOH_TABLES_DISCUSSION_SHOW' => (defined('TAOH_TABLES_DISCUSSION_SHOW')) ? TAOH_TABLES_DISCUSSION_SHOW : false,
        'TAOH_COMMENTS_SHOW' => (defined('TAOH_COMMENTS_SHOW')) ? TAOH_COMMENTS_SHOW : false,

        'TAOH_CHAT_BOT' => array(
            'TAOH_JUSASK_ENABLE' => (defined('TAOH_JUSASK_ENABLE')) ? TAOH_JUSASK_ENABLE : false,
            'TAOH_JUSASK_ICON' => (defined('TAOH_JUSASK_ICON')) ? TAOH_JUSASK_ICON : '',
            'TAOH_JUSASK_BOT_1' => (defined('TAOH_JUSASK_BOT_1')) ? TAOH_JUSASK_BOT_1 : '',
            'TAOH_JUSASK_BOT_1_NAME' => (defined('TAOH_JUSASK_BOT_1_NAME')) ? TAOH_JUSASK_BOT_1_NAME : '',
            'TAOH_JUSASK_BOT_1_ASK' => (defined('TAOH_JUSASK_BOT_1_ASK')) ? TAOH_JUSASK_BOT_1_ASK : '',
            'TAOH_JUSASK_BOT_1_TITLE' => (defined('TAOH_JUSASK_BOT_1_TITLE')) ? TAOH_JUSASK_BOT_1_TITLE : '',
            'TAOH_JUSASK_BOT_1_MSG1' => (defined('TAOH_JUSASK_BOT_1_MSG1')) ? TAOH_JUSASK_BOT_1_MSG1 : '',
            'TAOH_JUSASK_BOT_1_MSG2' => (defined('TAOH_JUSASK_BOT_1_MSG2')) ? TAOH_JUSASK_BOT_1_MSG2 : '',
            'TAOH_JUSASK_BOT_1_DESCRIPTION' => (defined('TAOH_JUSASK_BOT_1_DESCRIPTION')) ? TAOH_JUSASK_BOT_1_DESCRIPTION : '',
            'TAOH_JUSASK_BOT_1_IMG' => (defined('TAOH_JUSASK_BOT_1_IMG')) ? TAOH_JUSASK_BOT_1_IMG : '',

            'TAOH_JUSASK_BOT_2' => (defined('TAOH_JUSASK_BOT_2')) ? TAOH_JUSASK_BOT_2 : '',
            'TAOH_JUSASK_BOT_2_NAME' => (defined('TAOH_JUSASK_BOT_2_NAME')) ? TAOH_JUSASK_BOT_2_NAME : '',
            'TAOH_JUSASK_BOT_2_ASK' => (defined('TAOH_JUSASK_BOT_2_ASK')) ? TAOH_JUSASK_BOT_2_ASK : '',
            'TAOH_JUSASK_BOT_2_TITLE' => (defined('TAOH_JUSASK_BOT_2_TITLE')) ? TAOH_JUSASK_BOT_2_TITLE : '',
            'TAOH_JUSASK_BOT_2_MSG1' => (defined('TAOH_JUSASK_BOT_2_MSG1')) ? TAOH_JUSASK_BOT_2_MSG1 : '',
            'TAOH_JUSASK_BOT_2_MSG2' => (defined('TAOH_JUSASK_BOT_2_MSG2')) ? TAOH_JUSASK_BOT_2_MSG2 : '',
            'TAOH_JUSASK_BOT_2_DESCRIPTION' => (defined('TAOH_JUSASK_BOT_2_DESCRIPTION')) ? TAOH_JUSASK_BOT_2_DESCRIPTION : '',
            'TAOH_JUSASK_BOT_2_IMG' => (defined('TAOH_JUSASK_BOT_2_IMG')) ? TAOH_JUSASK_BOT_2_IMG : '',

            'TAOH_JUSASK_SUPPORT_BOT' => (defined('TAOH_JUSASK_SUPPORT_BOT')) ? TAOH_JUSASK_SUPPORT_BOT : '',
            'TAOH_JUSASK_SUPPORT_BOT_NAME' => (defined('TAOH_JUSASK_SUPPORT_BOT_NAME')) ? TAOH_JUSASK_SUPPORT_BOT_NAME : '',
            'TAOH_JUSASK_SUPPORT_BOT_ASK' => (defined('TAOH_JUSASK_SUPPORT_BOT_ASK')) ? TAOH_JUSASK_SUPPORT_BOT_ASK : '',
            'TAOH_JUSASK_SUPPORT_BOT_TITLE' => (defined('TAOH_JUSASK_SUPPORT_BOT_TITLE')) ? TAOH_JUSASK_SUPPORT_BOT_TITLE : '',
            'TAOH_JUSASK_SUPPORT_BOT_MSG1' => (defined('TAOH_JUSASK_SUPPORT_BOT_MSG1')) ? TAOH_JUSASK_SUPPORT_BOT_MSG1 : '',
            'TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION' => (defined('TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION')) ? TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION : '',
            'TAOH_JUSASK_SUPPORT_BOT_IMG' => (defined('TAOH_JUSASK_SUPPORT_BOT_IMG')) ? TAOH_JUSASK_SUPPORT_BOT_IMG : '',

            'TAOH_SITE_REFERRAL_ID' => (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_id'])) ? $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_id'] : '',
        ),

        'SUPERADMIN_FNAME' => (defined('SUPERADMIN_FNAME')) ? SUPERADMIN_FNAME : 'Super',
        'SUPERADMIN_LNAME' => (defined('SUPERADMIN_LNAME')) ? SUPERADMIN_LNAME : 'Admin',
        'SUPERADMIN_AVATAR' => (defined('SUPERADMIN_AVATAR')) ? SUPERADMIN_AVATAR : 'avatar_def',
        'SUPERADMIN_AVATAR_IMG' => (defined('SUPERADMIN_AVATAR_IMG')) ? SUPERADMIN_AVATAR_IMG : '',
        'TAOH_FOOTER_MENU_ARRAY' => (defined('TAOH_FOOTER_MENU_ARRAY')) ? TAOH_FOOTER_MENU_ARRAY : '',
        'TAOH_DONATE' => array(
            'TAOH_COMPANY_REG'  => (defined('TAOH_COMPANY_REG')) ? TAOH_COMPANY_REG : '',
            'TAOH_ORG_TYPE'  => (defined('TAOH_ORG_TYPE')) ? TAOH_ORG_TYPE : '',
            'TAOH_TAX_ID'       => (defined('TAOH_TAX_ID')) ? TAOH_TAX_ID : '',
            'TAOH_ORG_NAME'     => (defined('TAOH_ORG_NAME')) ? TAOH_ORG_NAME : '',
            'TAOH_ORG_ADDRESS'  => (defined('TAOH_ORG_ADDRESS')) ? TAOH_ORG_ADDRESS : '',
            'TAOH_ORG_PHONE'    => (defined('TAOH_ORG_PHONE')) ? TAOH_ORG_PHONE : '',
        ),

    );

    return $return;
}

// Subsecret Create Function
if (!function_exists('taoh_subsecret_arr')) {
    function taoh_subsecret_arr($subsecret = TAOH_SITE_ROOT_HASH)
    {
        $taoh_dash = false;
        if (defined('TAO_DASH_VERSION') && TAO_DASH_VERSION) $taoh_dash = true;
        $return = false;
        if (!$taoh_dash) {

            $return = site_config();
            $taoh_subsecret_arr['key'] = TAOH_SITE_ROOT_HASH;
            $taoh_subsecret_arr['value'] = $return;
            taoh_prep_subsecret($taoh_subsecret_arr, 3);
        }
        return $return;
    }
}


if (!function_exists('taoh_prep_subsecret')) {
    function taoh_prep_subsecret($taoh_subsecret_arr, $place)
    {
        //print_r($taoh_subsecret_arr);
        // echo "====aaaaaaaaaaa=======".json_encode( $taoh_subsecret_arr[ 'value' ] ) ;
        // echo "======I am here ======";die();
        $url = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" . $taoh_subsecret_arr['key'] . ".cache";
        /*foreach ( $taoh_subsecret_arr[ 'value' ] as $key => $value ){
          $to_enter = 'if ( ! defined( \''.$key.'\' ) ) define( \''.$key.'\', \''.$value.'\' );';
          file_put_contents( $url, $to_enter, FILE_APPEND | LOCK_EX );
      }*/
        // echo "<br>======place========".$place;
        file_put_contents($url, json_encode($taoh_subsecret_arr['value']));  //kalpana added
        //echo "===1111111======";die();
        return 1;
    }
}
//TAOH_SUBSECRET_HASH  force_subsecret_data
function force_subsecret_data($taoh_subsecret)
{
    $return = taoh_subsecret_arr();
    $vals = json_encode($return);
    $post_data = array(
        'code' => 'tc2asi3iida2',
        'ops' => 'subsecret',
        'value' => $vals,
        'key' => $taoh_subsecret,
        'status' => $taoh_subsecret,
        // 'debug'=> 1,
    );
    $result = taoh_remote_cache($post_data);
}

if (!function_exists('taoh_uuid_fetch1')) {
    function taoh_uuid_fetch1($taoh_uuid, $ops = 'get')
    {
        $post_data = array(
            'code' => 'tc2asi3iida2',
            'ops' => 'uuid',
            'value' => $taoh_uuid,
            'status' => $ops,
            //'debug'=> 1,
        );
        $result = taoh_remote_cache($post_data);
        $return_arr = json_decode($result, true);

        if (!(isset($return_arr['success']) && $return_arr['success'])) {
            // if $return_arr[ 'output' ] is set and is not empty, then save it into the session
            if (isset($return_arr['output']) && is_array($return_arr['output']) && count($return_arr['output']) > 0) {
                return $return_arr['output'];
            }
            return true;
        }
        return false;
    }
}


if (!function_exists('taoh_get_subsecret_info')) {
    function taoh_get_subsecret_info($taoh_subsecret, $force = 0)
    {
        $return = false;
        $taoh_dash = false;
        $url = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" . $taoh_subsecret . ".cache";

        if (file_exists($url)) {
            if ((time() - filemtime($url)) >= 7 * 24 * 60 * 60) {
                unlink($url);
            } else {
                $return = true;
                //echo file_get_contents($url);
               // die('000000000000');
                // require_once ( $url ); //Question : what is the purpose of require the cache file ?
            }
        }
        //echo $return;die();

        if ( ! $return || $force ){
        //if (1) {

            // Nothing local, lets check the redis now.
            $post_data = array(
                'code' => 'tc2asi3iida2',
                //'ops' => 'subsecret',
                'ops' => 'subsecret',//changed by kalpana
                'value' => $taoh_subsecret,
                'status' => 'get',
                //'debug'=> 1,
            );
            $result = taoh_remote_cache($post_data);

            $return_arr = json_decode($result, true);
            //echo"====aaaaaaaaaaa========";print_r($return_arr);die();

            if (isset($return_arr['success']) && $return_arr['success']) {
                // if $return_arr[ 'output' ] is set and is not empty, then save it into the session
                // if ( isset( $return_arr[ 'output' ] ) && is_array( $return_arr[ 'output' ] ) && count( $return_arr[ 'output' ] ) > 0 ){
                if (isset($return_arr['output'])) {
                    $taoh_subsecret_arr['key'] = $taoh_subsecret;
                    $taoh_subsecret_arr['value'] = json_decode($return_arr['output'], true);
                    // print_r($taoh_subsecret_arr);die('-----------');
                    taoh_prep_subsecret($taoh_subsecret_arr, 1);
                }
            } else {
                $taoh_subsecret_arr['key'] = $taoh_subsecret;


                $return = taoh_subsecret_arr();

                //echo "===sadsad=======";print_r($return);die();

                // if not in dash, create from current config. and return it as an array
                if (!$return) return false;


                $vals = json_encode($return);
                $taoh_subsecret_arr['value'] = $return;
                // print_r('--Here-----');
                //print_r($vals);
                //print_r($taoh_subsecret_arr);
                // Now push it to redis
                $post_data = array(
                    'code' => 'tc2asi3iida2',
                    'ops' => 'subsecret',
                    'value' => $vals,
                    'key' => $taoh_subsecret,
                    'status' => $taoh_subsecret,
                    // 'debug'=> 1,
                );
                $result = taoh_remote_cache($post_data);
                $result = taoh_prep_subsecret($taoh_subsecret_arr, 2);
                //require_once ( $url );
            }
        }
        // echo "===========".$url;
        return $return;
    }
}

function get_diff_dates($year1, $month1, $year2, $month2)
{
    $date1 = $year1 . '-' . $month1;
    $date2 = $year2 . '-' . $month2;
    $diff = abs(strtotime($date2) - strtotime($date1));
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    return $years . ' Yrs ' . $months . ' Mo ';
}

function get_month_from_number($month)
{
    $month_arr = array(
        '1' => 'Jan',
        '2' => 'Feb',
        '3' => 'Mar',
        '4' => 'Apr',
        '5' => 'May',
        '6' => 'Jun',
        '7' => 'Jul',
        '8' => 'Aug',
        '9' => 'Sep',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec',
    );
    return $month_arr[$month];
}

function taoh_lp_get_header()
{
    include_once('core/themes/header_lp.php');
}

function taoh_lp_get_footer()
{
    include_once('core/themes/footer_lp.php');
}

function taoh_land_get_header()
{
    include_once('core/themes/header_landing.php');
}

function taoh_land_get_footer()
{
    include_once('core/themes/footer_landing.php');
}

function taoh_blog_recipe_get($category = "", $count = "", $id = "")
{
    $category = (isset($category) && $category) ? $category : '';
    $count = (isset($count) && $count) ? $count : '';
    $id = (isset($id) && $id) ? $id : '';
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        "mod" => 'core',
        "ops" => 'related',
        "type" => 'blog',
        "recipe_id" => $id,
        'token' => taoh_get_dummy_token(1),
        "category" => $category,
        "count" => $count,
        'cache_time' => 30,
        // 'debug'=>1
    );
    $cache_name = $taoh_call.'_' . hash('sha256', $taoh_call . serialize($taoh_vals));
    //$taoh_vals[ 'cfcache' ] = $cache_name;
    $taoh_vals[ 'cache_name' ] = $cache_name;
    ksort($taoh_vals);
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
    $content = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
    if ($content != "") {
        $response = json_decode($content, true);
        return $response;
    }
    return array();
}

/* Function for save log Start */
/* function taoh_save_log_entry($ptoken,$message) {

  $dir_filename = TAOH_PLUGIN_PATH.'/cache/log/';
  if (!file_exists($dir_filename)) {
    mkdir( $dir_filename,0777,false );
  }
  $date = date("Ymd");
  $file_name = $ptoken.'_'.$date.'_log.csv';
  $i = 1;
  $filename = TAOH_PLUGIN_PATH.'/cache/log/'.$file_name;

  if (file_exists( $filename ) ){
    $rows = file($filename);
    $last_row = array_pop($rows);
    $data = str_getcsv($last_row);
    $inc = $data[0];

    $fp = fopen ($filename, 'a');
    $array[] = [$inc+1,time(),'',$message,'',$ptoken,''];
    foreach ($array as $fields) {
      fputcsv ($fp, $fields);
    }
    fclose($fp);
  }else{
    //Yesterday file check Start
    $yest_date = date('Ymd',strtotime("-1 days"));
    $yest_file_name = $ptoken.'_'.$yest_date.'_log.csv';
    $yest_filename = TAOH_PLUGIN_PATH.'/cache/log/'.$yest_file_name;
    if (file_exists( $yest_filename ) ){
      unlink($yest_filename);
    }
    //Yesterday file check End
    $fp = fopen ($filename, 'w');
    $csv = file($filename, FILE_IGNORE_NEW_LINES);
    $first = ( explode(',', $csv[0])[0] ) ? explode(',', $csv[0])[0] : 0 ;
    if($first == 0){
      $headers = ['ID', 'timestamp', 'level', 'message', 'context', 'user_id', 'event_type'];
      fputcsv($fp, $headers);
    }
    $array[] = [$i,time(),'',$message,'',$ptoken,''];
    foreach ($array as $fields) {
      fputcsv ($fp, $fields);
    }
    fclose($fp);
  }
} */
/* Function for save log End */


function group_by_array_val($grp_arr, $grp_key, $ksort = false)
{
    $output_arr = array();
    foreach ($grp_arr as $key => $item) {
        $output_arr[$item[$grp_key]][$key] = $item;
    }
    if ($ksort) ksort($output_arr);

    return $output_arr;
}

function getValidTimezone($timezone)
{
    if (!empty($timezone) && in_array($timezone, timezone_identifiers_list())) {
        return $timezone;
    } elseif (defined('TAOH_DEFAULT_TIMEZONE')) {
        return TAOH_DEFAULT_TIMEZONE;
    } else {
        return 'UTC';
    }
}

function getJsonDecodedData($data, $assoc = true)
{
    $decoded = json_decode($data, $assoc);
    return (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
}

function addslashes_recursive($data)
{
    if (is_array($data)) {
        return array_map('addslashes_recursive', $data);
    }
    if (is_string($data)) {
        return addslashes($data);
    }

    return $data;
}

function stripslashes_recursive($data)
{
    if (is_array($data)) {
        return array_map('stripslashes_recursive', $data);
    }
    if (is_string($data)) {
        return stripslashes($data);
    }

    return $data;
}

function addslashes_recursive_reference(&$data)
{
    if (is_array($data)) {
        foreach ($data as &$value) {
            addslashes_recursive_reference($value);
        }
    } elseif (is_string($data)) {
        $data = addslashes($data);
    }
}

function stripslashes_recursive_reference(&$data)
{
    if (is_array($data)) {
        foreach ($data as &$value) {
            stripslashes_recursive_reference($value);
        }
    } elseif (is_string($data)) {
        $data = stripslashes($data);
    }
}

/**
 * Encode data to Base64URL
 * Convert Base64 to Base64URL by replacing + with - and / with _
 *
 * @param string $data
 * @return boolean|string
 */
function base64url_encode(string $data): bool|string
{
    $url = strtr(base64_encode($data), '+/', '-_');

    // Remove padding character from the end of line
    return rtrim($url, '=');
}

/**
 * Decode data from Base64URL
 * Convert Base64URL to Base64 by replacing - with + and _ with /
 *
 * @param string $data
 * @param boolean $strict
 * @return boolean|string
 */
function base64url_decode(string $data, bool $strict = false): bool|string
{
    return base64_decode(strtr($data, '-_', '+/'), $strict);
}


function taoh_add_var_to_url($var_name, $var_value)
{
    $url_components = parse_url($_SERVER['REQUEST_URI']);
    $params = array();
    if (isset($url_components['query'])) {
        parse_str($url_components['query'], $params);
    }
    //print_r($params);exit();
    if (!isset($params[$var_name])) {
        $params[$var_name] = $var_value;
        $new_query_string = http_build_query($params);
        $new_url = $url_components['path'];
        if (!empty($new_query_string)) {
            $new_url .= '?' . $new_query_string;
        }
        header('Location: ' . $new_url);
        taoh_exit();
    }
    return;
}

/**
 * value = 0 for no token traffic, value = 1 for token traffic and value = 2 for pages with personal info
 */
function taoh_uslo_flag_set($value)
{

    // Parse the current URL to extract its components
    $url_components = parse_url($_SERVER['REQUEST_URI']);

    // Check if there are existing query parameters
    $params = array();
    if (isset($url_components['query'])) {
        parse_str($url_components['query'], $params);
    }

    // Determine if a change is needed
    $changeNeeded = false;

    // Set or remove the 'uslo' parameter based on the value
    if ($value != 0) {
        // Check if 'uslo' needs to be updated
        if (!isset($params['uslo']) || $params['uslo'] != $value) {
            $params['uslo'] = $value;
            $changeNeeded = true;
        }
    } else {
        // Remove 'uslo' if $value is zero and it exists
        if (isset($params['uslo'])) {
            unset($params['uslo']);
            $changeNeeded = true;
        }
    }

    // Proceed with redirection only if a change is necessary
    if ($changeNeeded) {

        $new_url = taoh_add_var_to_url('uslo', $value);

    }

    // If no change is needed, just return
    return;
}

/**
 * Get current uslo value
 */
function taoh_get_uslo_value()
{
    // Parse the current URL to extract its components
    $url_components = parse_url($_SERVER['REQUEST_URI']);

    // Initialize the default value for 'uslo' as null
    $uslo_value = null;

    // Check if there are existing query parameters
    if (isset($url_components['query'])) {
        // Parse the query string into an associative array
        $params = [];
        parse_str($url_components['query'], $params);

        // Retrieve the value of 'uslo' if it exists
        if (isset($params['uslo'])) {
            $uslo_value = $params['uslo'];
        }
    }

    // Return the value of 'uslo' or null if not found
    return $uslo_value;
}


function taoh_get_avatar_src()
{
    $logged_user = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null;

    if (isset($logged_user->avatar_image) && $logged_user->avatar_image != '') {
        return $logged_user->avatar_image ;
    } else if (isset($logged_user->avatar) && $logged_user->avatar != '') {
        return TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $logged_user->avatar . '.png' ;
    } else {
        return TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png';
    }
}

function taoh_get_avatar($logged_user)
{

    if (isset($logged_user->avatar_image) && $logged_user->avatar_image != '') {
        return '<img width="40" height="40" style="border-radius: 20px;" src="' . $logged_user->avatar_image . '" alt="Profile Image">';
    } else if (isset($logged_user->avatar) && $logged_user->avatar != '') {
        return '<img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $logged_user->avatar . '.png" alt="">';
    } else {
        return '<img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png" alt="">';
    }
}

function taoh_get_profile_image()
{
    if (!taoh_user_is_logged_in()) {
        return '<img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png" alt="">';
    }
    $logged_user = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null;

    if (isset($logged_user->avatar_image) && $logged_user->avatar_image != '' && @getimagesize($logged_user->avatar_image)) {
        return '<img width="40" height="40" style="border-radius: 20px;" src="' . $logged_user->avatar_image . '" alt="Profile Image">';
    } else if (isset($logged_user->avatar) && $logged_user->avatar != '') {
        return '<img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/' . $logged_user->avatar . '.png" alt="">';
    } else {
        return '<img width="40" height="40" src="' . TAOH_OPS_PREFIX . '/avatar/PNG/128/avatar_def.png" alt="">';
    }
}

function taoh_rand_string_gen($length = 4)
{
    // Characters to be used for generating random string
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // Length of characters
    $characters_length = strlen($characters);

    // Generate a unique seed based on current time
    $seed = time();

    // Initialize random number generator with seed
    srand($seed);

    $random_string_array = [];
    for ($i = 0; $i < $length; $i++) {
        // Generate random index using the unique seed
        $random_index = rand(0, $characters_length - 1);
        // Append character at the random index to the random string array
        $random_string_array[] = $characters[$random_index];
    }
    // Reset random number generator
    srand();
    // Return the generated random string
    return implode('', $random_string_array);
}

function taoh_get_unique_url($secure = false)
{
    // Get the current URL
    $return = $_SERVER['REQUEST_URI'];
    // Check if "/stl" or "/stlo" already exists in the URL
    if (strpos($return, '/stl') === false) {
        list($return, $post) = array_pad(explode('?', $return, 2), 2, '');
        $return = rtrim($return, '/');
        // Add "/stlo" to the URL
        if ($secure) {
            $return .= '/stl' . TAOH_MY_NOW_CODE;
        } else {
            $return .= '/stlo';
        }
        if (!empty($post)) {
            $return .= '?' . $post;
        }
    }
    return $return;

}

function taoh_check_and_refresh_page_cache($secure = false)
{
    // Check if TAOH_API_TOKEN is defined and not null
    $return = $_SERVER['REQUEST_URI'];
    if (defined('TAOH_API_TOKEN') && TAOH_API_TOKEN) {
        if (strpos($return, '/stl') === false) {
            $url = taoh_get_unique_url($secure);
            header('Location: ' . $url);
            exit(); // Stop further execution
        }
    }
    return;
}

function taoh_get_id($option)
{
    foreach ($option as $key => $value) {
        $keys = $key;
    }
    return $keys;
}

function taoh_get_skill_keys($option)
{
    $keys = array();
    foreach ($option as $key => $value) {
        $keys[] = $key;
    }
    return $keys;
}

function taoh_get_skill_vals($option)
{
    $keys = '';
    foreach ($option as $key => $value) {
        if ($key == 0) {
            $keys .= $value;
        } else {
            $keys .= '-' . $value;
        }
    }
    return $keys;
}

function taoh_get_session_skill_vals($option)
{
    $keys = array();
    foreach ($option as $key => $value) {
        list ($pre, $post) = explode(':>', $value);
        $keys[] = $post;
    }
    return $keys;
}

function taoh_get_date($create_date=''){
	$create_year = substr($create_date, 0, 4);
	$create_month = substr($create_date, 4, 2);
	$create_day = substr($create_date, 6, 2);
	return $create_year.'-'.$create_month.'-'.$create_day;
}
function taoh_fullyear_convert_time($timestamp,$convert = false) {

    if($convert){
        $year = date('Y');
        // Get the first two digits
        $first_two_digits = substr($year, 0, 2);
        $timestamp = $first_two_digits . $timestamp;
    }


    $date = DateTime::createFromFormat('YmdHis', $timestamp , new DateTimeZone('America/New_york'));
    return $date->format('Y-m-d H:i:s'); // Output: 2024-11-25 09:20:00

}

function taoh_fullyear_convert($timestamp,$convert = false) {

    if($convert){
        $year = date('Y');
        // Get the first two digits
        $first_two_digits = substr($year, 0, 2);
        $timestamp = $first_two_digits . $timestamp;
    }
    // Check if the input string length is correct
    if (strlen($timestamp) !== 14) {
        throw new InvalidArgumentException("Invalid timestamp length.");
    }

    $date = DateTime::createFromFormat('YmdHis', $timestamp , new DateTimeZone('America/New_york'));

    // Extract date and time components from the string
    /* $year = substr($timestamp, 0, 4);
    $month = substr($timestamp, 4, 2);
    $day = substr($timestamp, 6, 2);
    $hour = substr($timestamp, 8, 2);
    $minute = substr($timestamp, 10, 2);
    $second = substr($timestamp, 12, 2);
    $timezone = taoh_user_timezone();
    // Create a DateTime object
    $date = DateTime::createFromFormat('YmdHis', $year . $month . $day . $hour . $minute . $second , new DateTimeZone($timezone));

    if (!$date) {
        throw new Exception("Invalid timestamp format.");
    } */

    // Get the current date and time

    $now = new DateTime('now', new DateTimeZone('America/New_york'));

    // Calculate the interval
    $interval = $now->diff($date);

    // Determine the appropriate time unit to return
    if ($interval->y > 0) {
        return $interval->y . 'y ago';
    } elseif ($interval->m > 0) {
        return $interval->m . 'm ago';
    } elseif ($interval->d > 0) {
        return $interval->d . 'd ago';
    } elseif ($interval->h > 0) {
        return $interval->h . 'h ago';
    } elseif ($interval->i > 0) {
        return $interval->i . 'mi ago';
    } else {
        return $interval->s . 's ago';
    }
}

if (!function_exists('event_time_display_local')) {
    function event_time_display_local($input_date, $locality = 0, $event_timezone_abbr = '', $input = 'date', $format = 'D, M d, Y h:i A')
    {

        //echo "====222222222===".$event_timezone_abbr;die();
        $user_timezone = new DateTimeZone($event_timezone_abbr);
            //echo '<pre>';print_r($user_timezone);die();
        $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);

        return $datetime->format($format); // Output: Mon, Nov 25, 2024 09:20
    }
}

if (!function_exists('event_time_display')) {
    function event_time_display($input_date, $locality = 0, $event_timezone_abbr = '', $input = 'date', $format = 'D, M d, Y h:i A')
    {
        $user_timezone = new DateTimeZone(taoh_user_timezone());
        if ($locality) {
            // Global event
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);
        } else {
            // Local event
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, new DateTimeZone('UTC'));
            $datetime->setTimezone($user_timezone);

            //kalpana
           /* $user_timezone = new DateTimeZone($event_timezone_abbr);
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);*/
        }

        return $datetime->format($format); // Output: Mon, Nov 25, 2024 09:20
    }
}

if (!function_exists('get_event_datetime')) {
    function get_event_datetime($input_date, $locality = 0)
    {
        $user_timezone = new DateTimeZone(taoh_user_timezone());
        if ($locality) {
            // Global event
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);
        } else {
            // Local event
            $datetime = DateTime::createFromFormat('YmdHis', $input_date, new DateTimeZone('UTC'));
            $datetime->setTimezone($user_timezone);
        }

        return $datetime;
    }
}

if (!function_exists('isValidVideoUrl')) {
    function isValidVideoUrl($url) {
        // Check if the URL is well-formed
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Use get_headers() to fetch the headers for the URL
        $headers = @get_headers($url, 1);

        if ($headers === false) {
            return false; // URL is inaccessible
        }

        // Check for HTTP status code 200
        if (strpos($headers[0], '200') === false) {
            return false;
        }

        // Check if the Content-Type header indicates a video
        if (isset($headers['Content-Type'])) {
            $contentType = is_array($headers['Content-Type'])
                ? $headers['Content-Type'][0]
                : $headers['Content-Type'];

            // Validate against common video MIME types
            $validVideoTypes = [
                'video/mp4',
                'video/x-msvideo',
                'video/x-matroska',
                'video/webm',
                'video/ogg',
                'application/vnd.apple.mpegurl', // HLS playlist
                'application/x-mpegURL', // HLS playlist
            ];

            return in_array($contentType, $validVideoTypes, true);
        }

        return false;
    }
}

function truncateWords($text, $maxCharsPerLine = 50) {
    // Split text into words
    $words = explode(' ', $text);
    $output = '';
    $lineCount = 0;
    $currentLineLength = 0;

    foreach ($words as $word) {
        // Check if adding the word exceeds the line limit
        if ($currentLineLength + strlen($word) + 1 > $maxCharsPerLine) {
            $lineCount++;
            $currentLineLength = 0;

            // Stop if two lines are already reached
            if ($lineCount >= 2) {
                $output = rtrim($output) . '...';
                break;
            }

//            $output .= "\n";
        }

        $output .= $word . ' ';
        $currentLineLength += strlen($word) + 1; // +1 for space
    }

    return nl2br(trim($output));
}

function is_cache_valid($cache_file, $cache_validity_seconds) {
    return file_exists($cache_file) && (time() - filemtime($cache_file)) <= $cache_validity_seconds;
}

function read_cache_file($cache_file) {
    return file_get_contents($cache_file);
}

function update_cache_file($cache_file, $data) {
    $cache_dir = dirname($cache_file);
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0777, true);
    }
    file_put_contents($cache_file, $data);
}

function remove_cache_file($cache_dir, $remove_array)
{
    foreach ($remove_array as $pattern) {
        $hasWildcard = str_contains($pattern, '*');
        $hasExtension = pathinfo($pattern, PATHINFO_EXTENSION) !== '';

        $searchPattern = $cache_dir . $pattern;

        if (!$hasWildcard && !$hasExtension) {
            if (is_file($searchPattern)) {
                unlink($searchPattern);
            }
            $searchPattern .= '.cache';
        }

        $files = glob($searchPattern);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

function taoh_remote_file_upload($file, $uploadUrl = TAOH_CDN_PREFIX . '/cache/upload/now')
{
    $response = ['success' => false, 'output' => 'Failed to upload file.'];

    try {
        // Check if a file is uploaded
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];

            $ch = curl_init();
            $fileData = new CURLFile($fileTmpPath, mime_content_type($fileTmpPath), $fileName);

            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['fileToUpload' => $fileData, 'opscode' => TAOH_OPS_CODE]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Check for errors
            if (curl_errno($ch)) {
                $response['output'] = 'cURL error: ' . curl_error($ch);
            } elseif ($httpCode !== 200) {
                $response['output'] = "Failed to upload file. HTTP Code: $httpCode";
            } else {
                $resultData = json_decode($result, true);
                if ($resultData && isset($resultData['success']) && $resultData['success']) {
                    $response['success'] = true;
                    $response['output'] = $resultData['output'] ?? 'File uploaded successfully.';
                } else {
                    $response['output'] = $resultData['output'] ?? 'Failed to upload file.';
                }
            }

            curl_close($ch);
        } else {
            $response['output'] = 'No file uploaded or there was an upload error.';
        }
    } catch (Exception $e) {
        $response['output'] = 'An error occurred: ' . $e->getMessage();
    }

    return $response;
}

if (!function_exists('taoh_sanitizeInput')) {
    function taoh_sanitizeInput($input, $sanitizeType = 'alphanumeric')
    {
        if (strtolower($sanitizeType) === 'alphanumeric') {
            return preg_replace("/[^a-zA-Z0-9]/", "", $input);
        } elseif (strtolower($sanitizeType) === 'alphanumericwithspaces') {
            return preg_replace("/[^a-zA-Z0-9 ]/", "", $input);
        } else {
            return preg_replace("/[^a-zA-Z0-9]/", "", $input);
        }
    }
}

if (!function_exists('getRoundedTimestamp')) {
    function getRoundedTimestamp($interval, $unit = 'minutes')
    {
        $unitsInSeconds = [
            'seconds' => 1,
            'minutes' => 60,
            'hours' => 3600,
            'days' => 86400,
            'years' => 31536000
        ];

        if (!isset($unitsInSeconds[$unit]) || $interval <= 0) {
            throw new InvalidArgumentException("Invalid unit or interval. Use a positive interval with 'seconds', 'minutes', 'hours', 'days', or 'years'.");
        }

        $currentTimestamp = time();
        $intervalInSeconds = $interval * $unitsInSeconds[$unit];

        return ceil($currentTimestamp / $intervalInSeconds) * $intervalInSeconds;
    }
}

if (!function_exists('getRandomIceBreakQuestions')) {
    function getRandomIceBreakQuestions()
    {
    $questions = array('What is the hardest part of finding a job?',
                'Why did you choose a new job?',
                'What is the best way to find work?',
                'How do you show you are special when many want the same job?',
                'What work choice makes you proud?',
                'What do you wish you knew before looking for a job?',
                'How do you feel when a job does not work out?',
                'What is a smart job tip you can share?',
                'How do you meet new people for a job?',
                'How do you show your skills when changing jobs?',
                'How do you keep going when job searching takes long?',
                'What tells you a company is a good place to work?',
                'What would you ask a job expert?',
                'How do you think computers will change work?',
                'What skill will help you in the next 5 to 10 years?',
                'What change in your field makes you excited?',
                'What skill would you work on for a safe future?',
                'How do you use online classes to learn?',
                'Which jobs do you think will grow soon?',
                'How do you feel about short-term jobs?',
                'Which jobs are safe when computers do more work?',
                'What are you doing to get better at work?',
                'What is the best work advice you ever got?',
                'What is one wrong idea about job searching you want to fix?',
                'How do you make new friends at work?',
                'What habit helps you do your best at work?',
                'What work risk paid off for you?',
                'If you could change one work choice, what would it be?',
                'What mistake do people make when looking for jobs?',
                'What is the worst part of the job hunt?',
                'How do you know if a company is not a good place to work?',
                'What words in a job ad should you watch out for?',
                'How do you talk about pay without feeling shy?',
                'What new work idea would you like to see?',
                'What does a good mix of work and fun look like?',
                'What is one goal you have for your work?');

       $rand = array_rand($questions, 5);
        $iceBreakQuestions = '';
       foreach($rand as $r){
              $iceBreakQuestions .= '<div class="p-1 mb-3" style="border: 2px solid #D3D3D3; border-radius: 12px;">
              '.$questions[$r].'</div>';
       }
        return $iceBreakQuestions;
    }
}

/* function taoh_remote_file_upload($file, $uploadUrl = TAOH_CDN_PREFIX . '/cache/upload/now')
{
    $response = ['success' => false, 'output' => 'Failed to upload file.'];

    try {
        // Check if a file is uploaded
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];

            $ch = curl_init();
            $fileData = new CURLFile($fileTmpPath, mime_content_type($fileTmpPath), $fileName);

            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['fileToUpload' => $fileData, 'opscode' => TAOH_OPS_CODE]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Check for errors
            if (curl_errno($ch)) {
                $response['output'] = 'cURL error: ' . curl_error($ch);
            } elseif ($httpCode !== 200) {
                $response['output'] = "Failed to upload file. HTTP Code: $httpCode";
            } else {
                $resultData = json_decode($result, true);
                if ($resultData && isset($resultData['success']) && $resultData['success']) {
                    $response['success'] = true;
                    $response['output'] = $resultData['output'] ?? 'File uploaded successfully.';
                } else {
                    $response['output'] = $resultData['output'] ?? 'Failed to upload file.';
                }
            }

            curl_close($ch);
        } else {
            $response['output'] = 'No file uploaded or there was an upload error.';
        }
    } catch (Exception $e) {
        $response['output'] = 'An error occurred: ' . $e->getMessage();
    }

    return $response;
} */

function generateSecureSlug(string $slugString, int $short = 0, string $algorithm = 'sha256'): string
{
    $slugHash = hash($algorithm, $slugString);

    if (in_array($short, [16, 32])) {
        return substr($slugHash, 0, $short);
    }

    return $slugHash;
}

function getBrowserAndOS($userAgent) {
    $browser = 'Unknown';
    $version = 'Unknown';
    $os = 'Unknown';

    // OS detection
    if (preg_match('/Windows NT 10.0/i', $userAgent)) $os = 'Windows 10';
    elseif (preg_match('/Windows NT 6.3/i', $userAgent)) $os = 'Windows 8.1';
    elseif (preg_match('/Windows NT 6.2/i', $userAgent)) $os = 'Windows 8';
    elseif (preg_match('/Windows NT 6.1/i', $userAgent)) $os = 'Windows 7';
    elseif (preg_match('/Mac OS X/i', $userAgent)) $os = 'Mac OS X';
    elseif (preg_match('/Linux/i', $userAgent)) $os = 'Linux';

    // Browser detection
    if (preg_match('/Chrome\/([0-9\.]+)/i', $userAgent, $matches)) {
        $browser = 'Chrome';
        $version = $matches[1];
    } elseif (preg_match('/Firefox\/([0-9\.]+)/i', $userAgent, $matches)) {
        $browser = 'Firefox';
        $version = $matches[1];
    } elseif (preg_match('/MSIE ([0-9\.]+)/i', $userAgent, $matches)) {
        $browser = 'Internet Explorer';
        $version = $matches[1];
    } elseif (preg_match('/Safari\/([0-9\.]+)/i', $userAgent) && preg_match('/Version\/([0-9\.]+)/i', $userAgent, $matches)) {
        $browser = 'Safari';
        $version = $matches[1];
    }

    return [
        'browser' => $browser,
        'version' => $version,
        'OS' => $os
    ];
}

/**
 * To safely encrypt and decrypt data using OpenSSL
 * @param string $data (required) The data to encrypt
 * @param bool|string $secret_key (optional) The secret key to use for encryption
 *
 * @return string $encrypted The encrypted data
 */
function openEncrypt(string $data, bool|string $secret_key = false): string
{
    $key = defined('TAOH_ENCRYPTION_KEY')
        ? TAOH_ENCRYPTION_KEY
        : '75d8adb94bab9c160da599b3061cfa340f4e8c054fccfa631bc8cb73a024a0c5';
    if (!empty($secret_key)) $key .= $secret_key;

    $cipher = "AES-256-CBC";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);

    $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $encrypted, $key, true);

    // Combine IV, HMAC, and ciphertext for storage/transmission
    return base64_encode($iv . $hmac . $encrypted);
}

/**
 * To safely decrypt data using OpenSSL
 * @param string $data (required) The encrypted data to decrypt
 * @param bool|string $secret_key (optional) The secret key to use for decryption
 *
 * @return string|bool $decrypted The decrypted data or false on failure
 */
function openDecrypt(string $data, bool|string $secret_key = false): bool|string
{
    $key = defined('TAOH_ENCRYPTION_KEY')
        ? TAOH_ENCRYPTION_KEY
        : '75d8adb94bab9c160da599b3061cfa340f4e8c054fccfa631bc8cb73a024a0c5';
    if (!empty($secret_key)) $key .= $secret_key;

    $cipher = "AES-256-CBC";
    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);

    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, 32); // 256-bit HMAC = 32 bytes
    $ciphertext = substr($c, $ivlen + 32);

    $calculated_hmac = hash_hmac('sha256', $ciphertext, $key, true);

    // Timing attack safe comparison
    if (!hash_equals($hmac, $calculated_hmac)) {
        return false; // HMAC validation failed
    }

    return openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}

function get_live_now_data() {
    return file_get_contents(TAOH_LIVE_NOW_URL.'?y='.gmdate("YmdH"));
}

/**
 * Parse YmdHis -> DateTimeImmutable in given zone
 * (interprets as local "wall time")
 */
function makeZonedInstant(string $ymdHis, string $tz = 'UTC'): DateTimeImmutable {
    $z = new DateTimeZone($tz);
    return DateTimeImmutable::createFromFormat('YmdHis', $ymdHis, $z);
}

/**
 * Get timezone abbreviation (DST aware)
 */
function getTzAbbreviation(DateTimeImmutable $dt, ?string $tzid = null): string {
    // Use PHP's format('T') to get abbreviation (e.g., "EDT", "PST", "+04")
    $abbr = $dt->format('T');

    // If PHP already gives a valid abbreviation (like "EDT", "PST"), use it directly
    if (preg_match('/^[A-Z]{2,5}$/', $abbr)) {
        return $abbr;
    }

    // Determine the timezone ID from the input DateTime if not provided
    $tzid = $tzid ?: $dt->getTimezone()->getName();

    // Timezone abbreviation map
    static $map = [
        // Americas
        'America/Havana' => ['standard' => 'CST', 'daylight' => 'CDT'],
        'America/Los_Angeles' => ['standard' => 'PST', 'daylight' => 'PDT'],
        'America/Denver' => ['standard' => 'MST', 'daylight' => 'MDT'],
        'America/Chicago' => ['standard' => 'CST', 'daylight' => 'CDT'],
        'America/New_York' => ['standard' => 'EST', 'daylight' => 'EDT'],
        'America/Toronto' => ['standard' => 'EST', 'daylight' => 'EDT'],
        'America/Phoenix' => ['standard' => 'MST'],  // No DST
        'America/Sao_Paulo' => ['standard' => 'BRT'],  // No DST
        'America/Bogota' => ['standard' => 'COT'],  // No DST
        'America/Caracas' => ['standard' => 'VET'],  // No DST
        'America/Lima' => ['standard' => 'PET'],  // No DST
        'America/Vancouver' => ['standard' => 'PST'],  // No DST
        'America/Argentina/Buenos_Aires' => ['standard' => 'ART'],  // No DST
        'America/Santiago' => ['standard' => 'CLT'],  // No DST

        // Europe
        'Europe/London' => ['standard' => 'GMT', 'daylight' => 'BST'],
        'Europe/Paris' => ['standard' => 'CET', 'daylight' => 'CEST'],
        'Europe/Berlin' => ['standard' => 'CET', 'daylight' => 'CEST'],
        'Europe/Moscow' => ['standard' => 'MSK'],  // No DST
        'Europe/Istanbul' => ['standard' => 'TRT'],  // No DST

        // Asia
        'Asia/Dubai' => ['standard' => 'GST'],  // No DST
        'Asia/Karachi' => ['standard' => 'PKT'],  // No DST
        'Asia/Kolkata' => ['standard' => 'IST'],  // No DST
        'Asia/Calcutta' => ['standard' => 'IST'],  // No DST
        'Asia/Dhaka' => ['standard' => 'BST'],  // No DST
        'Asia/Bangkok' => ['standard' => 'ICT'],  // No DST
        'Asia/Singapore' => ['standard' => 'SGT'],  // No DST
        'Asia/Shanghai' => ['standard' => 'CST'],  // No DST
        'Asia/Tokyo' => ['standard' => 'JST'],  // No DST
        'Asia/Seoul' => ['standard' => 'KST'],  // No DST

        // Australia
        'Australia/Perth' => ['standard' => 'AWST'],  // No DST
        'Australia/Adelaide' => ['standard' => 'ACST', 'daylight' => 'ACDT'],
        'Australia/Sydney' => ['standard' => 'AEST', 'daylight' => 'AEDT'],
        'Pacific/Auckland' => ['standard' => 'NZST', 'daylight' => 'NZDT'],
        'Pacific/Fiji' => ['standard' => 'FJT'],  // No DST
        'Australia/Melbourne' => ['standard' => 'AEST'],  // No DST
        'Australia/Brisbane' => ['standard' => 'AEST'],  // No DST

        // UTC Zones
        'Etc/UTC' => ['standard' => 'UTC'],  // No DST
        'UTC' => ['standard' => 'UTC'],  // No DST
        'GMT' => ['standard' => 'GMT'],  // No DST
    ];

    // Check if the timezone exists in the map
    if (isset($map[$tzid])) {
        // Check if DST is active (format 'I' returns 1 for DST, 0 for non-DST)
        $currentAbbr = $dt->format('I') ? 'daylight' : 'standard';
        return $map[$tzid][$currentAbbr] ?? $map[$tzid]['standard'];
    }

    // Fallback: show a clear UTC offset if not found in the map
    return 'UTC' . $dt->format('P'); // e.g. "UTC+04:00"
}

/**
 * Same-day detector (local to the zone)
 */
function isSameLocalDay(string $startYmdHis, string $endYmdHis, string $tz = 'UTC'): bool {
    $z = new DateTimeZone($tz);
    $f = DateTimeImmutable::createFromFormat('YmdHis', $startYmdHis, $z);
    $t = DateTimeImmutable::createFromFormat('YmdHis', $endYmdHis, $z);
    return $f->format('Y-m-d') === $t->format('Y-m-d');
}

/**
 * Localized event data (global vs local) same as JS
 */
function get_localized_event_data(array $event_timestamp_data, string $user_timezone = 'UTC'): array
{
    $fmt = 'YmdHis'; // yyyyMMddHHmmss

    $locality = isset($event_timestamp_data['locality'])
        ? (int)$event_timestamp_data['locality']
        : 0;

    try {
        if ($locality === 1) {
            // Global event: interpret utc_datetime as a wall time in user's timezone (matches your JS)
            $userTz = new DateTimeZone($user_timezone);
            $dt = DateTimeImmutable::createFromFormat(
                $fmt,
                (string)($event_timestamp_data['utc_datetime'] ?? ''),
                $userTz
            );
            if ($dt === false) {
                throw new RuntimeException('Invalid utc_datetime');
            }
            return [
                'datetime' => $dt->format($fmt),
                'timezone' => $user_timezone,
            ];
        } else {
            // Local event: parse in event timezone, then convert to user's timezone
            $event_tz_str = $event_timestamp_data['timezone'] ?? 'UTC';
            $eventTz = new DateTimeZone($event_tz_str);
            $dt = DateTimeImmutable::createFromFormat(
                $fmt,
                (string)($event_timestamp_data['local_datetime'] ?? ''),
                $eventTz
            );
            if ($dt === false) {
                throw new RuntimeException('Invalid local_datetime');
            }
            $localized = $dt->setTimezone(new DateTimeZone($user_timezone));
            return [
                'datetime' => $localized->format($fmt),
                'timezone' => $user_timezone,
            ];
        }
    } catch (Throwable $e) {
        // Fallback with minimal info if parsing fails
        return [
            'datetime' => null,
            'timezone' => $user_timezone,
            'error'    => $e->getMessage(),
        ];
    }
}

/**
 * Beautify Time
 * fmtStr tokens: {week}, {year}, {month}, {day}, {time}, {abbr}
 * options: ['weekdayStyle'=>'short|long', 'monthStyle'=>'short|long']
 */
function beautifyTime(
    string $ymdHis,
    string $timeZone = 'UTC',
    string $fmtStr = '{week}, {day} {month} {year} - {time} {abbr}',
    array $options = ['weekdayStyle'=>'short','monthStyle'=>'short']
): string {
    $dt = makeZonedInstant($ymdHis, $timeZone);

    // Build PHP date format dynamically
    $weekdayFmt = ($options['weekdayStyle'] ?? 'short') === 'long' ? 'l' : 'D';
    $monthFmt   = ($options['monthStyle'] ?? 'short') === 'long' ? 'F' : 'M';

    $map = [
        '{week}' => $dt->format($weekdayFmt),
        '{year}' => $dt->format('Y'),
        '{month}' => $dt->format($monthFmt),
        '{day}' => ltrim($dt->format('j'), '0'),
        '{time}' => $dt->format('g:i A'),
        '{abbr}' => getTzAbbreviation($dt),
    ];

    return strtr($fmtStr, $map);
}

function formatEventDateTime($event_start_ymd, $event_ends_ymd)
{
    if (isSameLocalDay($event_start_ymd['datetime'], $event_ends_ymd['datetime'], $event_start_ymd['timezone'])) {
        $f = beautifyTime($event_start_ymd['datetime'], $event_start_ymd['timezone'], '{week}, {month} {day}, {year} - {time}');
        $t = beautifyTime($event_ends_ymd['datetime'], $event_ends_ymd['timezone'], '{time} {abbr}');
    } else {
        $f = beautifyTime($event_start_ymd['datetime'], $event_start_ymd['timezone'], '{week}, {month} {day}, {year}, {time} {abbr}');
        $t = beautifyTime($event_ends_ymd['datetime'], $event_ends_ymd['timezone'], '{week}, {month} {day}, {year}, {time} {abbr}');
    }
    return $f . ' - ' . $t;
}

function createRoomInfo($room_data, $keyword, $ptoken = '', $data = array())
{
    if (empty($ptoken)) $ptoken = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'detail';

    $response = array('output' => false, 'success' => false);
    //echo "==========". json_encode($room_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    //die();
    $taoh_vals = array(
        'ops' => 'status',
        'status' => 'postroom',
        'code' => TAOH_OPS_CODE,
        'key' => $ptoken,
        'keyslug' => $room_data['room']['keyslug'],
        'keyword' => $keyword,
        'type' => $type,
        'value' => addslashes(json_encode($room_data))
    );

    if (isset($room_data['room']['room_type']) && !empty($room_data['room']['room_type'])) {
        $taoh_vals['app'] = $room_data['room']['room_type'];
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);exit();

    $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $room_info_arr = json_decode($room_data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'invalid_json_response';
        $response['message'] = 'Invalid JSON response: ' . json_last_error_msg();
        return $response;
    }

    if ($room_info_arr['success'] && $room_info_arr['output']) {
        $response['output'] = $room_info_arr['output'];
        $response['success'] = $room_info_arr['success'];
    }

    $cache_dir = TAOH_PLUGIN_PATH . '/cache/general/rooms/';
    $cache_file_name = 'room_info_' . $taoh_vals['keyslug'] . '_' . $taoh_vals['type'] . '.cache'; //  . (isset($taoh_vals['app']) ? '_' . $taoh_vals['app'] : '')
    remove_cache_file($cache_dir, [$cache_file_name]);

    return $response;
}

function updateRoomInfo($room_data, $ptoken = '', $data = array())
{
    if (empty($ptoken)) $ptoken = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'detail';

    $response = array('output' => false, 'success' => false);

    $taoh_vals = array(
        'ops' => 'status',
        'status' => 'postroom',
        'code' => TAOH_OPS_CODE,
        'key' => $ptoken,
        'keyslug' => $room_data['room']['keyslug'],
        'type' => $type,
        'value' => addslashes(json_encode($room_data)),
        //'debug' => 1
    );

    if (isset($room_data['room']['room_type']) && !empty($room_data['room']['room_type'])) {
        $taoh_vals['app'] = $room_data['room']['room_type'];
    }

    $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $room_info_arr = json_decode($room_data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'invalid_json_response';
        $response['message'] = 'Invalid JSON response: ' . json_last_error_msg();
        return $response;
    }

    if ($room_info_arr['success'] && $room_info_arr['output']) {
        $response['output'] = $room_info_arr['output'];
        $response['success'] = $room_info_arr['success'];
    }

    $cache_dir = TAOH_PLUGIN_PATH . '/cache/general/rooms/';
    $cache_file_name = 'room_info_' . $taoh_vals['keyslug'] . '_' . $taoh_vals['type'] . '.cache'; //  . (isset($taoh_vals['app']) ? '_' . $taoh_vals['app'] : '')
    remove_cache_file($cache_dir, [$cache_file_name]);

    return $response;
}

function getRoomInfo($keyslug, $ptoken = '', $data = array())
{
    if (empty($ptoken)) $ptoken = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'detail';

    $response = array('output' => false, 'success' => false);

    $taoh_vals = array(
        'ops' => 'status',
        'status' => 'getroom',
        'code' => TAOH_OPS_CODE,
        'key' => $ptoken,
        'keyslug' => $keyslug,
        'type' => $type,
        //'debug'=>1
    );

    if (isset($data['room']['room_type']) && !empty($data['room']['room_type'])) {
        $taoh_vals['app'] = $data['room_type'];
    }

    $cache_dir = TAOH_PLUGIN_PATH . '/cache/general/rooms/';
    $cache_file = $cache_dir . 'room_info_' . $taoh_vals['keyslug'] . '_' . $taoh_vals['type'] . '.cache'; //  . (isset($taoh_vals['app']) ? '_' . $taoh_vals['app'] : '')

    $is_cache_response = false;
    if (is_cache_valid($cache_file, 120 * 60)) { // 2 hours
        $room_data_json = read_cache_file($cache_file);
        $is_cache_response = true;
    } else {
//        $taoh_vals['debug'] = 1;echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);exit();
        $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    }
    if ($is_cache_response === false) {
        // To update filetime on each call comment is_cache_response condition
        $room_info_arr = json_decode($room_data_json, true);
        if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
            update_cache_file($cache_file, $room_data_json);
        }
    }

    /*$room_info_arr = json_decode($room_data_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['error'] = 'invalid_json_response';
        $response['message'] = 'Invalid JSON response: ' . json_last_error_msg();
        return $response;
    }

    if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
        $response['output'] = $room_info_arr['output'];
        $response['success'] = $room_info_arr['success'];
    }

    return $response;*/

    return $room_data_json;
}

function encrypt_url_safe($data)
{
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', "taoh-secret-key", OPENSSL_RAW_DATA, $iv);
    return bin2hex($iv . $encrypted);
}

function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $requestUri = $_SERVER['REQUEST_URI'];
    return $protocol . $host . $requestUri;
}