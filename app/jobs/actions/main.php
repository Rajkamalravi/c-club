
<?php //add answer


if($_POST['action'] == "addcomments") {
    return add_comments();
}elseif($_POST['ops'] == "apply"){
	return apply_job();
}


function add_comments() {
	$error = 0;

	//print_r($_POST);die();

	if ( taoh_user_is_logged_in() ){

		$taoh_call = 'jobs.job.post';
		$taoh_call_type = 'POST';
        $taoh_vals = array(
            'mod' => 'asks',
            'token' => taoh_get_dummy_token(),
            'toenter' => $_POST,
            'ops' => 'answer',
        );

		$result = taoh_apicall_post($taoh_call, $taoh_vals);

        taoh_set_success_message("Comments been successfully recevied!");
        header("Location: ".TAOH_SITE_URL_ROOT."/jobs/d/".$_POST['slug']);
	}
}

function apply_job() {
	$error = 0;
	//echo '<PRE>'; print_r($_POST);die();
	$taoh_call = 'jobs.put.apply';
    $taoh_vals = array(
        'mod' => 'jobs',
        'token' => taoh_get_dummy_token(),
        'toenter' => $_POST,
        'ops' => $_POST['ops'],
        //'cache' => array( array( taoh_p2us("jobs.put.apply"), $_POST['conttoken'] ) ),
    );

	if(isset($_POST['enable_scout_apply']) && $_POST['enable_scout_apply'] ==1){
		$taoh_call = 'jobs.scout.job.post';
		$taoh_vals['type'] = 'applicant';
		$taoh_vals['conttoken'] = $_POST['conttoken'];
		unset($_POST['enable_scout_apply']);
	}
	//$taoh_vals['debug'] = 1;

	if ( taoh_user_is_logged_in() ){


		//echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die;
		$data = taoh_apicall_post($taoh_call, $taoh_vals);
		//	print_r($data);die();
		header('Content-Type: application/json; charset=utf-8');
		$results = trim($data);
  		echo $results;die;

		/* taoh_set_success_message("Applied Your Job Successfully!!!");
		header("Location: ".TAOH_SITE_URL_ROOT."/jobs/d".$_POST['slug']); */
	}
}