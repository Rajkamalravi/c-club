<?php
header ( "Location: ".dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ); exit();
if ( ! defined('TAOH_SITE_URL_ROOT') ) {
    define( 'TAOH_SITE_URL_ROOT', sprintf( "%s://%s%s", isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] != 'off' ? 'https' : 'http', $_SERVER[ 'HTTP_HOST' ], dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) ); // Only enable this if your website is not reachable by search engines
  }
//print_r( $_SERVER );
//echo __DIR__;
echo "
<h2>Installation</h2>
<h4>create a copy of the file env_sample.php and rename it to env_".hash( 'crc32', TAOH_SITE_URL_ROOT ).".php</h4>
<h4>edit the file env_".hash( 'crc32', TAOH_SITE_URL_ROOT ).".php and set your personal values</h4>
";
if ( stristr( $_SERVER[ 'SERVER_SOFTWARE' ], 'nginx' ) ){
    echo "
    <h4>Using NGINX?</h4>
    <h5>add the following to your nginx configuration file associated with ".$_SERVER[ 'HTTP_HOST' ]." configuration</h5>
    <h5>
    location /hires/ {<br />
        root ".dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ).";<br />
        try_files \$uri \$uri/ ".dirname( $_SERVER[ 'SCRIPT_NAME' ] )."/index.php?\$args;<br />
      }<br />
    </h5>
        ";
}
if ( stristr( $_SERVER[ 'SERVER_SOFTWARE' ], 'apache' ) ){
    echo "
    <h4>Using Apache?</h4>
    <h5>Add the following lines to .htaccess file and store it in this folder</h5>
    <h5>RewriteEngine on<br />
    RewriteCond %{REQUEST_FILENAME} !-d<br />
    RewriteCond %{REQUEST_FILENAME} !-f<br />
    RewriteRule . /wpl/hires-i/index.php [L]</h5>
    <h4>once done, delete this file install.php</h4>
        ";
}
?>