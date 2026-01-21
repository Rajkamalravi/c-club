<?php
define('TAOH_CURR_APP_SLUG', 'learning');
switch (taoh_parse_url(1)) {
    case 'obviousbaba':
        include_once('obviousbaba.php');
        break;
/*
    case 'askob':
        include_once('askob.php');
        break;
    case 'askcc':
        include_once('askcc.php');
        break;
*/
    case 'jusask':
        include_once('jusask.php');
        break;
    case 'askobviousbaba':
        include_once('askobviousbaba.php');
        break;
    case 'flashcard':
        include_once('flashcards/main.php');
        break;
    case 'newsletter':
        include_once('newsletter/main.php');
        break;
    case 'tips':
        include_once('tips/tips.php');
        break;
    case 'tip':
        include_once('tips/tips.php');
        break;
    default:
        include_once('blog/main.php');
        break;
  }
  ?>