<?php

$urls = [
    TAOH_SITE_URL_ROOT."/events",
    TAOH_SITE_URL_ROOT."/login",
    TAOH_SITE_URL_ROOT."/",
    TAOH_SITE_URL_ROOT."/events",
    TAOH_SITE_URL_ROOT."/asks",
    TAOH_SITE_URL_ROOT."/jobs",
    TAOH_SITE_URL_ROOT."/learning/job",
    TAOH_SITE_URL_ROOT."/learning/work",
    TAOH_SITE_URL_ROOT."/learning/wellness",
    TAOH_SITE_URL_ROOT."/learning/flashcard",
    TAOH_SITE_URL_ROOT."/404",

    // Add more URLs as needed
];

/*$urls = [
    "http://localhost/hires-i/login",
    "http://localhost/hires-i/",
    "http://localhost/hires-i/events",
    "http://localhost/hires-i/asks",
    "http://localhost/hires-i/jobs",
    "http://localhost/hires-i/learning/job",
    "http://localhost/hires-i/learning/work",
    "http://localhost/hires-i/learning/wellness",
    "http://localhost/hires-i/learning/flashcard",
    // Add more URLs as needed
];*/

//$username = "your_username";
//$password = "your_password";

function checkUrls($urls) {
    $results = [];
    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // We don't need the body, just the header
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            $results[$url] = "Request Exception: " . curl_error($ch);
        } else {
            if ($httpCode == 200) {
                $results[$url] = "Success";
            } elseif ($httpCode == 404) {
                $results[$url] = "Not Found (404)";
            } else {
                $results[$url] = "Error $httpCode";
            }
        }
        curl_close($ch);
    }
    return $results;
}

// Function to get URLs from the first list page
function getUrlsFromListPage($url) {
    //echo "url=====";print_r($url);//die();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds

    $response = curl_exec($ch);
     //echo "response=====";print_r($response);//die();
    if (curl_errno($ch)) {
        echo "Failed to fetch the list page: " . curl_error($ch) . "\n";
        curl_close($ch);
        return [];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "Failed to fetch the list page: HTTP $httpCode\n";
        return [];
    }

    $dom = new DOMDocument;
    @$dom->loadHTML($response);
   // echo "dom=====";print_r($dom);//die();
    $xpath = new DOMXPath($dom);
    //echo "xpath=====";print_r($xpath);//die();
    $nodes = $xpath->query('//events_list/a'); // Modify the XPath query based on actual structure
    echo "nodes=====";print_r($nodes);die();

    $urls = [];
    foreach ($nodes as $node) {
        $urls[] = $node->getAttribute('href');
    }

    return $urls;
}

function checkUrlsWithSession($urls) {
    $results = [];
    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // We don't need the body, just the header
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); // Use the saved cookies
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $results[$url] = "Request Exception: " . curl_error($ch);
        } else {
            if ($httpCode == 200) {
                $results[$url] = "Success";
            } elseif ($httpCode == 404) {
                $results[$url] = "Not Found (404)";
            } else {
                $results[$url] = "Error $httpCode";
            }
        }
        curl_close($ch);
    }
    return $results;
}

// Check URLs before login
// $results_before_login = checkUrls($urls);
// Check URLs before login
$urls_before_login = [];
foreach ($urls as $status ) {
    $urls_before_login = getUrlsFromListPage($status);
}

echo"==============";print_r($urls_before_login);die();

$results_before_login = checkUrls($urls_before_login);
echo "Before Login:<br/>";
foreach ($results_before_login as $url => $status) {
    echo "$url: $status<br/>";
}

// Perform login and check URLs after login
//if (login($login_url, $username, $password)) {
if(TAOH_API_TOKEN){
    $results_after_login = checkUrlsWithSession($urls_before_login);
    echo "After Login:<br/>";
    foreach ($results_after_login as $url => $status) {
        echo "$url: $status<br/>";
    }
} else {
    echo "Login failed. Cannot check URLs after login.<br/>";
}
?>
