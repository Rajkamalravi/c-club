<?php
/**
 * Convert size strings to bytes
 * Helper function for getMaxUploadSize()
 */
function convertToBytes($size) {
    if ($size == '-1') {
        return PHP_INT_MAX; // Unlimited
    }

    $size = trim($size);
    if (empty($size)) {
        return 0;
    }

    $last = strtolower($size[strlen($size)-1]);
    $size = (float)$size;

    switch($last) {
        case 'g': $size *= 1024;
        case 'm': $size *= 1024;
        case 'k': $size *= 1024;
    }
    return (int)$size;
}

/**
 * Get Maximum Upload Size Allowed by Server
 *
 * This function calculates the maximum file upload size allowed by the server
 * by checking all limiting factors: PHP settings and Nginx configuration.
 *
 * @return array Returns array with 'bytes', 'mb', 'formatted', and 'limiting_factor'
 */
function getMaxUploadSize() {

    // Get PHP configuration values
    $upload_max_filesize = ini_get('upload_max_filesize');
    $post_max_size = ini_get('post_max_size');
    $memory_limit = ini_get('memory_limit');

    // Convert to bytes
    $uploadBytes = convertToBytes($upload_max_filesize);
    $postBytes = convertToBytes($post_max_size);
    $memoryBytes = convertToBytes($memory_limit);

    // Try to get Nginx client_max_body_size
    $nginxSize = '1M'; // Default Nginx value
    $nginxBytes = convertToBytes($nginxSize);

    // Attempt to read Nginx config (may not have permissions)
    $nginxConfig = @shell_exec('grep -r client_max_body_size /etc/nginx/ 2>/dev/null | grep -v "#"');

    if (!empty($nginxConfig) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxConfig, $matches)) {
        $nginxSize = $matches[1];
        $nginxBytes = convertToBytes($nginxSize);
    } else {
        // Try nginx -T command
        $nginxTest = @shell_exec('nginx -T 2>&1 | grep client_max_body_size | grep -v "#"');
        if (!empty($nginxTest) && preg_match('/client_max_body_size\s+(\d+[KMG]?);/', $nginxTest, $matches)) {
            $nginxSize = $matches[1];
            $nginxBytes = convertToBytes($nginxSize);
        }
    }

    // Find the minimum (most restrictive) value
    $limits = [
        'nginx_client_max_body_size' => $nginxBytes,
        'php_upload_max_filesize' => $uploadBytes,
        'php_post_max_size' => $postBytes
    ];

    // Filter out unlimited values for comparison
    $realLimits = array_filter($limits, function($value) {
        return $value < PHP_INT_MAX;
    });

    if (empty($realLimits)) {
        $minBytes = PHP_INT_MAX;
        $limitingFactor = 'none_unlimited';
    } else {
        $minBytes = min($realLimits);
        $limitingFactor = array_search($minBytes, $limits);
    }

    // Format the result
    $maxUploadMB = $minBytes / 1024 / 1024;

    // Create human-readable format
    if ($minBytes >= 1024 * 1024 * 1024) {
        $formatted = round($minBytes / (1024 * 1024 * 1024), 2) . ' GB';
    } elseif ($minBytes >= 1024 * 1024) {
        $formatted = round($minBytes / (1024 * 1024), 2) . ' MB';
    } elseif ($minBytes >= 1024) {
        $formatted = round($minBytes / 1024, 2) . ' KB';
    } else {
        $formatted = $minBytes . ' bytes';
    }

    return [
        'bytes' => $minBytes,
        'mb' => $maxUploadMB,
        'formatted' => $formatted,
        'limiting_factor' => $limitingFactor,
        'details' => [
            'nginx_client_max_body_size' => [
                'value' => $nginxSize,
                'bytes' => $nginxBytes
            ],
            'php_upload_max_filesize' => [
                'value' => $upload_max_filesize,
                'bytes' => $uploadBytes
            ],
            'php_post_max_size' => [
                'value' => $post_max_size,
                'bytes' => $postBytes
            ],
            'php_memory_limit' => [
                'value' => $memory_limit,
                'bytes' => $memoryBytes
            ]
        ]
    ];
}

