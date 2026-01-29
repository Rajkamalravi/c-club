<?php
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

//echo json_encode(['from' => 'ptoken', 'message' => 'This is some message', 'messageid' => 'MESSAGEID', 'parentid' => 0]);exit();

//echo "$mins :: $gap_prev : $gap : $gap_next";exit();

//exit();
// Check if the request method is POST
if ( ! function_exists('taoh_exit') ) {
	function taoh_exit(){
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request();
		}
		exit();
	}
}
if ( ! defined ( 'TAOH_OPSKEY' ) ) {
    define( 'TAOH_OPSKEY', 'tc2asi3iida2' );
}

// https://cache.tao.ai/index.php?code=tc2asi3iida2&key=somekey&value=somevalue&ops=get_mstamp
// https://cache.tao.ai/index.php?code=tc2asi3iida2&key=somekey&value=somevalue&ops=get_mstamp
// https://cache.tao.ai/index.php?code=tc2asi3iida2&key=somekey&value=somevalue&ops=get_mstamp&lastmtime=12002

$doit = 0;
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $cache_arr = $_GET;
    $doit = 1;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cache_arr = $_POST;
    $doit = 1;
}
function getMessageIdFromMessage($message) {
    // Implement logic to extract the message ID from the message
    // For example, if messages have IDs in the format "ID:12345", you can use a regex to extract it.
    preg_match('/ID:(\d+)/', $message, $matches);
    if (isset($matches[1])) {
        return (int)$matches[1];
    }
    return 0;
}

function subsetArray($array, $start, $stop) {
    // Handle negative values for start and stop
    if ($start < 0) {
        $start = max(0, count($array) + $start);
    }
    if ($stop < 0) {
        $stop = max(0, count($array) + $stop);
    }

    // Use array_slice to get the subset
    return array_slice($array, $start, $stop - $start + 1);
}


// ** REDIS Specific Function - Start ** //

function taoh_print( $return, $toprint = 1 ){
    if ( $toprint ){
        echo $return;
    }
    taoh_exit();
}

function taoh_redis_start(){
    $redis = new Redis();
    $redis->connect('localhost');
    return $redis;
}

function taoh_get( $cache_arr ){
    $redis = taoh_redis_start();
    if ( isset( $cache_arr[ 'key' ] ) && $cache_arr[ 'key' ] ){
        $value = $redis->get($cache_arr[ 'key' ]);
        $redis->close();
        if ( $value ){
            $return = json_encode(['output' => $value, 'success' => true]);
        } else {
            $return = json_encode([ 'success' => false]);
        }
    } else {
        $return = json_encode([ 'success' => false]);
    }
    return $return;
}

function taoh_set( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['value'])) {
        $redis = taoh_redis_start();
        $value = $cache_arr['value'];
        $key = $cache_arr['key'];
        $ttl = isset($cache_arr['ttl']) ? (int)$cache_arr['ttl'] : null;
        if ($ttl !== null) {
            // Set the key with TTL
            $redis->setex($key, $ttl, $value);
            $time = time();
            $redis->set($key.'_filemtime', $ttl, $time);
        } else {
            // Set the key without TTL
            $redis->set($key, $value);
            $time = time();
            $redis->set($key.'_filemtime', $time);
        }
        $return = json_encode(['output' => $time, 'success' => true]);
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }
    $redis->close();
    return $return;
}

function taoh_get_mstamp( $cache_arr ){
    $redis = taoh_redis_start();
    $value = $redis->get($cache_arr[ 'key' ].'_filemtime');
    $redis->close();
    if ( $value ) {
            $return = json_encode(['output' => $value, 'success' => true]);
    } else {
        $return = json_encode(['output' => $value, 'success' => false]);
    }
    return $return;
}

function taoh_lpush( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['value'])) {
        $redis = taoh_redis_start();
        $value = $cache_arr['value'];
        $key = $cache_arr['key'];
        $redis->LPUSH($key, $value);
        $time = time();
        $redis->set($key.'_filemtime', $time);
        $redis->close();
        $return = json_encode(['output' => $time, 'success' => true]);
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }

    return $return;
}

