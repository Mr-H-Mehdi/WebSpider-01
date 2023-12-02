<?php
$seedURL="http://localhost/Assignments/WebSpider-01/html/testLinks.html";

function linkFollower($url){

    $content=file_get_contents($url);
    if ($content==false){
        echo "Error: Links Could Not Be Fetched.";
        return;
    }
    $doc=new DOMDocument();
    $doc->loadHTML($content);

    $linkQueue = $doc->getElementsByTagName("a");
    foreach ($linkQueue as $link) {
        # code...
        $l = $link->getAttribute("href");

        /** conditionals to handle different formats of links **/
        
        //if the link has a forward slash in the start(relative link), add scheme and host
        if (substr($l, 0, 1)=='/' && substr($l, 0, 2) !="//") { 
            $l=parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
        } 
        //if the link has two forward slashes in the start(link missing scheme word), add scheme
        else if (substr($l, 0, 2) =="//") {
            $l=parse_url($url)["scheme"].":".$l;
        } 
        //if the link has ./ in start, add its path from hostname to parent directory
        else if (substr($l, 0, 2) =="./") {
            $l=parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
        } 
        //if the link points to a tag on the page, add path of the page
        else if (substr($l, 0, 1) =="#") {
            $l=parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
        } 
        //if the link has ../ in the start, add path 
        else if (substr($l, 0, 3) =="../") {
            $l=parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
        } 
        //if the link points to a javascript script, ignore
        else if (substr($l, 0, 11)=="javascript:") {
            continue;
        } 
        //if the link points to some page on the host, add scheme and host
        else if (substr($l, 0, 5) !='https' && substr($l, 0, 4) !="http") {
            $l=parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
        }
        
        
        echo $l."<br>";
    }
}
linkFollower($seedURL);


?>