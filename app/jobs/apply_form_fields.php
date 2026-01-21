<?php
function field_location($coordinates="", $location="", $geohash="", $js=0, $required = 0) {
  $required = ($required) ? 'required':'';
  $str ='<select id="locationSelect" placeholder="Location search" autocomplete="off" class="locationSelect" name="coordinates" '.$required.'>';
  if($coordinates && $location) {
    $str .='<option value="'.$coordinates.'">'.$location.'</option>';
  }
  $str .='</select>';
  $str .='<input id="coordinateLocation" type="hidden" name="full_location" value="'.$location.'">';
  $str .='<input id="geohash" type="hidden" name="geohash" value="'.$geohash.'">';
  $str .='<script>locationSelect();</script>';

  return $str;
}

function field_job_location($coordinates="", $location="", $geohash="", $js=0, $required = 0) {
  $required = ($required) ? 'required':'';
  $str ='<select id="joblocationSelect" placeholder="Location search" autocomplete="off" class="locationSelect" name="coordinates" '.$required.'>';
  if($coordinates && $location) {
    $str .='<option value="'.$coordinates.'">'.$location.'</option>';
  }
  $str .='</select>';
  $str .='<input id="coordinateLocation" type="hidden" name="full_location" value="'.$location.'">';
  $str .='<input id="geohash" type="hidden" name="geohash" value="'.$geohash.'">';
  $str .='<script>joblocationSelect();</script>';

  return $str;
}

function field_company($options = "") {
    $str = '<select id="companySelect" name="company:company[]" placeholder="Type to select">';
    if(@$options) {
      foreach ( $options as $key => $value ){
        list ( $pre, $post ) = explode( ':>', $value );
        $str .= "<option value='$key' selected='selected'>$post</option>";
      }
    }
  $str .='</select><script>companySelect();</script>';
  return $str;
}

function field_fname($option = "") {
  return '<div class="form-group">
      <input class="form-control form--control" value="'.$option.'" type="text" name="fname">
  </div>';
}

function field_lname($option = "") {
  return '<div class="form-group">
      <input class="form-control form--control" value="'.$option.'" type="text" name="lname">
  </div>';
}

function field_email($option = "") {
  return '<div class="form-group">
      <input class="form-control form--control" value="'.$option.'" required type="text" name="email">
  </div>';
}

?>
