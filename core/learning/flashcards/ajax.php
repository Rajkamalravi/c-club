<?php 
function flashcard_get(){
    //https://preapi.tao.ai/core.content.get?mod=core&ops=random&type=flash&token=y2Ds3ugv&category=mindfulness
    $category = ( isset( $_POST['category'] ) && $_POST['category'] )? $_POST['category']:'interview';
    $conttoken = ( isset( $_POST['conttoken'] ) && $_POST['conttoken'] )? $_POST['conttoken']:'';
    $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'';
    $type = ( isset( $_POST['type'] ) && $_POST['type'] )? $_POST['type']:'';
    //$category = taoh_parse_url(2);
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod'=>'core',
        'token'=>taoh_get_dummy_token(1),
        'ops'=>$ops,
        'type'=>$type,
        'category'=>$category,
        'conttoken'=>$conttoken,
        'cache_required'=>0,
        'time'=>time(),
    );
    //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
    $taoh_call_type = "get";
    //echo TAOH_API_PREFIX."/$taoh_call?".http_build_query($taoh_vals);taoh_exit();
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_get( $taoh_call, $taoh_vals );
    echo $data;
    die();
}
?>