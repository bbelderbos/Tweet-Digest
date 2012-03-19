<?php
function getTweets($user, $numTweets) {
  // if wrong value, default = 20 
  // up until 200 tweets limit for now to make just one request to twitter
  if(!is_numeric($numTweets)) $numTweets = 20;
  if($numTweets > 200) $numTweets = 200;  
  
  $url = "https://twitter.com/statuses/user_timeline/".$user.".json?count=".$numTweets;

  if(!$info = @file_get_contents($url, true)) {
   die("Not able to get tweets\n");
  }
  $tweets = json_decode($info);
  return $tweets;
  
  // when > 200 is ok to support use the following: 
  /*$tweets = array(); $tweetsNew = array();
  
  for($i = 0; $i < (round($numTweets/100, 0, PHP_ROUND_HALF_DOWN) + 1); $i++) {
    $url = "https://twitter.com/statuses/user_timeline/".$user.".json?count=100&page=" . $i;

    if(!$info = @file_get_contents($url, true)) {
     die("Not able to get tweets\n");
    }
    $tweetsNew = json_decode($info);   
    $tweets = array_merge($tweets, $tweetsNew); 
  }

  return array_slice($tweets, 0, $numTweets);*/
}

function outputTweetsForSelect($tweets) {
  $output = ''; 
  foreach($tweets as $item) {
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
  include 'conn.php';
  $tstamp = time();
  
  if ($stmt = $mysqli->prepare("INSERT INTO stats (user, numTweets, insertDate) VALUES (?, ?, ?);")) {
      $stmt->bind_param("ssi", $user, $numTweets, $tstamp);
      $stmt->execute();
      $stmt->close();
  }

  $mysqli->close;
}
?>
