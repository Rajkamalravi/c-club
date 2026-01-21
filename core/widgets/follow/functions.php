<?php
function get_follow($conttoken, $type) {
    //$api = TAOH_SITE_CONTENT_GET . "?mod=core&ops=get&type=follow&conttoken=".$conttoken."&conttype=".$type."&token=".taoh_get_api_token();
    //$req = taoh_url_get_content($api);
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod'=>'core',
        'ops'=>'get',
        'type'=>'follow',
        'conttoken'=>$conttoken,
        'conttype'=>$type,
        'token'=>taoh_get_dummy_token(1),
        //'cfcc5h'=> 1, //cfcache newly added
      );
    //   $cache_name = $taoh_call.'_follow_' . hash('sha256', $taoh_call . serialize($taoh_vals));
    //   $taoh_vals[ 'cfcache' ] = $cache_name;
    //   $taoh_vals[ 'cache_name' ] = $cache_name;
      ksort($taoh_vals);
      $taoh_call_type = "get";
      $req = taoh_apicall_get($taoh_call, $taoh_vals);

      
    return json_decode($req);
}

//For Ajax
function do_follow()
{
    //$api = TAOH_SITE_CONTENT_GET . "?mod=core&ops=add&type=follow&conttoken=".$_POST['conttoken']."&conttype=".$_POST['conttype']."&token=".taoh_get_api_token();
    //$req = taoh_url_get_content($api);
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod' => 'core',
        'ops' => 'add',
        'type' => 'follow',
        'conttoken' => $_POST['conttoken'],
        'conttype' => $_POST['conttype'],
        'token' => taoh_get_dummy_token(),        
    );
    $taoh_call_type = "get";
    $req = taoh_apicall_get($taoh_call, $taoh_vals);

    echo $req;
}

function un_follow() {
    //$api = TAOH_SITE_CONTENT_GET . "?mod=core&ops=remove&type=follow&conttoken=".$_POST['conttoken']."&conttype=".$_POST['conttype']."&token=".taoh_get_api_token();
    //$req = taoh_url_get_content($api);
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod' => 'core',
        'ops' => 'remove',
        'type' => 'follow',
        'conttoken' => $_POST['conttoken'],
        'conttype' => $_POST['conttype'],
        'token' => taoh_get_dummy_token(),        
    );

    $taoh_call_type = "get";
    $req = taoh_apicall_get($taoh_call, $taoh_vals);

    echo $req;
}