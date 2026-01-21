<?php

function domainValuation($domain) {
    // Define weights for different factors (adjust these as needed)
    $weights = [
        'length' => 0.2,       // Weight for domain length
        'extension' => 0.2,    // Weight for domain extension
        'keyword' => 0.3,      // Weight for keyword relevance
        'age' => 0.1,          // Weight for domain age
        'backlinks' => 0.1,    // Weight for quality backlinks
        'traffic' => 0.1       // Weight for existing traffic
    ];

    // Calculate the length score (shorter domains get higher scores)
    $lengthScore = 1 - (strlen($domain) / 20);

    // Define a list of valuable extensions and their scores
    $extensionScores = [
        'com' => 1.0,
        'net' => 0.8,
        'org' => 0.7
        // Add more extensions and scores as needed
    ];

    // Extract the extension from the domain
    $domainParts = explode('.', $domain);
    $extension = end($domainParts);
    $extensionScore = isset($extensionScores[$extension]) ? $extensionScores[$extension] : 0.5;

    // You can implement keyword analysis here (assign a keyword score)

    // Calculate the age score (older domains get higher scores)
    $domainCreationDate = strtotime('1995-01-01'); // Replace with actual creation date
    $currentDate = time();
    $domainAgeYears = ($currentDate - $domainCreationDate) / (365 * 24 * 3600);
    $ageScore = 1 - ($domainAgeYears / 30); // Assuming a 30-year maximum age

    // You can integrate data on backlinks and traffic from external sources
    $backlinksScore = 0.5; // Assign a score based on the quality and quantity of backlinks
    $trafficScore = 0.6;   // Assign a score based on the level of existing traffic

    // Calculate the composite score
    $compositeScore = (
        $lengthScore * $weights['length'] +
        $extensionScore * $weights['extension'] +
        $ageScore * $weights['age'] +
        $backlinksScore * $weights['backlinks'] +
        $trafficScore * $weights['traffic']
    ) * 100;

    return $compositeScore;
}

$domain = "example.com";
$valuation = domainValuation($_GET[ 'q' ]);
echo "The estimated value of $domain is $valuation";

?>
