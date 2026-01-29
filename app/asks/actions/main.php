
<?php //add answer

die('--------------');
if($_POST['action'] == "addanswer") {
    return add_answer();
}


function add_answer() {
	$error = 0;

	if ( taoh_user_is_logged_in() ){
		$taoh_call = 'asks.ask.post';
		$taoh_call_type = 'POST';
        $taoh_vals = array(
            'mod' => 'asks',
            'token' => taoh_get_dummy_token(),
            'toenter' => $_POST,
            'ops' => 'answer',
            //'cache' => array( array( taoh_p2us("asks.ask"), $_POST[ 'conttoken' ]), array( taoh_p2us("asks.get") ) ),
        );
		//taoh_apicall_post_debug($taoh_call, $taoh_vals);die();
		$result = taoh_apicall_post($taoh_call, $taoh_vals);
		//echo $result;exit();

        taoh_set_success_message("Answer has been successfully received");
        header("Location: ".TAOH_SITE_URL_ROOT."/asks/d/".$_POST['slug']);
	}
}