<?php
//this file not in use - we can delete it
//if ( isset( $_POST[ 'ptoken' ] ) ){
	
    $taoh_call = 'core.content.post';
    $taoh_call_type = 'POST';
    $taoh_vals = array(
        'token'=>taoh_get_dummy_token(),
        'ptoken'=>$_POST['ptoken'],
        'mod' => 'tao_tao',
        'ops' => 'add',
        'type' => 'feedback',
        'toenter' => $_POST,
    );
    //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die();
   // $result = taoh_apicall_post($taoh_call, $taoh_vals);
    $return = json_decode($result, true);
    if (isset($return['success']) && $return['success']) {
        taoh_set_success_message("Thank you so much for your time to give us feedback. Your involvement is not just valued, 

        It's vital in shaping a future where every worker can thrive in their career journey.");
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }

    
//}
?>