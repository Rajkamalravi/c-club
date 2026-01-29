<?php
function taoh_string_split($string, $length = 1) {
    $return = array();
    if ($length < 1) return false;
    $pieces = ceil( strlen($string) / $length );
    //echo $pieces;taoh_exit();
    $words = explode(" ", $string);
    $i = 0;
    $return [ $i ] = '';
    foreach ($words as $word) {
        if (strlen($return[$i] . $word) < $length) {
            $return[$i] .= $word . ' ';
        } else {
            $i++;
            $return[$i] .= $word . ' ';
        }
    }
    return $return;
}
function taoh_get_category_color( $cat ){
    $cat_arr = array(
      "interview" => '#3E54AC',
      "job-search" => '#4E6E81',
      "networking" => '#3795BD',
      "resume" => '#AD7BE9',
      "jobs-of-future" => '#C92C6D',
      "branding" => '#609EA2',
      "mentor-coach" => '#865DFF',
     "conflict-management" => '#537FE7',
      "growth-mindset" => '#183A1D',
      "handling-change" => '#F0A04B',
      "leadership" => '#5D9C59',
      "career-development" => '#2B3467',
      "learning" => '#3A98B9',
      "mindfulness" => '#E96479',
      "future-of-work" => '#4D455D',
      "organization" => '#060047',
      "general-work-strategy" => '#443C68',
      "productivity" => '#40513B',
      "upskilling" => '#8D7B68',
    );
    $return = '#000000';
    if ( isset( $cat_arr[ $cat ] ) ){
      $return = $cat_arr[ $cat ];
    }
    if ( $cat == 'general' ){
      shuffle($cat_arr);
      $return = $cat_arr[0];
    }
    return $return ;
}

if ( ! defined( 'DOCUMENT_ROOT' ) ) define( 'DOCUMENT_ROOT', __DIR__ );
list ( $pre, $post ) = explode( 'images/ig/', $_SERVER['REQUEST_URI'] );
$vals = explode('/', $post);

$title = ucwords(urldecode($vals[0]));
$podname = urldecode($vals[1]);
$category = urldecode($vals[2]);

//me & Job Search for 2023
// Design a logic to cut string into substring pieces with complete words and not cut in the middle of a word
// https://stackoverflow.com/questions/11935037/how-to-break-a-string-into-smaller-strings-with-complete-words
// Text font size
//http://localhost/wpl/hires-i/ig/Creating%20Teams-%20The%20Fundamental%20Fabrics%20of%20a%20Successful%20Organization%20with%20Patrick%20Touhey/general/600_400/default.png

if ( ! isset( $category ) ){
    $category = 'general';
}
$img = array();

$img['width'] = '600';
$img['height'] = '400';
$misc = $category;
if ( stristr( $misc, '_' ) ){
    list($img['width'], $img['height']) = explode( '_', $misc );
}

$font_size = ceil( $img['width'] / 25 );
if ( $font_size > 35 && 0 ){
    $font_size = 30;
}
$per_line = ceil( $img['width'] / $font_size );


$text = ucwords( urldecode( $title ) );
$text_len = strlen($text);
if ( $text_len > 140 ) {
    $text = substr( $text, 0, 137 )."...";
}

$lines = ceil( $text_len / $font_size );
$text_arr = taoh_string_split($text, $per_line);
$category = strtolower( urldecode( $podname ) );
// Specify font path
//$font = TAOH_PLUGIN_PATH."/assets/fonts/Roboto.TTF";
$font = "../../fonts/VERDANA0.TTF";

$img['background'] = taoh_get_category_color( $category );
$img['color'] = '#ffffff';
$background = explode(",",hex2rgb($img['background']));
$textColorRgb = explode(",",hex2rgb($img['color']));

$width = empty($img['width']) ? 100 : $img['width'];
$height = empty($img['height']) ? 100 : $img['height'];

// Get text from URL
// Create the image resource
$image = @imagecreate($width, $height) or die("Cannot Initialize new GD image stream");
imagelayereffect($image, IMG_EFFECT_OVERLAY);

// Create image background
$background_color = imagecolorallocate($image, $background[0], $background[1], $background[2]);
$text_color = imagecolorallocate($image, $textColorRgb[0], $textColorRgb[1], $textColorRgb[2]);
imagefilledrectangle($image, 0, 0, $width, $height, $background_color );
$y = $font_size;
foreach ( $text_arr as $key => $value ) {

// Grab the width & height of the text box
$bounding_box_size = imagettfbbox($font_size, 0, $font, $value);
$text_width = $bounding_box_size[2] - $bounding_box_size[0];
$text_height = $bounding_box_size[7]-$bounding_box_size[1];

// Text x&y coordinates
$x = ceil(($width - $text_width) / 2);
// Define text color
// Write text to image
    $y = $y + 2 * $font_size;
    imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $value);
}

$text_foot = 'Reads by TAO';
$font_size = ceil( $img['width'] / 50 );
$bounding_box_size = imagettfbbox($font_size, 0, $font, $text_foot);
$text_width = $bounding_box_size[2] - $bounding_box_size[0];
$text_height = $bounding_box_size[7]-$bounding_box_size[1];
$x = ceil(($width - $text_width) / 2);
$y = ceil(($height - ceil(1.5 * $font_size)));
imagettftext($image, $font_size, 0,$x, $y, $text_color, $font, $text_foot);

$text_foot = "[ ".ucwords( str_ireplace( '-', ' ', $category ) )." ]";
$font_size = ceil( $img['width'] / 40 );
$bounding_box_size = imagettfbbox($font_size, 0, $font, $text_foot);
$text_width = $bounding_box_size[2] - $bounding_box_size[0];
$text_height = $bounding_box_size[7]-$bounding_box_size[1];
$x = ceil(($width - $text_width) / 2);
$y = $y - ceil(1.5 * $font_size);
imagettftext($image, $font_size, 0,$x, $y, $text_color, $font, $text_foot);

// Set the content type header - in this case image/png
header('Content-Type: image/png');

// Output the image
imagepng($image);
// Free up memory
imagedestroy($image);

// Convert color code to rgb
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    switch(strlen($hex)){
        case 1:
            $hex = $hex.$hex;
        case 2:
            $r = hexdec($hex);
            $g = hexdec($hex);
            $b = hexdec($hex);
            break;
        case 3:
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            break;
        default:
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
            break;
    }

    $rgb = array($r, $g, $b);
    return implode(",", $rgb);
}
?>
