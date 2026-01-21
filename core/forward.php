<?php

if ( isset( $_GET[ 'redirect' ] ) && $_GET[ 'redirect' ] ){
    header("Location: ".urldecode($_GET[ 'redirect' ]));taoh_exit();
}
