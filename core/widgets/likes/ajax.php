<?php function taoh_like() {
    $url = TAOH_SITE_CONTENT_GET.'?mod=core&ops=like&type=likes&conttype='.$_POST["type"].'&conttoken='.$_POST["conttoken"].'&token='.TAOH_API_TOKEN;
    //https://preapi.tao.ai/core.content.get?mod=core&ops=like&type=likes&conttoken=i30t2bgy94hd&conttype=blog&token=y2Ds3ugv&like=1
  	//$result = file_get_contents( $url, false, $context );
       $taoh_call = 'core.content.get';
       $taoh_vals = array(
        'mod' => 'core',
        'token'=>taoh_get_dummy_token(1),
        'ops' =>  'like',
        'type' => 'likes',
        'conntoken' => $_POST['conttoken'],
        'conttype' => $_POST["type"],

      );
      // $cache_name = $taoh_call.'_likes_' . hash('sha256', $taoh_call . serialize($taoh_vals));
      // $taoh_vals[ 'cfcache' ] = $cache_name;
      // $taoh_vals[ 'cache_name' ] = $cache_name;
      ksort($taoh_vals);
      $req = taoh_apicall_get($taoh_call, $taoh_vals);

     print_r($result);
     die();
}
?>
