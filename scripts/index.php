<?php
$start="http://localhost/Assignments/WebSpider-01/html/testLinks.html";

function linkFollower($url){

    $content=file_get_contents($url);
    if ($content==false){
        echo "Error: Links Could Not Be Fetched.";
        return;
    }
    $doc=new DOMDocument();
    $doc->loadHTML($content);

    $linkQueue= $doc->getElementsByTagName("a");
    foreach ($linkQueue as $link) {
        # code...
        echo $link->getAttribute("href")."<br>";
    }
}
linkFollower($start);


?>