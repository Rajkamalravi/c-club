<?php
function newsletter_like_put() {
  $values = json_encode(array($_POST['conttoken'],'newsletter',$_POST['ptoken'],'like',time(),TAOH_API_SECRET));
  //print_r($values);die;
  $data = taoh_cacheops( 'metricspush', $values );
  //print_r($data);die;
  echo $data;
  die();
}
?>