function taoh_lpushu( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['value'])) {
        $redis = taoh_redis_start();
        $value = $cache_arr['value'];
        $key = $cache_arr['key'];
        if ( ! is_numeric( $redis->LPOS($key, $value) )){
            $time = time();
            $redis->set($key.'_filemtime', $time);
            $redis->LPUSH($key, $value);
            if ( isset( $cache_arr[ 'ttl' ] ) && $cache_arr[ 'ttl' ] ){
                $redis->set($key.'_'.hash('crc32', $value ).'_filemtime', $time,  $cache_arr['ttl']);
            }
            $redis->close();
            $return = json_encode(['output' => $time, 'success' => true]);
        } else {
            $redis->close();
            $return = json_encode(['output' => 'not unique', 'success' => false]);
        }
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }

    return $return;
}

function taoh_lpos( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['value'])) {
        $redis = taoh_redis_start();
        $value = $cache_arr['value'];
        $key = $cache_arr['key'];
        $pos = $redis->LPOS($key, $value);
        if ( is_numeric( $pos ) ){
            if ( isset( $cache_arr[ 'ttl' ] ) && $cache_arr[ 'ttl' ] ){
                $value2 = $redis->get($key.'_'.hash('crc32', $value ).'_filemtime');
                if ( ! $value2 ){
                    $redis->LREM($key, $value);
                    $pos = false;
                }
            }
            $return =  json_encode(['output' => $pos, 'success' => true]);
        } else {
            $return =  json_encode(['output' => false, 'success' => false]);
        }
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }
    $redis->close();
    return $return;
}

function taoh_ldelete( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['value'])) {
        $redis = taoh_redis_start();
        $value = $cache_arr['value'];
        $key = $cache_arr['key'];
        // Set the key without TTL
        $pos = $redis->LREM($key, $value);
        if ( is_numeric( $pos ) ){
            $time = time();
            $redis->set($key.'_filemtime', $time);
            $return = json_encode(['output' => $pos, 'success' => true]);
        } else {
            $return = json_encode(['output' => false, 'success' => false]);
        }
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }
    $redis->close();
    return $return;
}

function taoh_lrange( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['key'])) {
        $redis = taoh_redis_start();
        $key = $cache_arr['key'];
        if ( isset( $cache_arr[ 'start' ] ) && $cache_arr[ 'start' ] ){
            $start = $cache_arr[ 'start' ];
        } else {
            $start = 0;
        }
        if ( isset( $cache_arr[ 'end' ] ) && $cache_arr[ 'end' ] ){
            $end = $cache_arr[ 'end' ];
        } else {
            $end = -1;
        }
        $out =  $redis->LRANGE($key, $start, $end);
        $redis->close();
        if ( $out ){
            $return = json_encode(['output' => $out, 'success' => true]);
        } else {
            $return = json_encode(['output' => false, 'success' => false]);
        }
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }
    return $return;
}

function taoh_latest( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['key'])) {
        $redis = taoh_redis_start();
        $key = $cache_arr['key'];
        $value = $redis->get($key.'_filemtime');
        $redis->close();
        if ( $value ) {
            if ( isset( $cache_arr[ 'lastmtime' ] ) && $cache_arr[ 'lastmtime' ] ){
                if ( $cache_arr[ 'lastmtime' ] < $value ){
                    $return = json_encode(['output' => $value, 'success' => true]);
                } else {
                    $return = json_encode(['output' => false, 'success' => true]);
                }
            } else {
                $return = json_encode(['output' => $value, 'success' => true]);
            }
        } else {
            $return = json_encode(['output' => $value, 'success' => false]);
        }
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }
    return $return;
}

function taoh_delete( $cache_arr ){
    // Check for 'value' and 'ttl' in POST data
    if (isset($cache_arr['key'])) {
        $redis = taoh_redis_start();
        $key = $cache_arr['key'];
        $redis->unlink($key);
        $redis->close();
        $return = json_encode(['output' => 'Key deleted', 'success' => true]);
    } else {
        $return = json_encode(['output' => 'Missing "value" for "set" operation', 'success' => false]);
    }
    return $return;
}

