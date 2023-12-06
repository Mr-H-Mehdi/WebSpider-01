<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    






<?php
$seedURL="https://www.google.com";

$crawled=array();
$crawling=array(); 
$count= 04;



/* 
*  function getting url and returning associated html page
*  requires url of the page
*/
function getHtml($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $contents = curl_exec($ch);
    if ($contents === false) {
        $error = curl_error($ch);
        echo $error."<br>". "The resources could not be fetched";
        return;
    }
    curl_close($ch);

    return $contents;
}


/**
 * this function takes a link to return its corresponding details
 * 
 *Requires: @param string $url gives link of website to the function
 *Effect:
 * @return string containing title, decription, keywords and  url in json format(may change later on)
 */
function getDetails($url){
    global $count;

    $count--;

    // $options=array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: HMKsBot/0.01\n", "timeout"=>0.3)) ;
    // //for managing the headers of the files
    // $context=stream_context_create($options);    
    // $content=@file_get_contents($url, false, $context);
    // if ($content==false){
    //     // echo "Error: Link's Content Could Not Be Fetched.";
    //     return;    }
    $doc=new DOMDocument();
    @$doc->loadHTML(getHtml($url));

    //extracting the page's title
    $title = $doc->getElementsByTagName("title")->item(0)->nodeValue;

    // if ($title==="") {
    //     # code...
    //     echo "no title <br>";
    //     return "nothing";
    // }
    
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

    $result = array(
        "Title" => $title,
        "Description" => str_replace("\n", "", $description),
        "Keywords" => $keywords,
        "URL" => $url,
        "Content" => strip_tags($doc->getElementsByTagName("body")->item(0)->nodeValue)
    );
    
    return $result;
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
    global $count;

    
    $options=array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: HMKsBot/0.01\n", "timeout"=>2)) ;
    //for managing the headers of the files
    $context=stream_context_create($options);


    $content=@file_get_contents($url, false, $context);
    
    
    // echo $content."asdf";
    if ($content==false){
        echo "Error: Links Could Not Be Fetched.";
        return;
    }
    $doc=new DOMDocument();
    // echo "Checking111";
    @$doc->loadHTML($content);

    $linkQueue = $doc->getElementsByTagName("a");
    // print_r($linkQueue);

    $allDet = array();

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
        

        $det=getDetails($l);
        array_push($allDet, $det);

        foreach ($det as $key => $value) {
            if($key=="Title"){
                echo  "<h2>".$value."</h2>";
                echo  "<br>";
                continue;
            }
            if($key!="Content"){
                echo  "".$key."====>".$value."";
                echo  "<br>";
            }
            echo "<br>";
        }


        // Encode the array into a JSON formatted string
        $jsonData = json_encode($allDet);

        // Write the JSON string to a .json file
        file_put_contents('data.json', $jsonData);
        
        echo    'Crawling data saved in data.json';
        
        
        //trying to Implement multi layers crawling

        // echo json_decode(getDetails($l), true);
        
        // if (!in_array($l, $crawled)) {
        //     $crawled[]=$l;
        //     $crawling[]=$l;
        //     echo $l."<br>";
        //     // print_r($l."<br>");
        //     // echo "<br><br><br>";
        //     echo getDetails($l)."<br>";
        // }
        // recursive crawling, keeps going to the child link
        // array_shift($crawling);
        
        // foreach($crawling as $site) {
        //     $count--;
        //     linkFollower($site);
        //     if ($count==0) {
        //         return;
        //     }
        // }
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the values from the form
    $url = $_POST["text"];
    $action = $_POST["action"];

    //Lets start crawling
    linkFollower($seedURL);
}



?>



</body>
</html>