/**
 * Simple function to get max upload size in bytes
 * Use this in your plugin for quick access
 *
 * @return int Maximum upload size in bytes
 */
function getMaxUploadSizeBytes() {
    $result = getMaxUploadSize();
    return $result['bytes'];
}

/**
 * Simple function to get max upload size in MB
 * Use this in your plugin for quick access
 *
 * @return float Maximum upload size in MB
 */
function getMaxUploadSizeMB() {
    $result = getMaxUploadSize();
    return $result['mb'];
}

// ============================================
// USAGE EXAMPLES
// ============================================

// Example 1: Get complete information
$uploadInfo = getMaxUploadSize();
echo "<h3>Server Upload Limits</h3>";
echo "<p><strong>Maximum Upload Size:</strong> " . $uploadInfo['formatted'] . "</p>";
echo "<p><strong>In Bytes:</strong> " . number_format($uploadInfo['bytes']) . "</p>";
echo "<p><strong>In MB:</strong> " . round($uploadInfo['mb'], 2) . " MB</p>";
echo "<p><strong>Limiting Factor:</strong> " . $uploadInfo['limiting_factor'] . "</p>";

echo "<hr>";

// Example 2: Use in file upload validation
$maxBytes = getMaxUploadSizeBytes();
$maxMB = getMaxUploadSizeMB();

echo "<h3>Plugin Usage Example</h3>";
echo "<pre>";
echo "// In your plugin's file upload handler:\n";
echo "if (\$_FILES['uploaded_file']['size'] > " . $maxBytes . ") {\n";
echo "    die('File too large. Maximum allowed: " . round($maxMB, 2) . " MB');\n";
echo "}\n";
echo "</pre>";

echo "<hr>";

// Example 3: Display in your plugin's UI
echo "<h3>For Plugin UI (JavaScript)</h3>";
echo "<pre>";
echo "// Set max file size in your file input\n";
echo "const MAX_FILE_SIZE = " . $maxBytes . "; // " . round($maxMB, 2) . " MB\n";
echo "const MAX_FILE_SIZE_MB = " . round($maxMB, 2) . ";\n\n";
echo "// Display message to user\n";
echo "console.log('Maximum upload size: " . round($maxMB, 2) . " MB');\n";
echo "</pre>";

echo "<hr>";

// Example 4: Detailed breakdown for debugging
echo "<h3>Detailed Configuration</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th><th>Bytes</th><th>Status</th></tr>";

foreach ($uploadInfo['details'] as $key => $detail) {
    $isLimiting = ($detail['bytes'] == $uploadInfo['bytes'] && $detail['bytes'] < PHP_INT_MAX);
    $bgColor = $isLimiting ? '#ffcccc' : '#ccffcc';
    $status = $isLimiting ? '<strong>LIMITING</strong>' : 'OK';

    if ($key !== 'php_memory_limit') {
        echo "<tr style='background-color: $bgColor'>";
        echo "<td>" . str_replace('_', ' ', $key) . "</td>";
        echo "<td>" . $detail['value'] . "</td>";
        echo "<td>" . number_format($detail['bytes']) . "</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<hr>";

// Example 5: JSON output for AJAX requests
echo "<h3>JSON Output (for AJAX)</h3>";
echo "<pre>";
echo json_encode($uploadInfo, JSON_PRETTY_PRINT);
echo "</pre>";

echo "<hr>";

// Example 6: Calculate safe chunk size for large file uploads
$safeChunkSize = min($maxBytes * 0.8, 5 * 1024 * 1024); // 80% of max or 5MB, whichever is smaller
echo "<h3>Recommended Chunk Size for Chunked Uploads</h3>";
echo "<p>" . round($safeChunkSize / 1024 / 1024, 2) . " MB (80% of max upload size)</p>";

?>
