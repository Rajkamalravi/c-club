<?php
function avatar_select($current = "") {
	$return = "<div id='avatarSelect'></div>
  <script type='text/javascript' src='".TAOH_CDN_PREFIX."/assets/iconselect/iconselect.js'></script>
  <script> avatarSelect('".$current."','".TAOH_AVATAR_URL."'); </script>";
  return $return;
}

function field_location($coordinates="", $location="", $geohash="", $js="") {
  $str ='<select id="locationSelect" placeholder="Location search" autocomplete="off" class="required" name="coordinates">';
  if($coordinates && $location) {
    $str .='<option value="'.$coordinates.'">'.$location.'</option>';
  }
  $str .='</select>';
  $str .='<input id="coordinateLocation" type="hidden" name="full_location" value="'.$location.'">';
  $str .='<input id="geohash" type="hidden" name="geohash" value="'.$geohash.'">';
  if($js) {
    $str .='<script>locationSelect();</script>';
  } else {
    $str .='<script>locationSelect();</script>';
  }

  return $str;
}

function field_time_zone($value = "", $required = 0 ) {
  $str ='<input type="text" value="'.$value.'" id="local_timezoneSelect" name="local_timezone" placeholder="Type to select" />';
  $str .='<script>timeZoneSelect();</script>';
  return $str;
}

function field_company($options = "") {
    $str = '<select id="companySelect" required class="required" name="company:company[]" placeholder="Type to select">';
    if(@$options) {
      foreach ( $options as $key => $value ){
        list ( $pre, $post ) = explode( ':>', $value );
        $str .= "<option value='$key' selected='selected'>$post</option>";
      }
    }
  $str .='</select><script>companySelect();</script>';
  return $str;
}

function field_role($options = "") {
    $str = '<select id="roleSelect" required class="required" name="title:title[]" placeholder="Type to select">';
    if(@$options) {
      foreach ( $options as $key => $value ){
        list ( $pre, $post ) = explode( ':>', $value );
        $str .= "<option value='$key' selected='selected'>$post</option>";
      }
    }
  $str .='</select><script>roleSelect();</script>';
  return $str;
}

function field_skill($options = "", $required = 0 ) {
    $str = '<select required id="skillSelect" multiple name="skill:skill[]" placeholder="Type to select">';
    if(@$options) {
      foreach ( $options as $key => $value ){
        if(is_object($value)) {

          $str .= "<option value='$value->id' selected='selected'>$value->title</option>";

        } else {

          list ( $pre, $post ) = explode( ':>', $value );

           $str .= "<option value='$key' selected='selected'>$post</option>";

        }
      }
    }
  $str .='</select><script>skillSelect();</script>';
  return $str;
}

function field_fname($option = "") {
  return '<div class="form-group">
      <input class="form-control form--control required" type="text" name="fname">
  </div>';
}

function field_lname($option = "") {
  return '<div class="form-group">
      <input class="form-control form--control required" type="text" name="lname">
  </div>';
}

 ?>