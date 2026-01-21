<?php

include 'functions.php';

switch (taoh_parse_url(2)) {
    case 'post':
        if( ! taoh_user_is_logged_in() ) {
            return taoh_redirect();
        }
        if ( isset( $_GET[ 'post' ] ) && $_GET[ 'post' ] == date('Ymd') ){
            include_once('flashcard_post.php');
        } else {
            return taoh_redirect();
        }        
      break;

    case 'edit':
        if( ! taoh_user_is_logged_in() ) {
            return taoh_redirect();
        }
    include_once('flashcard_post.php');
    break;
    default:
      include_once('flash_local.php');
      break;
  }
  ?>