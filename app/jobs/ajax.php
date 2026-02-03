<?php

//Getting a list of asks
function jobs_get() {
  //https://api.tao.ai/jobs.get?mod=jobs&geohash=&token=y2Ds3ugv&ops=list&search=dev
  //https://api.tao.ai/jobs.get?mod=jobs&geohash=&token=y2Ds3ugv&ops=list&search=cook
  $offset_default = 0;
  $limit_default = 10;

  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'list';
  $geohash = ( isset( $_POST['geohash'] ) && $_POST['geohash'] )? $_POST['geohash']:'';
  $search = ( isset( $_POST['search'] ) && $_POST['search'] )? $_POST['search']:'';
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? ($_POST['offset'] * $limit_default):($offset_default * $limit_default);
  $postDate = ( isset( $_POST['postDate'] ) && $_POST['postDate'] )? $_POST['postDate']:'';
  $from_date = ( isset( $_POST['from_date'] ) && $_POST['from_date'] )? $_POST['from_date']:'';
  $to_date = ( isset( $_POST['to_date'] ) && $_POST['to_date'] )? $_POST['to_date']:'';
  $filter_type = ( isset( $_POST['filter_type'] ) && $_POST['filter_type'] )? $_POST['filter_type']:'';

  $allFilters = $_POST['filters'];
  //print_r(unserialize($allFilters));
  //echo"===========";
  //Get token from https://cdn.tao.ai/func/dtoken
//  $token =  taoh_get_api_token() ? taoh_get_api_token() :'hT93oaWC';

  //$api = TAOH_API_PREFIX."/jobs.get?mod=jobs&geohash=".$geohash."&key=".TAOH_API_SECRET."&token=".$token."&ops=".$ops."&search=".$search;

  //$data = taoh_url_get($api);
  $taoh_call = "jobs.get";
  $taoh_vals = array(
    'mod'=>'jobs',
    'ops'=>$ops,
	  //'secret' => TAOH_API_SECRET,
    'token'=>taoh_get_dummy_token(1),
    'key' => defined('TAOH_JOBS_GET_LOCAL') && TAOH_JOBS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
    'token' => taoh_get_dummy_token(1),   
    'local'=>TAOH_JOBS_GET_LOCAL,
    'geohash'=>$geohash,
    'search'=>$search,
    'limit'=>$limit,
    'offset'=>$offset,
    'postDate'=>$postDate,
    'from_date'=>$from_date,
    'to_date'=>$to_date,
    'filters'=>$allFilters,
    'filter_type'=>$filter_type,
    'cfcc5h'=> 1,
    
    //'cache'=> array ( "name" => taoh_p2us($taoh_call).'_'.$_POST['ptoken'].'_'.TAOH_ROOT_PATH_HASH.'_'.hash('crc32',$search.$geohash.$allFilters.$offset.$limit).'_list', 'ttl' => 3600 ),
  );
 
  //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
  /*if ( $offset == $offset_default && $limit == $limit_default ) {
    $taoh_vals['cache'] = array ( "name" => taoh_p2us($taoh_call).'_'.TAOH_API_SECRET.'_'.hash('crc32',$search).'_list', 'ttl' => 3600 ) ;
  }*/
  if($filter_type == 'saved' || $filter_type == 'applied'){
    $taoh_vals['cache_required'] = 0;
    unset($taoh_vals['cfcc5h']);
    unset($taoh_vals['cache_time']);
    unset($taoh_vals['cache']);
    $taoh_vals['token'] = taoh_get_dummy_token();
    
  }
  
  $taoh_call_type = "get";
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
  //$data = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);

  //echo TAOH_API_PREFIX . '/' .$taoh_call.'?'.http_build_query($taoh_vals);
  //echo $url;
  echo $data;
  die();
}

function jobs_get_detail_old(){
  $conttoken = isset($_POST['conttoken']) ? $_POST['conttoken'] : '';
  $ops = 'info';
  $mod = 'jobs';
  $taoh_call = 'jobs.job.get';
  if ( ! ctype_alnum( $conttoken ) ) { taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG );taoh_exit(); }
  
  //$cache_name = $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
  $cache_name = $mod.'_'.$ops.'_' . $conttoken ;
  $taoh_vals = array(
      'token' => taoh_get_dummy_token(1),
      'ops' => $ops,
      'mod' => $mod,
      'cache_name' => $cache_name,
      'cache_time' => 7200,
     // 'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
      'conttoken' => $conttoken,
      
  );
  //$taoh_vals[ 'cfcache' ] = $cache_name;
  ksort($taoh_vals);

  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
  $response = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
  echo $response;die;
}

