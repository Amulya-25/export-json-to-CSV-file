<?php
/*----------------------------
PASS YOUR WALL_ID , APP_ID , APP_SECRET
------------------------------*/
$wall_id= "12077";
$app_id= "f0afca337586413cae1e68689d5f50b5";
$app_secret= "abb10a6046d145b0be5e1d417a7f686b";
/*----------------------------------
  FILE NAME TO STORE JSON DATA 
-------------------------------------*/  
$filename="new_json.csv";
//Guzzlehttp handler
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
try{ 
    //CREATE CSV FILE
    $output=fopen($filename,"w");
    //MAX ITERATION to get total no. of posts
    $max_iteration=34;
    // Base URI is used with relative requests
    $client = new Client(['base_uri' => 'https://www.socialmediawall.io','timeout'  => 10.0,]); 
    $offset=0;
    $header = false;
    //Iterations to get request
    for($iteration_index=0;$iteration_index<$max_iteration;$iteration_index++){
       try{ 
            //API URL
            $url="https://www.socialmediawall.io/api/v1.1/".$wall_id."/posts/?app_id=".$app_id."&app_secret=".$app_secret."&limit=30&offset=".$offset;
            //request to get REST API
            $response = $client->get($url );
        }
        catch(RequestException $e){
            echo "CHECK TIME OUT ";
            exit();
        }
        $code = $response->getStatusCode();
        if($code!=200){
            exit();
        }
        $body = $response->getBody();

        $content=$response->getBody()->getContents();

        $json=json_decode($content);
        if(!isset($json->data)){
            exit();
        }
        
        if(!isset($json->data->posts)){
            exit();
        }
        
        $json_objects =$json->data->posts;

        if(!isset($json->data->hasMore)){
            exit();
        }
        $hasMore=$json->data->hasMore;
        
        $max_post=count($json_objects );
        
        //insert json data to csv file
        for($Post_index=0;$Post_index<$max_post;$Post_index++){
            $postid="'".$json_objects[$Post_index]->postid."'";
            $sourcelink="'".addslashes($json_objects[$Post_index]->sourcelink)."'";
            $text="'".$json_objects[$Post_index]->text."'";
            $txt=str_replace("\n"," ",$text);
            $imagelink="'".addslashes($json_objects[$Post_index]->imagelink)."'";
            $creationtime="'".addslashes($json_objects[$Post_index]->creationtime)."'";
            $line= array($postid,$sourcelink,$txt,$imagelink,$creationtime);
            if (empty($header)){
                 $header=array("postid","sourcelink","text","imagelink","creationtime");
                 fputcsv($output, $header);
            } 
            fputcsv($output,  $line);
        }
        if($hasMore=='true'){ 
            $offset+=30;
        }
        else{
            exit();
            echo "NO MORE POSTS";      
        }
    }
    
 }
catch(RequestException $e){
    echo "Something went bad!!! CHECK TIME OUT";
}

?>