function taoh_append( $cache_arr ){
    $key = $cache_arr['key'];
    $redis = taoh_redis_start();
    $value = $redis->get( $key );
    if ( $value ){
        $value = $value.$cache_arr['value'];
        $ttl = isset($cache_arr['ttl']) ? (int)$cache_arr['ttl'] : null;
        if ($ttl !== null) {
            // Set the key with TTL
            $redis->setex($key, $ttl, $value);
            $time = time();
            $redis->set($key.'_filemtime', $ttl, $time);
        } else {
            // Set the key without TTL
            $redis->set($key, $value);
            $time = time();
            $redis->set($key.'_filemtime', $time);
        }
        $return = json_encode(['output' => $time, 'success' => true]);
    } else {
        $value = $cache_arr['value'];
        $ttl = isset($cache_arr['ttl']) ? (int)$cache_arr['ttl'] : null;
        if ($ttl !== null) {
            // Set the key with TTL
            $redis->setex($key, $ttl, $value);
            $time = time();
            $redis->set($key.'_filemtime', $ttl, $time);
        } else {
            // Set the key without TTL
            $redis->set($key, $value);
            $time = time();
            $redis->set($key.'_filemtime', $time);
        }
        $return = json_encode(['output' => $time, 'success' => true]);
    }
    $redis->close();
    return $return;

}

function taoh_keys( $cache_arr ){
    $key = $cache_arr['key'];
    // Perform a Redis DELETE operation
    if ( strlen( $key ) >= 10 ){
        $redis = taoh_redis_start();
        $value = $redis->keys($key.'*');
        $redis->close();
        //echo $value;
        if ($value) {
            $return = json_encode(['output' => $value, 'success' => true]);
        } else {
            $return = json_encode(['output' => 'Key not found', 'success' => true]);
        }
    } else {
        $return = json_encode(['output' => 'Key not found', 'success' => true]);
    }
    return $return;
}

