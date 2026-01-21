<?php
if ( isset( $_GET[ 'install' ] ) ){
  taoh_insall_xml();
}

myxml();

function taoh_insall_xml(){
  $site_robot_file = TAOH_SITE_ROBOT;
  if( isset( $_GET[ 'forcerobots' ] ) ) {
    $get_val = "User-agent: *
Disallow: /wp-admin/
Disallow: /wp-login.php ".PHP_EOL."
Sitemap: ".TAOH_XML_URL.PHP_EOL."
    ";
    file_put_contents( $site_robot_file, $get_val );
  }
  if ( file_exists( $site_robot_file ) ){
    if ( ! stristr( taoh_url_get( $site_robot_file ), "sitemap: ".TAOH_XML_URL ) ){
      $get_val = PHP_EOL."sitemap: ".TTAOH_XML_URL;
      file_put_contents( $site_robot_file, $get_val, FILE_APPEND );
    }
  }
}

function myxml(){
  $url = TAOH_SITE_XML_GET;
  if ( taoh_parse_url(0) ) $url = $url."?q=".taoh_parse_url(0);
  $sitemap_json = str_replace( "[localurl]", TAOH_SITE_URL_ROOT, taoh_url_get_content( $url ) );
  $sitemap_arr = ( array ) json_decode( $sitemap_json );
  foreach ( $sitemap_arr as $site_elem ) {
    echo $site_elem.PHP_EOL;
  }
}

?>
