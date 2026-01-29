<?php
// /hires/cacheo/remove/b5d278b21285ffbc1483ac3d7798a729/vbgbye821k_chat_chats_get
$cmd = taoh_parse_url(2,0);
$string = taoh_parse_url(3,0);
$code = taoh_parse_url(4,0);
$code2 = json_decode( taoh_url_get_content(TAOH_OPS_PREFIX."/scripts/code.php?q=".TAOH_API_SECRET ), true)[ 'output' ];
if ( $code == $code2 ){
    //echo "Cache removed for $string <br>";
    if ( $cmd == 'remove' ){
        taoh_cache_remove_local($string);

    }
}
