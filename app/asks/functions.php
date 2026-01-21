<?php
function taoh_get_info( $conttoken, $type = 'content' ){
	$taoh_call = 'asks.ask.get';
	$taoh_call_type = 'get' ;
	$taoh_vals = array(
		"mod" => 'asks',
		'token' => taoh_get_dummy_token(1),
		//'cfcc5h'=> 1, //cfcache newly added
		//"ops" => "info",
	);

	if ( $type == 'content' ){
		$taoh_vals[ 'conttoken' ] = $conttoken;
	} else {
		$title = "skill";
		$type2 = $type;
	  if( $type == "rolechat") {
	    $type2 = "title";
		} else if( $type == "skillchat") {
	      $type2 = "skill";
			} else if( $type == "orgchat") {
		      $type2 = "company";
	  }
		$taoh_vals[ 'title' ] = $conttoken;
		$taoh_vals[ 'type' ] = $type2;
	}
	$cache_name = taoh_p2us('asks.ask').'_'. $conttoken.'_'.$type2;
	//$taoh_vals[ 'cache' ] = array ( "name" => $cache_name,  "ttl" => 7200);
	//$taoh_vals[ 'cfcache' ] = $cache_name;
	ksort($taoh_vals);
	$return = taoh_apicall_get( $taoh_call, $taoh_vals );
	$return = json_decode($return, true);
	return $return;
}

function taoh_chat_clubkey_post( $taoh_id, $current_app, $conttype, $request_type ){
	$taoh_call = 'chat.club.post';
	$taoh_call_type = 'post' ;
	$recipe = "type:$conttype::q:$taoh_id::user:n"; //company/skill/role=title
	$taoh_vals = array(
		"mod" => 'ask',
		'token' => taoh_get_dummy_token(),
		"app" => $current_app,
		"q" => $recipe,
	);
	$result = taoh_apicall_post($taoh_call,  $taoh_vals);
	$return = json_decode($result, true);
	return $return;
}

 ?>