function jobs_get_detail(){
  $conttoken = $_POST['conttoken'];
  $ptoken = $_POST['ptoken'];
  $from = 'listing';
  header('Content-Type: application/html; charset=utf-8');
  include_once('common_job_details.php');
}

function jobs_scout_list(){
  $taoh_call = "jobs.scout.list";
  $taoh_vals = array(
    'mod' => $_POST['mod'],
    'ptoken' => $_POST['ptoken'],
    'secret' => TAOH_API_SECRET,
    //'cache' => array ( "name" => taoh_p2us($taoh_call).'_'.$conttoken.'_apply_status', "ttl" => 3600),
  );
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
  $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);

  echo $data;
  die();
}

function job_like_put_old() {
  $values = json_encode(array($_POST['conttoken'],'jobs',$_POST['ptoken'],'like',time(),TAOH_API_SECRET));
  //print_r($values);die;
  $data = taoh_cacheops( 'metricspush', $values );
  //print_r($data);die;
  echo $data;
  die();
}

function job_like_put() {
  //https://ppapi.tao.ai/jobs.save?mod=jobs&token=C3kONdHX&conttoken=hcr9759qtuir
  $taoh_call = "content.save";
  $remove = array("jobs_metrics");
  $taoh_vals = array(
      'slug' => 'jobs',
      'token' => taoh_get_dummy_token(),
      'conttoken' => $_POST['conttoken'],
      'redis_store' => 'taoh_intaodb_jobs',
      'cache' => array('remove' => $remove),
  );
  echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die;
  $data = taoh_apicall_post($taoh_call, $taoh_vals);
  //taoh_delete_local_cache('jobs',$remove);
  echo $data;
  die();
}

function metrics_put_job() {
  $values = json_encode(array($_POST['conttoken'],'jobs',$_POST['ptoken'],$_POST['met_click'],time(),TAOH_API_SECRET));
  //print_r($values);die;
  $data = taoh_cacheops( 'metricspush', $values );
  //print_r($data);die;
  echo $data;
  die();
}

function taoh_apply_job_referral(){
  $toenter = array();
  $taoh_call = "core.refer.put";
  $rr_array = [];
  $toenter['refer'] = json_encode($rr_array);
  $actions_var = array(
      'action_url' => '',
      'action_page_blurb' => '',
      'action_email_vars' => array(
          'subject' => 'Apply for the job ' . $_POST['job_title'],
          'supertitle' => 'You have been invited to apply for the job ' . $_POST['job_title'],
          'title' => 'Apply for the job ' . $_POST['job_title'],
          'subtitle' => 'After creating and completing your profile, visit detail page for the job ' . $_POST['job_title'] . ' to apply',
      ),
      'extra_info' => array(
          'title' => $_POST['job_title'],
          'app_name' => 'Job',
          'action' => 'apply',
          'link' => $_POST['detail_link'],
          'action_link' => $_POST['from_link'],
          'site_name' => TAOH_SITE_NAME_SLUG,
      ),

  );
  $toenter['refer_data'] = json_encode(array(
          'requested_by_ptoken' => taoh_get_dummy_token(),
          'from_link' => $_POST['from_link'],
          'to_link' => $_POST['from_link'],
          'for_email' => '',
          'action_flag' => 1,
          'actions_var' => $actions_var,
          'referral_type' => 'Apply',
      )
  );
  $taoh_vals = array(
      'ops' => 'invite',
      'token' => taoh_get_dummy_token(),
      'toenter' => $toenter,
  );
  
  //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );
  // $result = taoh_post( TAOH_CACHE_CHAT_PROC_URL, $taoh_vals );
  $result = json_decode(taoh_apicall_post($taoh_call, $taoh_vals)); 
  //print_r($result);die();
  $res = array('success'=> 1,'refer_token' => $result->refer_token[0]);
  setcookie(TAOH_ROOT_PATH_HASH.'_'.'refer_token', $result->refer_token[0], strtotime( '+1 days' ), '/');
  setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',$_POST['from_link'], strtotime( '+1 days' ), '/');

  if($result->success==1){
    echo json_encode($res);
    die();
  }else{
    echo 0;
  }
}

 ?>