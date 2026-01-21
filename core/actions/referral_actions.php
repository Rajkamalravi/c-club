<?php
if (isset($_POST)) {
    //print_r($_POST[ 'email' ]);
    $taoh_call = "core.referral.put";
    $taoh_call_type = "post";
    $taoh_vals = array(
        "mod" => "users",
        'token' => taoh_get_dummy_token(),
        "toenter" => $_POST,
        'time' => time(),
    );
    //$referral_code = (array) json_decode(taoh_apicall( $taoh_call, $taoh_call_type, $taoh_vals ));
    //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die;
    $referral_code = json_decode(taoh_apicall_post($taoh_call, $taoh_vals), true);
    taoh_set_success_message("Referral request for " . strtoupper($_POST['email']) . " added!");
    taoh_redirect(TAOH_REFERRAL_URL);
    taoh_exit();
}
?>