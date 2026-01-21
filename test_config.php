<?php
// Function to convert size strings to bytes
function convertToBytes($size) {
    $size = trim($size);
    $last = strtolower($size[strlen($size)-1]);
    $size = (int)$size;

    switch($last) {
        case 'g': $size *= 1024;
        case 'm': $size *= 1024;
        case 'k': $size *= 1024;
    }
    return $size;
}

// Get Nginx client_max_body_size
$nginxConfig = shell_exec('grep -r client_max_body_size /etc/nginx/ 2>/dev/null | grep -v "#"');
$nginxSize = '1M'; // Default Nginx value if not found

if (!empty($nginxConfig) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxConfig, $matches)) {
    $nginxSize = $matches[1];
}

// Try alternative methods to get Nginx config
if ($nginxSize === '1M' && empty($nginxConfig)) {
    $nginxConfig .= "\n[Attempting alternative methods...]\n";

    // Try nginx -T (requires permissions)
    $nginxTest = shell_exec('nginx -T 2>&1 | grep client_max_body_size');
    if (!empty($nginxTest)) {
        $nginxConfig .= "From nginx -T:\n" . $nginxTest;
        if (preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxTest, $matches)) {
            $nginxSize = $matches[1];
        }
    }

    // Try common config locations
    $locations = [
        '/etc/nginx/nginx.conf',
        '/etc/nginx/sites-enabled/*',
        '/etc/nginx/conf.d/*'
    ];

    foreach ($locations as $loc) {
        $result = shell_exec("grep client_max_body_size $loc 2>/dev/null");
        if (!empty($result)) {
            $nginxConfig .= "From $loc:\n" . $result;
            if (preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $result, $matches)) {
                $nginxSize = $matches[1];
                break;
            }
        }
    }
}

// Get PHP configuration values
$upload_max_filesize = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');

// Convert to bytes for comparison
$nginxBytes = convertToBytes($nginxSize);
$uploadBytes = convertToBytes($upload_max_filesize);
$postBytes = convertToBytes($post_max_size);
$memoryBytes = convertToBytes($memory_limit);

// Find the minimum (most restrictive)
$restrictingSizes = [
    'Nginx client_max_body_size' => $nginxBytes,
    'PHP upload_max_filesize' => $uploadBytes,
    'PHP post_max_size' => $postBytes
];

$minSize = min($restrictingSizes);
$restrictingFactor = array_search($minSize, $restrictingSizes);

// Display results
echo "<h2>Upload Size Configuration Analysis</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Configuration</th><th>Value</th><th>Bytes</th><th>Status</th></tr>";

echo "<tr style='background-color: " . ($nginxBytes == $minSize ? '#ffcccc' : '#ccffcc') . "'>";
echo "<td>Nginx client_max_body_size</td><td>" . $nginxSize . (empty($nginxConfig) ? ' (default - not found in config)' : '') . "</td><td>" . number_format($nginxBytes) . "</td>";
echo "<td>" . ($nginxBytes == $minSize ? '<strong>RESTRICTING</strong>' : 'OK') . "</td></tr>";

echo "<tr style='background-color: " . ($uploadBytes == $minSize ? '#ffcccc' : '#ccffcc') . "'>";
echo "<td>PHP upload_max_filesize</td><td>" . $upload_max_filesize . "</td><td>" . number_format($uploadBytes) . "</td>";
echo "<td>" . ($uploadBytes == $minSize ? '<strong>RESTRICTING</strong>' : 'OK') . "</td></tr>";

echo "<tr style='background-color: " . ($postBytes == $minSize ? '#ffcccc' : '#ccffcc') . "'>";
echo "<td>PHP post_max_size</td><td>" . $post_max_size . "</td><td>" . number_format($postBytes) . "</td>";
echo "<td>" . ($postBytes == $minSize ? '<strong>RESTRICTING</strong>' : 'OK') . "</td></tr>";

echo "<tr><td>PHP memory_limit (reference)</td><td>" . $memory_limit . "</td><td>" . number_format($memoryBytes) . "</td><td>-</td></tr>";

echo "</table>";

echo "<h3 style='color: red;'>Maximum Upload Size: " . ($minSize / 1024 / 1024) . " MB</h3>";
echo "<p><strong>Restricting Factor:</strong> " . $restrictingFactor . "</p>";

echo "<hr>";
echo "<h3>Diagnostic Information:</h3>";
echo "<pre>";
echo "PHP INI Location: " . php_ini_loaded_file() . "\n";
echo "Web Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "PHP User: " . get_current_user() . " (UID: " . getmyuid() . ")\n";
echo "</pre>";

echo "<h3>Raw Nginx Configuration (client_max_body_size):</h3>";
echo "<pre>" . htmlspecialchars($nginxConfig ?: 'Not found in Nginx config - PHP user may not have permission to read /etc/nginx/') . "</pre>";

// Additional helpful info
echo "<h3>Quick Fix Guide:</h3>";
echo "<ul>";
echo "<li>If Nginx is restricting: Edit your Nginx config and add/increase <code>client_max_body_size 100M;</code></li>";
echo "<li>If PHP upload_max_filesize is restricting: Edit php.ini and set <code>upload_max_filesize = 100M</code></li>";
echo "<li>If PHP post_max_size is restricting: Edit php.ini and set <code>post_max_size = 100M</code></li>";
echo "<li>Note: post_max_size should be >= upload_max_filesize</li>";
echo "<li>After changes: Restart PHP-FPM and Nginx</li>";
echo "</ul>";
?>
