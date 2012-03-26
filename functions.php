<?php
function getTweets($user, $numTweets) {
  // if wrong value, default = 20 
  // up until 200 tweets limit for now to make just one request to twitter
  if(!is_numeric($numTweets)) $numTweets = 20;
  if($numTweets > 200) $numTweets = 200;  
  
  $url = "http://twitter.com/statuses/user_timeline/".$user.".json?count=".$numTweets;
  
  // make request
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  $output = curl_exec($ch); 

  // convert response
  $tweets = json_decode($output);

  // handle error; error output
  $info = curl_getinfo($ch);
  curl_close($ch);  
  if($info['http_code'] !== 200) {
    echo "http code returned by Twitter: ".$info['http_code']."<br><br>Dump request: <br>";
    var_dump($output);
    die();
  }
  
  return $tweets;
  
  // when > 200 is ok to support use the following: 
  /*$tweets = array(); $tweetsNew = array();
  
  for($i = 0; $i < (round($numTweets/100, 0, PHP_ROUND_HALF_DOWN) + 1); $i++) {
    $url = "https://twitter.com/statuses/user_timeline/".$user.".json?count=100&page=" . $i;

    if(!$info = @file_get_contents($url, true)) {
     die("Not able to get tweets now (Twitter only allows 150 requests per hour)\n");
    }
    $tweetsNew = json_decode($info);   
    $tweets = array_merge($tweets, $tweetsNew); 
  }

  return array_slice($tweets, 0, $numTweets);*/
}

function outputTweetsForSelect($tweets) {
  $output = ''; 
  foreach($tweets as $item) {
    
    // todo data-in-reply-to
    
    $output .= '<li>
      
        <input type="checkbox" class="tweet" name="selectedTweets[]" value="'.$item->id_str.'" />
        <img src="'.$item->user->profile_image_url.'">
        <div class="tweetToCopy">
          <blockquote class="twitter-tweet"><p>'.makeLinks($item->text).'</p>&mdash; '.$item->user->name.' (@'.$item->user->screen_name.') <a href="https://twitter.com/'.$item->user->screen_name.'/status/'.$item->id_str.'" data-datetime="'.convertDate($item->created_at).'">'.convertDateReadable($item->created_at).'</a></blockquote>
        </div>
      
    </li>';
  }
  return $output;
}




function getTweetHtml($tweetId) {
  $url = "https://api.twitter.com/1/statuses/oembed.json?id=$tweetId&omit_script=true&lang=en";
  if(!$info = @file_get_contents($url, true)) {
   echo "Not able to get tweet content\n";
   return -1;
  }
  $tweetContent = json_decode($info);
  return $tweetContent->html;
}


function makeLinks($var){
  /*
  playing with <a href="https://twitter.com/search/%2523Sencha">#Sencha</a> , more info on release 2 : <a href="http://t.co/WzdAr0MY" title="http://rww.to/yP9qzb">rww.to/yP9qzb</a> via @<a href="https://twitter.com/RWW">RWW</a>
  */
  $var = preg_replace('/(http[\S]+)/i','<a href="$1" title="$1" target="_blank">$1</a>',$var);
  $var = preg_replace('/(@[\S]+)/i','<a href="https://twitter.com/$1" target="_blank">$1</a>',$var);
  $var = preg_replace('/(#[\S]+)/i','<a href="https://twitter.com/search/$1" target="_blank">$1</a>',$var);
  return $var;
}

function debugArray($var){
  echo "<pre>"; print_r($var ); echo "</pre>"; exit;
}

function convertDate($date) {
  $months = array('Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov'=> '11', 'Dec' => '12');
  $fields = explode(' ', $date);

  return "$fields[5]-".$months[$fields[1]]."-$fields[2]T$fields[3]+00:00";
}

function convertDateReadable($date) {
  $months = array('Jan' => 'January', 'Feb' => 'February', 'Mar' => 'March', 'Apr' => 'April', 'May' => 'May', 'Jun' => 'June', 'Jul' => 'July', 'Aug' => 'August', 'Sep' => 'September', 'Oct' => 'October', 'Nov'=> 'November', 'Dec' => 'December');
  $fields = explode(' ', $date);

  return $months[$fields[1]]." $fields[2], $fields[5]";
}


function insertStat($user, $numTweets){  
  include '../../includes/conn_tweetDigest.php';
  $tstamp = time();
  
  if ($stmt = $mysqli->prepare("INSERT INTO stats (user, numTweets, insertDate) VALUES (?, ?, ?);")) {
      $stmt->bind_param("ssi", $user, $numTweets, $tstamp);
      $stmt->execute();
      $stmt->close();
  }

  $mysqli->close;
}



// yql - does this have a rate limit ?!!

function getTweetsViaYql($user, $numTweets) {
  $query = "select created_at,from_user_id_str,from_user,from_user_name,text,id_str,profile_image_url,text,in_reply_to_status_id_str from twitter.search($numTweets) where q='from:$user'";
  $url = "http://query.yahooapis.com/v1/public/yql?q=";
  $url .= rawurlencode($query);
  $url .= "&format=json&env=store://datatables.org/alltableswithkeys";

  $json = get_data($url);
  $info = json_decode($json, true) ;
  return $info;
}

// from: http://davidwalsh.name/download-urls-content-php-curl
function get_data($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}


function outputTweetsForSelectYql($tweets) {
  $output = ''; 
  foreach($tweets['query']['results']['results'] as $tweet) {
    
    // todo data-in-reply-to
    
    $output .= '<li>
      
        <input type="checkbox" class="tweet" name="selectedTweets[]" value="'.$tweet['id_str'].'" />
        <img src="'.$tweet['profile_image_url'].'">
        <div class="tweetToCopy">
          <blockquote class="twitter-tweet"';
    
    if(isset($tweet['in_reply_to_status_id_str'])) $output .= 'data-in-reply-to="'.$tweet['in_reply_to_status_id_str'].'"';
    
    $output .= '><p>'.makeLinks($tweet['text']).'</p>&mdash; '.$tweet['from_user_name'].' (@'.$tweet['from_user'].') <a href="https://twitter.com/'.$tweet['from_user'].'/status/'.$tweet['id_str'].'" data-datetime="'.convertDate($tweet['created_at']).'">'.convertDateReadable($tweet['created_at']).'</a></blockquote>
        </div>
      
    </li>';
  }
  return $output;
}


?>