function taoh_setadd( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $output = $redis->sadd($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_setcard( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $redis = taoh_redis_start();
    $output = $redis->scard($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}
function taoh_setremove( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $redis = taoh_redis_start();
    $output = $redis->srem($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}
function taoh_setlist( $cache_arr ){
    $key = $cache_arr['key'];
    $redis = taoh_redis_start();
    $output = $redis->smembers($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}
function taoh_setmember( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $redis = taoh_redis_start();
    $output = $redis->sismember($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_zadd( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $score = ( isset( $cache_arr['score'] ) && $cache_arr['score'] ) ? $cache_arr['score']:0;
    $options = ( isset( $cache_arr['options'] ) && $cache_arr['options'] ) ? $cache_arr['options']:'CH';
    $redis = taoh_redis_start();
    $output = $redis->zadd($key, '['.$options.']', $score, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_zrange( $cache_arr ){
    $key = $cache_arr['key'];
    $start = ( isset( $cache_arr['start'] ) && $cache_arr['start'] ) ? $cache_arr['start']:0;
    $stop = ( isset( $cache_arr['stop'] ) && $cache_arr['stop'] ) ? $cache_arr['stop']:-1;
    $redis = taoh_redis_start();
    $output = $redis->zrange($key, $start, $stop);
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_zrem( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $redis = taoh_redis_start();
    $output = $redis->zrem($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_zscan( $cache_arr ){
    $key = $cache_arr['key'];
    $redis = taoh_redis_start();
    $output = $redis->zscan($key, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_geoadd( $cache_arr ){
    $key = $cache_arr['key'];
    $value = $cache_arr['value'];
    $longitude = ( isset( $cache_arr['longitude'] ) && $cache_arr['longitude'] ) ? $cache_arr['longitude']:0;
    $latitude = ( isset( $cache_arr['latitude'] ) && $cache_arr['latitude'] ) ? $cache_arr['latitude']:0;
    $redis = taoh_redis_start();
    // $redis->geoAdd($key, $longitude, $latitude, $member [, $longitude, $latitude, $member, ...]);
    $output = $redis->geoadd($key, $longitude, $latitude, $value);
    $redis->close();
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_georadius( $cache_arr ){
    $key = $cache_arr['key'];
    $longitude = ( isset( $cache_arr['longitude'] ) && $cache_arr['longitude'] ) ? $cache_arr['longitude']:0;
    $latitude = ( isset( $cache_arr['latitude'] ) && $cache_arr['latitude'] ) ? $cache_arr['latitude']:0;
    $radius = ( isset( $cache_arr['radius'] ) && $cache_arr['radius'] ) ? $cache_arr['radius']:0;
    $unit = ( isset( $cache_arr['unit'] ) && $cache_arr['unit'] ) ? $cache_arr['unit']:'mi';
    $redis = taoh_redis_start();
    // $redis->geoAdd($key, $longitude, $latitude, $member [, $longitude, $latitude, $member, ...]);
    $output = $redis->georadius($key, $longitude, $latitude, $radius, $unit);
    $redis->close();
    if ( isset( $cache_arr[ 'search' ] ) && $cache_arr[ 'search' ] ){
         // Initialize an array to store matching results
        $matchingResults = [];
        foreach ($output as $member) {
            // If the value exists and contains the search term, add it to the results
            if ($member && strpos($member, $cache_arr[ 'search' ]) !== false) {
                $matchingResults[] = $member;
            }
        }
        $output = $matchingResults;

    }
    $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

function taoh_georadiusbymember( $cache_arr ){
    $key = $cache_arr['key'];
    $member = ( isset( $cache_arr['member'] ) && $cache_arr['member'] ) ? $cache_arr['member']:0;
    $radius = ( isset( $cache_arr['radius'] ) && $cache_arr['radius'] ) ? $cache_arr['radius']:0;
    $unit = ( isset( $cache_arr['unit'] ) && $cache_arr['unit'] ) ? $cache_arr['unit']:'mi';
    $redis = taoh_redis_start();
    // $redis->geoAdd($key, $longitude, $latitude, $member [, $longitude, $latitude, $member, ...]);
    $output = $redis->georadiusbymember($key, $member, $radius, $unit);
    $redis->close();
    if ( isset( $cache_arr[ 'search' ] ) && $cache_arr[ 'search' ] ){
        // Initialize an array to store matching results
       $matchingResults = [];
       foreach ($output as $member) {
           // If the value exists and contains the search term, add it to the results
           if ($member && strpos($member, $cache_arr[ 'search' ]) !== false) {
               $matchingResults[] = $member;
           }
       }
       $output = $matchingResults;
   }
   $return = json_encode(['output' => $output, 'success' => true]);
    return $return;
}

// ** REDIS Specific Function - End ** //


// ** HIRES Specific Function - Start ** //

function taoh_livepost( $cache_arr ){
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['value'] ) && $cache_arr['value'] ){
        $key = $cache_arr['key'];
        $redis = taoh_redis_start();
        $mins = date('m');
        $gap = floor($mins / 5);
        $gap_prev = ( $gap == 0 ) ? 11 : $gap - 1;
        $gap_next = ( $gap == 11 ) ? 0 : $gap + 1;
        $value = $cache_arr['value'];

        $redis->sadd($key."_".$gap, $value);
        $redis->sadd($key."_".$gap_next, $value);
        if ( $redis->scard($key."_".$gap_prev) ) $redis->unlink($key."_".$gap_prev);
        $redis->close();
        $return = json_encode(['output' => true, 'success' => true]);
    }
    return $return;
}

function taoh_livecheck( $cache_arr ){
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['value'] ) && $cache_arr['value'] ){
        $key = $cache_arr['key'];
        $redis = taoh_redis_start();
        $mins = date('m');
        $gap = floor($mins / 5);
        $value = $cache_arr['value'];
        $return = json_encode(['output' => $redis->sIsMember($key."_".$gap, $value), 'success' => true]);
        $redis->close();
    }
    return $return;
}

function taoh_roomguestadd( $cache_arr ){
    // longitude, latitude, key, ptype, ptoken, info, ops=roomguestadd
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['ptoken'] ) && isset( $cache_arr['info'] ) && $cache_arr['ptoken'] && $cache_arr['info'] ){
        $key = $cache_arr['key'];
        $value = $cache_arr['ptoken'];
        $longitude = ( isset( $cache_arr['longitude'] ) && $cache_arr['longitude'] ) ? $cache_arr['longitude']:0;
        $latitude = ( isset( $cache_arr['latitude'] ) && $cache_arr['latitude'] ) ? $cache_arr['latitude']:0;
        $cache_arr['ptype'] = ( isset( $cache_arr['ptype'] ) && $cache_arr['ptype'] ) ? $cache_arr['ptype']:'professional';
        $redis = taoh_redis_start();

        $all_room_key = "taoroom_".$key."_room_all";
        $type_room_key = "taoroom_".$key."_room_".$cache_arr['ptype'];
        $room_guest_set = "taoroom_".$key."_guest";

        $redis->geoadd($all_room_key, $longitude, $latitude, $cache_arr['ptoken']);
        $redis->geoadd($type_room_key, $longitude, $latitude, $cache_arr['ptoken']);
        if ( ! $redis->sIsMember( $room_guest_set, $cache_arr['ptoken'] ) ){
            $redis->sadd($room_guest_set, $cache_arr['ptoken']);

            $room_ptoken_info_key = "taoroom_".$key."_".$cache_arr['ptoken'];
            $redis->set($room_ptoken_info_key, $cache_arr['info']);
        }
        $redis->close();
        $return = json_encode(['output' => $output, 'success' => true]);
    }
    return $return;
}

function taoh_roomguestremove ( $cache_arr ){
    // key, ptype, ptoken, ops=roomguestremove
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['ptoken'] ) && $cache_arr['ptoken'] ){
        $key = $cache_arr['key'];
        $value = $cache_arr['ptoken'];
        $cache_arr['ptype'] = ( isset( $cache_arr['ptype'] ) && $cache_arr['ptype'] ) ? $cache_arr['ptype']:'professional';
        $redis = taoh_redis_start();

        $all_room_key = "taoroom_".$key."_room_all";
        $type_room_key = "taoroom_".$key."_room_".$cache_arr['ptype'];
        $room_guest_set = "taoroom_".$key."_guest";

        $redis->zrem($all_room_key, $cache_arr['ptoken']);
        $redis->zrem($type_room_key, $cache_arr['ptoken']);
        $redis->srem($room_guest_set, $cache_arr['ptoken']);
        $room_ptoken_info_key = "taoroom_".$key."_".$cache_arr['ptoken'];
        $redis->unlink($room_ptoken_info_key);
        $redis->close();
        $return = json_encode(['output' => $output, 'success' => true]);
    }
    return $return;
}

function taoh_roomguestliveorremove ( $cache_arr ){
    // ptoken, key, ptype, ops=roomguestliveorremove
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['ptoken'] ) && $cache_arr['ptoken'] ){
        $cache_arr[ 'value' ] = $cache_arr['ptoken'];
        $cache_arr['ptype'] = ( isset( $cache_arr['ptype'] ) && $cache_arr['ptype'] ) ? $cache_arr['ptype']:'professional';
        $key = $cache_arr['key'];
        //$cache_arr[ 'key' ] =  "taoroom_".$key."_live_".$cache_arr['ptype'];
        $cache_arr[ 'key' ] =  "taoroom_".$key."_live";
        $cache_arr[ 'value' ] = $cache_arr['ptoken'];
        $out_string = taoh_livecheck( $cache_arr );
        $out_arr = json_decode( $out_string, true );
        if ( $out_arr[ 'success' ] && ! $out_arr[ 'output' ] ){
            $output = taoh_roomguestremove ( $cache_arr );
            $return = json_encode(['output' => $output, 'success' => true]);
        }
    }
    return $return;
}

function taoh_roomadd ( $cache_arr ){
    // key, value ops=roomadd
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['value'] ) && $cache_arr['value'] ){

        $redis = taoh_redis_start();
        $room_guest_set = "taoroom_".$key;
        $redis->set($room_guest_set, $cache_arr['value']);
        $redis->close();
        $return = json_encode(['output' => $output, 'success' => true]);
    }
    return $return;
}

function taoh_roomguestliveoradd ( $cache_arr ){
    // longitude, latitude, key, search, ptype, ptoken, info, ops=roomguestliveoradd
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['ptoken'] ) && $cache_arr['ptoken'] ){
        $cache_arr[ 'value' ] = $cache_arr['ptoken'];
        $cache_arr['ptype'] = ( isset( $cache_arr['ptype'] ) && $cache_arr['ptype'] ) ? $cache_arr['ptype']:'professional';
        $key = $cache_arr['key'];
        //$cache_arr[ 'key' ] =  "taoroom_".$key."_live_".$cache_arr['ptype'];
        $cache_arr[ 'key' ] =  "taoroom_".$key."_live";
        $cache_arr[ 'value' ] = $cache_arr['ptoken'];
        $out_string = taoh_livecheck( $cache_arr );
        $out_arr = json_decode( $out_string, true );

        if ( $out_arr[ 'success' ] && ! $out_arr[ 'output' ] ){
            $cache_arr[ 'key' ] =  $key;
            $output = taoh_roomguestadd( $cache_arr );
            $return = json_encode(['output' => $output, 'success' => true]);
        }
    }
    return $return;
}

function taoh_roomguestuserfilter( $key, $ptoken_arr, $search ){
    $return = [];
    foreach( $ptoken_arr as $ptoken ){
        $live_key = "taoroom_".$key."_live";
        $live = taoh_livecheck( ['key' => $key, 'value' => $ptoken] );
        $live_arr = json_decode( $live, true );
        if ( $live[ 'success' ] && $live[ 'output' ] ){

            $room_guest_set = "taoroom_".$key."_".$value;
            $value = $redis->get($room_guest_set);
            if ( $search ){
                if ($value && strpos($value, $search) !== false) {
                    $return[$ptoken] = $value;
                }
            } else {
                $return[$ptoken] = $value;
            }
        }
    }
    return $return;
}

function taoh_roomguestsearch ( $cache_arr ){
    // key, longitude, latitude, ptype, search, radius, unit, start, end ops=roomguestsearch
    $return = json_encode(['output' => false, 'success' => false]);
    if ( isset( $cache_arr['search'] ) && $cache_arr['search'] ){
        $key = $cache_arr['key'];
        $cache_arr['ptype'] = ( isset( $cache_arr['ptype'] ) && $cache_arr['ptype'] ) ? $cache_arr['ptype']:'professional';
        $cache_arr[ 'key' ] =  "taoroom_".$key."_room_".$cache_arr['ptype'];
        $cache_arr[ 'value' ] = $cache_arr['search'];
        $out_string = taoh_georadius( $cache_arr );
        $out_arr = json_decode( $out_string, true );

        //$search_key = "taoroom_".$key."_live_".$cache_arr['ptype'];
        $search_key = "taoroom_".$key."_live";

        $out_arr = taoh_roomguestuserfilter( $key, $out_arr[ 'output' ], $cache_arr['search'] );
        $cache_arr[ 'start' ] =  ( isset( $cache_arr['start'] ) && $cache_arr['start'] ) ? $cache_arr['start']:0;
        $cache_arr[ 'end' ] =  ( isset( $cache_arr['end'] ) && $cache_arr['end'] ) ? $cache_arr['end']:-1;
        $out_arr = subsetArray($out_arr);

        if ( $out_arr[ 'success' ] && $out_arr[ 'output' ] ){
            $return = json_encode(['output' => $out_arr, 'success' => true]);
        }
    }
    return $return;
}

// ** HIRES Specific Function - End ** //

if ( $doit ) {
    //print_r($_GET);exit();
    header('Access-Control-Allow-Origin: *');
    if ( is_array($cache_arr) && isset( $cache_arr[ 'code' ] ) && isset( $cache_arr[ 'ops' ] ) && isset( $cache_arr[ 'key' ] ) && $cache_arr[ 'code' ] && $cache_arr[ 'code' ] && isset( $cache_arr[ 'ops' ] ) ){
        // Check if 'key' and 'ops' are present in the POST data
        if (isset($cache_arr['key'], $cache_arr['ops'])) {
            $key = $cache_arr['key'];
            $ops = $cache_arr['ops'];

            // Validate the 'md' field (MD5 hash of the 'key')
            if (isset($cache_arr['code']) && TAOH_OPSKEY === $cache_arr['code']) {
                // Connect to Redis server
//                $redis = new Redis();
//                $redis->connect('localhost', 6379);
                //$redis->connect('127.0.0.1', 7000);
//                $redis->connect('localhost');

                // Perform Redis operations based on 'ops'
                switch ($ops) {
                    case 'get':
                        $return = taoh_get( $cache_arr );
                        taoh_print( $return );
                        // Perform a Redis GET operation
                        break;
                    case 'get_mstamp':
                        $return = taoh_get_mstamp( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'set':
                        $return = taoh_set( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'lpush':
                        $return = taoh_lpush( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'lpushu':
                        $return = taoh_lpushu( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'lpos':
                        $return = taoh_lpos( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'ldelete':
                        $return = taoh_ldelete( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'lrange':
                        $return = taoh_lrange( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'latest':
                        $return = taoh_latest( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'delete':
                        $return = taoh_delete( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'append':
                        $return = taoh_append( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'keys':
                        $return = taoh_keys( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'setadd':
                        $return = taoh_setadd( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'setcard':
                        $return = taoh_setcard( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'setremove':
                        $return = taoh_setremove( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'setlist':
                        $return = taoh_setlist( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'setmember':
                        $return = taoh_setmember( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'zadd':
                        $return = taoh_zadd( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'zrange':
                        $return = taoh_zrange( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'zrem':
                        $return = taoh_zrem( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'zscan':
                        $return = taoh_zscan( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'geoadd':
                        $return = taoh_geoadd( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'georadius':
                        $return = taoh_georadius( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'georadiusbymember':
                        $return = taoh_georadiusbymember( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'livepost':
                        $return = taoh_livepost( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'livecheck':
                        $return = taoh_livecheck( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'roomguestadd':
                        // longitude, latitude, key, ptype, ptoken, info, ops=roomguestadd
                        $return = taoh_roomguestadd( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'roomadd':
                        // key, value ops=roomadd
                        $return = taoh_roomadd( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'roomguestremove':
                        // key, ptype, ptoken, ops=roomguestremove
                        $return = taoh_roomguestremove( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'roomguestliveorremove':
                        // ptoken, key, ptype, ops=roomguestliveorremove
                        $return = taoh_roomguestliveorremove( $cache_arr );
                        taoh_print( $return );
                        break;
                    case 'roomguestliveoradd':
                        // longitude, latitude, key, ptype, ptoken, info, ops=roomguestliveoradd
                        $return = taoh_roomguestliveoradd( $cache_arr );
                        taoh_print( $return );
                        break;

                    case 'roomguestsearch':
                        // key, longitude, latitude, ptype, search, radius, unit, ops=roomguestsearch
                        $return = taoh_roomguestsearch( $cache_arr );
                        taoh_print( $return );
                        break;


                    case 'keysnvalues':
                        // Perform a Redis DELETE operation
                        if ( strlen( $key ) >= 10 ){
                            $value = $redis->keys($key.'*');
                            //echo $value;
                            if ($value) {
                                $out = array();
                                print_r($value);exit();

                                foreach ( $value as $key => $val ){
                                    if ( ! stristr( $val, '_filemtime' ) ){
                                        $valtemp = $redis->get($val);
                                        $out[ $val ] = $valtemp;
                                        echo "Key: $key ; Value: $valtemp";
                                    }


                                }
                                print_r($out);exit();
                                if ( is_array($out) ){
                                    if ( isset( $cache_arr[ 'outkey' ] ) && $cache_arr[ 'outkey' ] ){
                                        $ttl = isset($cache_arr['ttl']) ? (int)$cache_arr['ttl'] : null;
                                        $value = json_encode($out);
                                        $key = $cache_arr[ 'outkey' ];
                                        if ($ttl !== null) {
                                            // Set the key with TTL
                                            $redis->setex($key, $ttl, $value);
                                            $time = time();
                                            $redis->set($key.'_filemtime', $ttl, $time);
                                        } else {
                                            // Set the key without TTL
                                            $redis->set($key, $value);
                                            $time = time();
                                            $redis->set($key.'_filemtime', $time);
                                        }
                                    }
                                    echo json_encode(['output' => $out, 'success' => true, 'timestamp' => $time]); taoh_exit();
                                } else {
                                    echo json_encode(['output' => 'Key not found', 'success' => true]);taoh_exit();
                                }

                            } else {
                                echo json_encode(['output' => 'Key not found', 'success' => true]);taoh_exit();
                            }
                        }
                        echo json_encode(['output' => 'Key not found', 'success' => true]);taoh_exit();
                        break;
                    case 'blanket':
                        // Perform a Redis DELETE operation

                        $key2 = ( ( isset( $cache_arr[ 'key2' ] ) && $cache_arr[ 'key2' ] ) ? $cache_arr[ 'key2' ]:'' );
                        if ( strlen( $key ) >= 10 ){
                            $value = $redis->keys($key.'*'.$key2);
                            //echo $value;
                            if ($value) {
                                echo json_encode(['output' => json_encode($value), 'success' => true]);
                            } else {
                                echo json_encode(['output' => 'Key not found', 'success' => true]);
                            }
                        }
                        echo json_encode(['output' => 'Key not found', 'success' => true]);
                        break;

                    case 'pubsubpublish':
                        if ( isset( $cache_arr['value'] ) && $cache_arr['value'] ){
                            $message = $cache_arr['value'];
                            $redis->publish($key, $message);
                            http_response_code(200);
                            echo json_encode(['output' => 'Message published successfully', 'success' => true]); taoh_exit();
                        }
                        echo json_encode(['output' => false, 'success' => false]); taoh_exit();
                        break;
                    case 'pubsubsubscribe':
                        $channel = $key; // Replace with your channel name

                        // Subscribe to the Redis channel
                        $redis->subscribe([$channel]);

                        // Continuously check for incoming messages and send them to the client
                        while (true) {
                            $message = $redis->psubscribeOnce(); // Wait for a message

                            // Respond with the received message
                            echo json_encode(['output' => $message, 'success' => true]) . "\n";
                            ob_flush();
                            flush(); // Flush output to send the message immediately

                            // You can add logic here to handle client disconnects and clean up
                        }
                        break;

                    case 'pubsubget':
                        $channel = $key; // Replace with your channel name

                        // Retrieve recent messages from the Redis channel
                        $recentMessages = $redis->lrange($channel, 0, -1);

                        // Close the Redis connection
                        $redis->close();

                        // Respond with the list of recent messages
                        http_response_code(200);
                        //echo json_encode(['messages' => $recentMessages]);
                        echo json_encode(['output' => $recentMessages, 'success' => true]);
                        break;
                    case 'pubsubnewmessage':
                            // Specify the channel to subscribe to
                            $channel = $key; // Replace with your channel name

                            // Get the last acknowledged message ID from the client (if provided)
                            $lastAcknowledgedMessageId = ( isset( $cache_arr[ 'value' ] ) && $cache_arr[ 'value' ] ) ? $cache_arr[ 'value' ]:0;

                            // Subscribe to the Redis channel
                            $redis->subscribe([$channel]);

                            // Continuously check for incoming messages and send them to the client
                            while (true) {
                                $message = $redis->psubscribeOnce(); // Wait for a message

                                // Check if the message ID is greater than the last acknowledged message ID
                                $messageId = getMessageIdFromMessage($message);
                                if ($messageId > $lastAcknowledgedMessageId) {
                                    // If the message is newer, send it to the client
                                    echo json_encode(['message' => $message]) . "\n";
                                    ob_flush();
                                    flush(); // Flush output to send the message immediately

                                    // Update the last acknowledged message ID for this client
                                    $lastAcknowledgedMessageId = $messageId;
                                }
                            }
                        break;
                    default:
                        echo json_encode(['output' => 'Invalid "ops" parameter', 'success' => false]);
                        break;
                }

                // Close the Redis connection

            } else {
                echo json_encode(['output' => 'Invalid "md" field', 'success' => false]);
            }
        } else {
            echo json_encode(['output' => 'Missing "key" or "ops" parameter', 'success' => false]);
        }
    } else {
        echo json_encode(['output' => 'Invalid request method', 'success' => false]);
    }
} else {
    echo json_encode(['output' => 'Invalid request method', 'success' => false]);
}
taoh_exit();
/*
function connectToRedis($host = '127.0.0.1', $port = 6379) {
    $redis = new Redis();
    $connected = $redis->connect($host, $port);

    if (!$connected) {
        throw new Exception("Failed to connect to Redis server at $host:$port");
    }

    return $redis;
}

// Example usage:
try {
    $redis = connectToRedis();
    $value = $redis->get('my_key');
    echo "Value for 'my_key': $value\n";
    $redis->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$redis->set($key, $value);
$value = $redis->get($key);

// ttl in seconds
$redis->setex($key, $ttl, $value);

$keyExists = $redis->exists($key);

$redis->del($key);

$redis->incr($key);      // Increment by 1
$redis->decr($key);      // Decrement by 1
$redis->incrby($key, 5); // Increment by 5
$redis->decrby($key, 3); // Decrement by 3
$keys = $redis->keys('my_*');
$redis = new Redis();

// Opens up the pipeline
$pipe = $redis->multi(Redis::PIPELINE);

// Loops through the data and performs actions
foreach ($users as $user_id => $username)
{
	// Increment the number of times the user has changed their username
	$pipe->incr('changes:' . $user_id);

	// Changes the username
	$pipe->set('user:' . $user_id . ':username', $username);
}

// Executes all of the commands in one shot
$pipe->exec();

*/


?>