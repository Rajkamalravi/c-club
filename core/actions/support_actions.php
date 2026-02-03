<?php
/* if ( ! taoh_user_is_logged_in() ){
	$subject = "HiresQuery";
	if (isset($_GET['q'])) $subject = "$subject, from ".$_GET['q'];
	header("Location: mailto:info@tao.ai?subject=$subject");
  taoh_exit();
} */
//echo '--------';die;
//print_r($_POST);
$cache_file = TAOH_PLUGIN_PATH.'/cache/logs/'.TAOH_API_SECRET.'.cache';
$file = file_get_contents($cache_file);
if($file != ''){
    $temparray = explode('a:7:',$file);
}
$url_arr = [];
$newarr = [];
foreach($temparray as $value){
    if(str_contains($value,TAOH_API_TOKEN)){
        $array = explode('s:4:"misc";s:',$value);
        if(isset($array[1])){
           // $url_val = str_replace('"','',$array[1]);
            $url_val = str_replace('";}','',$array[1]) ; 
        }
        $finallarray = explode(':"',$url_val);
        if(isset($finallarray[1])){
            //echo "<br>".$finallarray[1];
            $url_arr[] = $finallarray[1];
        }
        $newarr[] = $value;
        //echo "<pre>";print_r($array);echo "<\pre>";
    }
}
$total = count($url_arr) ;
//echo $total;
if($total > 20){
    $offset = $total - 20;
    //echo $offset;
    $logarr = array_slice($url_arr,  $offset, 20);

}else{
    $logarr = $url_arr;
}
//print_r($_POST);
$backurl = $_SESSION["history"];
if ( is_array($_POST) ){
    $ptoken = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
    $name = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->fname.' '.taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->lname;
    $email = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->email;
    /* Get Redis ID by sandy */
    $vals = array(
        'POST' => $_POST,
        'GET' => $_GET,
        'SESSION' => $_SESSION,
        'COOKIE' => $_COOKIE,
    );
    $time = time();
    $vals = json_encode($vals);
    $post_data = array(
        'code' => 'tc2asi3iida2',
        'ops' => 'post',
        'value' => $vals,
        'key' => 'support_'.$ptoken.'_'.$time,
        'ttl' => 2*24*60*60,
    );
    $result = taoh_post( TAOH_CACHE_CHAT_PROC_URL , $post_data);
    $result = json_decode($result,true);
    //echo $result;die();
    /* Get Redis ID by sandy */
    if($result['success'] && $result['output']){
        $post = array(
            'name' =>  $name,
            'email' =>  $email,
            'message' =>  $_POST['we_message'].'<br><br>The key is: '.$post_data['key'],
            'LASTLOGS' => json_encode($logarr),
            'LASTWEBSITE' => $_SESSION["history"],
            'PTOKEN' => $ptoken
        );
        //print_r($post);
        $postdata = array(
            'mod' => 'tao_tao',
            'token'=>TAOH_API_TOKEN,
            'type' => 'support',
            'ops' => 'send',
            'toenter' => $post      
        );
        //print_r($postdata);
        $cmd = "core.post";
        //echo taoh_apicall_get_debug( $cmd,  $postdata );die();
        $result = taoh_apicall_post($cmd, $postdata);
        taoh_set_success_message("Thank you for contacting us. We will respond to you within 48 hours.");
        taoh_redirect($backurl);
        taoh_exit();
    }else{
        taoh_set_error_message("Something went wrong. Please try again.");
        taoh_redirect($backurl);
        taoh_exit();
    }
}
?>
