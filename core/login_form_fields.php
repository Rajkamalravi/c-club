<?php
function avatar_select($current = "") {
	$return = "<div onblur='validate2(3)' id='avatarSelect'></div>
  <style>
	.selected-box {
	    border: none !important;
      cursor: pointer;
	}
	.component-icon {
	    display: none;
	}
	.icon {
	    background: none !important;
	    width: 40px !important;
	    height: 40px !important;
	    border: none !important;
	    margin: 0 !important;
	}
	div#avatarSelect-box-scroll {
    width: 425px;
    padding: 10px;
    text-align: center;
    z-index: 9;
    height: auto;
    position: absolute;
    top: -88px;
    left: 0px;
	}
	</style>
  <script type='text/javascript' src='".TAOH_CDN_PREFIX."/assets/iconselect/iconselect.js'></script>
  <script> avatarSelect('".$current."','".TAOH_AVATAR_URL."'); </script>";
  return $return;
}

function field_location($coordinates="", $location="", $geohash="", $js="") {
  $str ='<select id="locationSelect" placeholder="Location search" onchange="validate2(2)" autocomplete="off" class="required" name="coordinates">';
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
    $str = '<select id="companySelect" class="required" name="company:company[]" required placeholder="Type to select">';
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
    $str = '<select id="roleSelect" class="required" name="title:title[]" required placeholder="Type to select">';
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
    $str = '<select id="skillSelect" class="required" multiple name="skill:skill[]" required placeholder="Type to select">';
    if(@$options) {
      foreach ( $options as $key => $value ){
        list ( $pre, $post ) = explode( ':>', $value );
        $str .= "<option value='$key' selected='selected'>$post</option>";
      }
    }
  $str .='</select><script>skillSelect();</script>';
  return $str;
}

function field_fname($option = "") {
  return '<div class="form-group"> <label class="form-control-label">First Name :<span style="color:red"> * </span></label> <input type="text" id="fname" name="fname" placeholder="" class="form-control" onblur="validate1(1)"> </div>';
}

function field_lname($option = "") {
  return '<div class="form-group"> <label class="form-control-label">Last Name :<span style="color:red"> * </span></label> <input type="text" id="lname" name="lname" placeholder="" class="form-control" onblur="validate1(2)"> </div>';
}

function field_cname($option = "") {
  return '<div class="form-group"> <label class="form-control-label">My Public Chat Name :<span style="color:red"> * </span></label> <input type="text" id="chat_name" name="chat_name" placeholder="" class="form-control" onblur="validate2(1)"> </div>';
}

function field_about_me($option = "") {
  return '<div class="form-group"> <label class="form-control-label">About Me :<span style="color:red"> * </span></label><textarea name="aboutme" rows="5" type="text" onblur="validate3(1)" class="form-control" id="aboutme"></textarea></div>';
}

function field_funfact($option = "") {
  return '<div class="form-group"> <label class="form-control-label">Fun Fact (Great for ice-breakers) :<span style="color:red"> * </span></label><textarea name="funfact" id="funfact" rows="5" type="text" onblur="validate3(2)" class="form-control required"></textarea> </div>';
}

 ?>
