<?php

switch (taoh_parse_url(1)) {
    case 'inhale-exhale':
        include_once('inhale-exhale.php');
        break;
    case 'pomodoro':
        include_once('pomodoro.php');
        break;
    default:
        include_once('../blog/main.php');
        break;
  }
  ?>