<?php

if(!TAOH_REFER_ENABLE){
    taoh_redirect(TAOH_SITE_URL_ROOT);
    taoh_exit();
}
if(isset($_GET['already_refered']) && $_GET['already_refered'] == 1){


    if(!taoh_user_is_logged_in() ) {
        //
        //die('=====11111111======'.TAOH_LOGIN_URL);
        taoh_redirect( TAOH_LOGIN_URL );
        taoh_exit();
    }
    else if(defined( 'TAOH_API_TOKEN' ) && defined('TAOH_SETTINGS_URL') && TAOH_API_TOKEN ){
        $user_data = taoh_user_all_info();
        //die('=====222222222======');
        //print_r($user_data);exit();
        //if ( ! isset( $user_data->fname ) && ! isset( $user_data->type )  && ! isset( $user_data->chat_name ) && defined( 'TAOH_API_TOKEN' ) && defined('TAOH_SETTINGS_URL') && TAOH_API_TOKEN ) {
        if (  ! isset( $user_data->type )  && ! isset( $user_data->chat_name )) {
            taoh_redirect(TAOH_SITE_URL_ROOT.'/createacc');
            taoh_exit();
        }
        else{

           //die('----xxxxxxxx---');
            if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'] != ''){
            $url = $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'];

            delete_refer_token();

            taoh_redirect($url); exit();
            }
            else{
                taoh_redirect(TAOH_SITE_URL_ROOT );
                taoh_exit();

            }
        }
    }
    else{
        die('I am struck in refer page');
    }

}
else if(isset($_GET['ignore']) && $_GET['ignore'] == 1){

    //https://ppapi.tao.ai/core.refer.ignore?secret=DVrIy1cu&refer_token=6jeosfky
    $refer_token = $_GET['refer_token'];
    $taoh_call = "core.refer.ignore";
    $taoh_vals = array(
        "mod" => "referal",
        'refer_token'=>$refer_token,
        'secret' => TAOH_API_SECRET,

    );
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );exit();
    $res = taoh_apicall_get( $taoh_call, $taoh_vals );
    taoh_redirect(TAOH_SITE_URL_ROOT );
    taoh_exit();

}
//START - kalpana worked for referral redirect and cookie handlings
else if(isset($_GET['refer_token']) && $_GET['refer_token']!=''){
    $refer_token = $_GET['refer_token'];


    //echo "====".$refer_token;
    //error_reporting(E_ALL);
    $taoh_call = "core.refer.get";
    $taoh_vals = array(
        "mod" => "referal",
        'refer_id'=>$refer_token,
        'refer_token'=>$refer_token,
        'secret' => TAOH_API_SECRET,
        // 'cfcc5h' => 1 //cfcache newly added

    );
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );exit();
    $res = taoh_apicall_get( $taoh_call, $taoh_vals );
    $referral_data = json_decode($res, true);
    if($referral_data['success']){


        $data = $referral_data['output'][0]['misc'];

        $data = str_replace('"refer_data":"{"','"refer_data":{"',$data);
        $data = str_replace('"}","refer"','"},"refer"',$data);
        $data = str_replace('"}"}','"}}',$data);

    //  echo'<pre>====';print_r($data);echo'</pre>';
        $misc_data = json_decode($data,1);
      // echo'<pre>';print_r($misc_data);echo'';
      //  die();

     //   echo"============".$data;


         $link = $misc_data['refer_data']['to_link'];
       // echo"============".$link;
       // die();


        setcookie(TAOH_ROOT_PATH_HASH.'_'.'refer_token',$refer_token, strtotime( '+1 days' ), '/');
        setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',$link, strtotime( '+1 days' ), '/');
        setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_data',$data, strtotime( '+1 days' ), '/');
        setcookie(TAOH_ROOT_PATH_HASH.'_'.'from_referral',1, strtotime( '+1 days' ), '/');

        if (taoh_user_is_logged_in()) {
            $data = taoh_user_all_info();
            $profile_complete = (isset($data->profile_complete) && $data->profile_complete) ? $data->profile_complete : 0;
            if (!$profile_complete) {
                taoh_set_error_message('complete your settings to fully use the platform');
                taoh_redirect(TAOH_SITE_URL_ROOT . '/settings');
                taoh_exit();
            }


        }

        taoh_redirect($link );taoh_exit();
    }

}
else{
    taoh_redirect(TAOH_SITE_URL_ROOT );
        taoh_exit();
}

//END - kalpana worked for referral redirect and cookie handlings
