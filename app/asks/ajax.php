<?php
//Getting a list of rooms
function asks_get_rooms() {

  $title = "skill";
  if( $_POST['type'] == "rolechat") {
    $title = "title";
  } else if( $_POST['type'] == "orgchat") {
    $title = "company";
  }
  //$query = ($_POST['term'] != "" ? "&term=" .$_POST['term'] : "");
  //$api = TAOH_CDN_PREFIX."/api/find.php?mod=".$_POST['mod']."&type=".$title."&ctype=token&code=".taoh_get_dummy_token()."&misc=".$_POST['misc']."&maxr=".$_POST['maxr'].$query;
  //echo $api;
  //$data = taoh_url_get_content($api);

  $query = ($_POST['term'] != "" ? $_POST['term'] : "");
  $taoh_vals = array(
      'mod' => $_POST['mod'],
      'type' => $title,
      'ctype' => 'token',
      'code' => taoh_get_dummy_token(),
      'misc' => $_POST['misc'],
      'maxr' => $_POST['maxr'],
      'term' => $query,
  );
  $taoh_call = "/api/find.php";
  $taoh_call_type = "get";

  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals, TAOH_CDN_PREFIX);exit();
  $data = taoh_apicall_get( $taoh_call, $taoh_vals, TAOH_PCDN_PREFIX);


  echo $data;
   die();
}

//Getting a list of asks
/* function asks_detail_options() {
  $conttoken = ( isset( $_POST['conttoken'] ) && $_POST['conttoken'] )? $_POST['conttoken']:'';
  $taoh_call = "asks.ask.get";
  $taoh_vals = array(
      'mod'=>'asks',
      'token'=>taoh_get_dummy_token(),
      'conttoken'=>$conttoken,
      'ops'=>'info',
    );
  if(isset( $conttoken ) ){
    $taoh_vals['cache'] = array ( "name" => taoh_p2us('asks.ask').'_'.$conttoken.'_info', "ttl" => 3600);
  }
  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();

  $response = taoh_apicall_get($taoh_call, $taoh_vals);
  echo $response;
  die();
} */

function asks_get() {
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

  $taoh_call = "asks.get";
  $taoh_vals = array(
      'mod' => 'asks',
      'geohash' => $geohash,
      'token' => taoh_get_dummy_token(1),
      'key' => defined('TAOH_ASKS_GET_LOCAL') && TAOH_ASKS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
      'local' => TAOH_ASKS_GET_LOCAL,
      'ops' => $ops,
      'search' => $search,
      'limit' => $limit,
      'offset' => $offset,
      'postDate' => $postDate,
      'from_date' => $from_date,
      'to_date' => $to_date,
      'filter_type' => $filter_type,
      'cfcc5h'=> 1,
    //'cache' => array ( "name" => taoh_p2us($taoh_call).'_'.TAOH_API_SECRET.'_list', "ttl" => 3600);
  );
  //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
  if($filter_type == 'saved'){
    $taoh_vals['cache_required'] = 0;

    unset($taoh_vals['cache_time']);
    unset($taoh_vals['cache']);
    unset($taoh_vals['cfcc5h']);
    $taoh_vals['token'] = taoh_get_dummy_token();
  }

  /* if ( $offset == $offset_default && $limit == $limit_default ) {
    $taoh_vals['cache'] = array ( "name" => taoh_p2us($taoh_call).'_'.TAOH_ROOT_PATH_HASH.'_list_'.hash('crc32',$search.$offset.$geohash.$limit), "ttl" => 3600);
  } */

  //echo TAOH_API_PREFIX."/$taoh_call?".http_build_query($taoh_vals);
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
  $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
  echo $data;
  die();
}
function ask_like_put(){
  //https://ppapi.tao.ai/asks.save?mod=asks&token=C3kONdHX&conttoken=hcr9759qtuir
  $taoh_call = "content.save";
  $remove = array("asks_*","ask_".$_POST['conttoken']);
  $taoh_vals = array(
      'slug' => 'asks',
      'token' => taoh_get_dummy_token(),
      'conttoken' => $_POST['conttoken'],
      'redis_store' => 'taoh_intaodb_asks',
      //'cache' => array('remove' => $remove),
  );
  //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die;
  $data = taoh_apicall_post($taoh_call, $taoh_vals);
  taoh_delete_local_cache('asks',$remove);
  echo $data;
  die();
}

function ask_comment_put() {
  $values = json_encode(array($_POST['conttoken'],'asks',$_POST['ptoken'],$_POST['met_click'],time(),TAOH_API_SECRET));
  //print_r($values);die;
  $data = taoh_cacheops( 'metricspush', $values );
  //print_r($data);die;
  echo $data;
  die();
}
//echo "hi";exit();


function taoh_apply_job_ask(){
  $toenter = array();
  $taoh_call = "core.refer.put";
  $rr_array = [];
  $toenter['refer'] = json_encode($rr_array );
  $actions_var = array(
                  'action_url' => '',
                  'action_page_blurb' => '',
                  'action_email_vars'=> array(
                            'subject' => 'Answer for the ask '.$_POST['ask_title'],
                            'supertitle' => 'You have been invited to answer for the ask '.$_POST['ask_title'],
                            'title' =>  'Answer for the ask '.$_POST['ask_title'],
                            'subtitle' => 'After creating and completing your profile, visit detail page for the ask '.$_POST['ask_title'].' to answer',
                            ),
                    'extra_info'=> array(
                      'title' => $_POST['ask_title'],
                      'app_name' => 'Ask',
                      'action' =>  'answer',
                      'link' => $_POST['detail_link'],
                      'action_link' => $_POST['from_link'],
                      'site_name'=>TAOH_SITE_NAME_SLUG,
                    ),

                  );
  $toenter['refer_data'] = json_encode(array(
          'requested_by_ptoken' => taoh_get_dummy_token(),
          'from_link' => $_POST['from_link'],
          'to_link' => $_POST['from_link'],
          'for_email' => '',
          'action_flag' => 1,
          'actions_var' => $actions_var,
          'referral_type' => 'Answer',
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

function asks_get_detail(){
  $conttoken = $_POST['conttoken'];
  $ptoken = $_POST['ptoken'];
  $from = 'listing';
  header('Content-Type: application/html; charset=utf-8');
  include_once('asks_detail_content.php');
}

?>
