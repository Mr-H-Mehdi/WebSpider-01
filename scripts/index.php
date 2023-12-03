<?php
$seedURL="http://localhost/Assignments/WebSpider-01/html/testLinks.html";

$crawled=array();
$crawling=array(); 


/**
 * this function takes a link to return its corresponding details
 * 
 *Requires: @param string $url gives link of website to the function
 *Effect:
 * @return string containing title, decription, keywords and  url in json format(may change later on)
 */
function getDetails($url){
    $options=array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: HMKsBot/0.01\n")) ;
    //for managing the headers of the files
    $context=stream_context_create($options);
    
    $content=@file_get_contents($url, false, $context);
    if ($content==false){
        // echo "Error: Link's Content Could Not Be Fetched.";
        return;
    }
    $doc=new DOMDocument();
    @$doc->loadHTML($content);

    //extracting the page's title
    $title = $doc->getElementsByTagName("title")->item(0)->nodeValue;
    if ($title==="") {
        # code...
        echo "no title <br>";
        return "nothing";
    }
    //extracting description and keywords from meta tags
    $description= "";
    $keywords= "";
    $metas= $doc->getElementsByTagName("meta");
    for( $i= 0; $i < $metas->length; $i++ ){
        $meta = $metas->item($i);
        if($meta->getAttribute("name") == strtolower("description")){
            $description=$meta->getAttribute("content");
        }
        if($meta->getAttribute("name") == strtolower("keywords")){
            $keywords=$meta->getAttribute("content");
        }
    }

    return '{"Title": "'.$title.'", "Description": "'.str_replace("\n", "", $description).'", "Keywords" : "'.$keywords.'", "URL": "'.$url.'"}';
}


/**
 * this function takes a link as seed to display its child link details with recursive calls
 * 
 * Requires: @param string $url gives link of seed URL to the function
 * Effect: displays the results of crawling in the form of json array
 * @return nothing
 */
function linkFollower($url){
    global $crawled;
    global $crawling;

    $options=array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: HMKsBot/0.01\n")) ;
    //for managing the headers of the files
    $context=stream_context_create($options);

    $content=@file_get_contents($url, false, $context);
    if ($content==false){
        echo "Error: Links Could Not Be Fetched.";
        return;
    }
    $doc=new DOMDocument();
    $doc->loadHTML($content);

    $linkQueue = $doc->getElementsByTagName("a");
    foreach ($linkQueue as $link) {
        # code...
        $l = @$link->getAttribute("href");

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
        
        if (!in_array($l, $crawled)) {
            $crawled[]=$l;
            $crawling[]=$l;
            echo $l."<br>";
            // print_r($l."<br>");
            // echo "<br><br><br>";
            echo getDetails($l)."<br>";
        }
        //recursive crawling, keeps going to the child link
        array_shift($crawling);
        foreach($crawling as $site) {
            linkFollower($site);
        }
    }
}
linkFollower($seedURL);


?>