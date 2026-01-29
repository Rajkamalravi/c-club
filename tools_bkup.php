<?php

// http://localhost/wpl/club/tools.php?secret=DVrIy1cu&ops=addsitemap&url=http://www.abc.com/sitemap.xml

require_once 'config.php';
require_once 'function.php';
$vars = $_POST;

if ( ! function_exists( 'taoh_sitemapops' ) ) {

    function taoh_sitemapops($vars) {
        $sitemapPath = 'sitemap.xml'; // Local path to the sitemap file
        $response = ['success' => false, 'message' => ''];
        if(isset($vars['url'])){
            $vars['url'] = htmlspecialchars($vars['url']);
        }
        // Check if the sitemap exists, if not, create a new basic sitemap
        if (!file_exists($sitemapPath)) {
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
            $xml->asXML($sitemapPath);
            $response['message'] = "New sitemap.xml created.";
        } else {
            $xml = simplexml_load_file($sitemapPath);
            if ($xml === false) {
                $response['message'] = "Error: Failed to load sitemap.";
                return $response;
            }
        }

        switch ($vars['ops']) {
            case 'addsitemap':
                $urlFound = false;

                // Check if the URL already exists
                foreach ($xml->url as $urlElement) {
                    if ((string)$urlElement->loc === $vars['url']) {
                        $urlFound = true;
                        $urlElement->lastmod = date('Y-m-d H:i:s'); // Update lastmod if URL exists
                        break;
                    }
                }

                // Add the new URL if it was not found
                if (!$urlFound) {
                    $urlElement = $xml->addChild('url');
                    $urlElement->addChild('loc', $vars['url']);
                    $urlElement->addChild('lastmod', date('Y-m-d H:i:s'));
                    $urlElement->addChild('changefreq', 'monthly');
                    $urlElement->addChild('priority', '0.5');
                    $urlElement->addChild('expiry', $vars['expiry']);
                    $response['message'] = "URL added successfully.";
                    $response['success'] = true;
                } else {
                    $response['message'] = "URL already exists and lastmod updated.";
                    $response['success'] = true;
                }
                break;
            case 'delsitemap':
                $found = false;
                // Find and remove the specified URL
                $all_xml = (array) $xml;
                //echo '<pre>';print_r($all_xml);echo '</pre>';
                foreach($all_xml['url'] as $key => $value){
                    if($key == 'loc'){
                        if($value == $vars['url']){
                            unset($all_xml['url']);
                            $found = true;
                            break;
                        }
                    }else{
                        if($value->loc == $vars['url']){
                            unset($all_xml['url'][$key]);
                            $found = true;
                            break;
                        }
                    }
                }
                //echo '<pre>';print_r($all_xml);echo '</pre>';
                if ($found) {
                    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
                    foreach($all_xml['url'] as $key => $value){
                        $urlElement = $xml->addChild('url');
                        $urlElement->addChild('loc', $value->loc);
                        $urlElement->addChild('lastmod', $value->lastmod);
                        $urlElement->addChild('changefreq', $value->changefreq);
                        $urlElement->addChild('priority', $value->priority);
                        $urlElement->addChild('expiry', $value->expiry);
                    }
                    $response['message'] = "URL removed successfully.";
                    $response['success'] = true;
                } else {
                    $response['message'] = "URL not found in the sitemap.";
                }
                break;
            case 'cleansitemap':
                // Remove all entries from the sitemap
                $all_xml = (array) $xml;
                unset($all_xml['url']);
                $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
                $response['message'] = "All URLs have been removed from the sitemap.";
                $response['success'] = true;
                break;
            case 'getsitemap':
                $response['message'] = (array)$xml;
                $response['success'] = true;
                break;
            default:
                $response['message'] = "Invalid operation specified.";
                return $response;
        }
        //echo '<pre>';print_r($all_xml);echo '</pre>';die;
        // Save the updated sitemap back to the file
        $xml->asXML($sitemapPath);
        $response['time'] = date('Y-m-d H:i:s');
        return $response;
    }
}

if ( isset( $vars[ 'secret' ] ) && $vars[ 'secret' ] === TAOH_API_SECRET ){
    switch ( $vars[ 'ops' ] ) {
        case 'addsitemap':
            $return = taoh_sitemapops( $vars );
            break;
        case 'delsitemap':
            $return = taoh_sitemapops( $vars );
            break;
        case 'cleansitemap':
            $return = taoh_sitemapops( $vars );
            break;
        case 'getsitemap':
            $return = taoh_sitemapops( $vars );
            break;
        default:
            echo 'Invalid operation';
    }
} else {
    echo 'Invalid Secret!!!';
}
echo json_encode( $return );
taoh_exit();

?>