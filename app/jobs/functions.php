<?php

function taoh_get_info( $conttoken, $type = 'content' ){
	if ( ! ctype_alnum( $conttoken ) ) { taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG );taoh_exit(); }

	$taoh_call = 'jobs.job.get';
	$taoh_call_type = 'get';
	$taoh_vals = array(
		"mod" => 'jobs',
		'token' => taoh_get_dummy_token(1),
		//'cfcc5h'=> 1, ////cfcache newly added
		//"ops" => "info",
	);
	$cache_name = taoh_p2us('jobs.job').'_'. $conttoken.'_'.$type;
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
	$key = taoh_scope_key_encode( serialize( $taoh_vals ), 'global' );
	//$taoh_vals[ 'cache' ] = array ( "name" => 'jobs_job_'. $key, "ttl" => 3600);
	//$taoh_vals[ 'cfcache' ] = 'jobs_job_'. $key;
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
		"mod" => 'jobs',
		'token' => taoh_get_dummy_token(),
		"app" => $current_app,
		"q" => $recipe,
	);
	$result = taoh_apicall_post($taoh_call,  $taoh_vals);

	$return = json_decode($result, true);
	return $return;
}

?>
