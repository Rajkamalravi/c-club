<?php

$cmd = taoh_parse_url(1);
$key = taoh_parse_url(2);
$current_app = TAOH_WERTUAL_SLUG;
$back_utl = TAOH_HOME_URL;
$subdomain = '';
if($_SERVER[ 'SERVER_NAME' ] == 'localhost'){
    $subdomain = '/dash';
}
?>
<script> 
    localStorage.removeItem('isCodeSent');
    localStorage.removeItem('email');
</script>
<?php
//echo 'cmd ======'.$cmd.'key========='.$key.'current_app========'.$current_app;die();
switch( $cmd ){


    case 'home':


            if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_temp_api_token'])){
                taoh_session_save(TAOH_ROOT_PATH_HASH, ['TAOH_API_TOKEN' => $_COOKIE[TAOH_ROOT_PATH_HASH.'_temp_api_token']]);
            }

            $back_url = TAOH_SITE_URL_ROOT.'/login';

            if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_locked']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_locked']){
                //echo "====back=====".$back_url;taoh_exit();
                taoh_redirect( $back_url ); taoh_exit();
            }
            else{
                
                //setcookie( TAOH_ROOT_PATH_HASH.'_taoh_api_token', $_COOKIE[TAOH_ROOT_PATH_HASH.'_temp_api_token'], strtotime( '+1 days' ),'/');
               // $email = $_COOKIE[TAOH_ROOT_PATH_HASH.'_tao_api_email'];
                call_login_referral_action();
                //taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();
            }

        break;
    case 'code':
        if(isset($key) && $key != ''){
            $access_code = taoh_parse_url(2);
            
            $app_data = taoh_app_info();
            //$forward_url = TAOH_SITE_URL_ROOT."/login_fwd/home";
            $forward_url = TAOH_SITE_URL_ROOT.'/login';
            $taoh_call = "account.temps";
            $taoh_vals = array(
            'secret'=>TAOH_API_SECRET,
            'mod'=>$app_data?->slug ?? '',
            'cmd'=>'create',
            'cache_required'=>0,
            'q'=>urlencode( $current_app.":::".$access_code ),
           // 'debug'=>1
            );
            $taoh_call_type = "get";
            //echo "====in code case=====".taoh_apicall_get_debug($taoh_call, $taoh_vals );die();
            $return = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );

            //echo "============".$forward_url;
           
            //echo'<pre>';print_r($return);die();

            if($return['success'] &&  isset( $return['output'] ) ) {
                $api_return_token = $return['output'];
                if(TAOH_SIMPLE_LOGIN && $return['is_new_user']){
                    setcookie( TAOH_ROOT_PATH_HASH.'_anonymous', 1, strtotime( '+1 days' ),'/');
                }
                
                setcookie( TAOH_ROOT_PATH_HASH."_temp_api_token", $return['output'], strtotime( '+1 days' ), '/'  );

                if(

                    (
                      isset($return['is_new_user']) && $return['is_new_user'] == 1
                    ) ||
                    (
                      isset($return['site_locked']) && $return['site_locked'] == 1 &&
                      isset($return['is_new_user']) && $return['is_new_user'] == 0 &&
                      isset($return['user_locked']) && $return['user_locked'] == 0
                    )

                  ){

                    if(TAOH_LOGIC_LOCK_CODE){
                        setcookie( TAOH_ROOT_PATH_HASH.'_enable_lock_screen', '1', strtotime( '+1 days' ),'/');
                        setcookie( TAOH_ROOT_PATH_HASH.'_locked', '1', strtotime( '+1 days' ),'/');

                        taoh_redirect($forward_url);
                        taoh_exit();

                    }
                    else{
                      //setcookie( TAOH_ROOT_PATH_HASH."_taoh_api_token", $return['output'], strtotime( '+30 days' ), '/'  );
                        //die('------------');
                        ?>
                        <script>
                        localStorage.removeItem('email');
                        localStorage.removeItem('isCodeSent');
                        </script>
                        <?php 
                        $_SESSION['referral_redirect'] = 'referral_redirect';
                        call_login_referral_action($return['output'],1);
                       // taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();
                    }

                  }

                  
                    ?>
                  <script>
                    localStorage.removeItem('email');
                     localStorage.removeItem('isCodeSent');
                </script>
                <?php
                //die('-----aaaaaa---------');
                //echo "===========".$return['output'];die();
                //setcookie( TAOH_ROOT_PATH_HASH."_taoh_api_token", $return['output'], strtotime( '+30 days' ), '/'  );
                $_SESSION['referral_redirect'] = 'referral_redirect';
                call_login_referral_action($return['output'],1);
               //taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();


            } else {
                //unset($_COOKIE);
                taoh_set_error_message('Invalid Code!');
                taoh_redirect(TAOH_SITE_URL_ROOT);
                taoh_exit();
            }

        }



    default: $fwdurl = ''; break;
}


?>