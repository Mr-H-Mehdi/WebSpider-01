<?php
// Load the JSON file
$jsonFile = 'data.json';
$jsonData = json_decode(file_get_contents($jsonFile), true);

// Search for the given query
$query = $_POST['text'];
$results = [];
foreach ($jsonData as $item) {
    if (strpos($item['Title'], $query) !== false || strpos($item['Description'], $query) !== false || strpos($item['Keywords'], $query) !== false || strpos($item['URL'], $query) !== false) {
        $results[] = $item;
    }
}

// Display the search results
echo '<h2>Search Results for "'.$_POST["text"].'"</h2>';
echo '<ul>';
foreach ($results as $result) {
    // echo ''. $result['Title'].'';
    // echo '<li><b>' . $result['Title'] . '</b>: <a href="'.$result['URL'].'">' . $result['url'] . '</li>';
    echo '<div>
            <h2>'.$result["Title"].'</h2>
            <br>

            <p>'.$result["Description"].'</p>
            <br>

            <a href='.$result['URL'].'>'.$result['URL'].'</a>
            <br>;
        </div>';
    echo "<br>";

}
echo '</ul>';

// Highlight the matched parts
// foreach ($results as $result) {
//     $matchedParts = explode(' ', $query);
//     foreach ($matchedParts as $part) {
//         $highlight = '';
//         if (strpos($result['name'], $part) !== false) {
//             $highlight .= '<span class="highlight">' . $part . '</span>';
//         } elseif (strpos($result['description'], $part) !== false) {
//             $highlight .= '<span class="highlight">' . $part . '</span>';
//         }
//         echo $highlight;
//     }
// }
?>