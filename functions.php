<?php
function getTweets($user, $numTweets) {
  $tweets = array(); $tweetsNew = array();
  
  for($i = 0; $i < (round($numTweets/100, 0, PHP_ROUND_HALF_DOWN) + 1); $i++) {
    $url = "https://twitter.com/statuses/user_timeline/".$user.".json?count=100&page=" . $i;

    if(!$info = @file_get_contents($url, true)) {
     die("Not able to get tweets\n");
    }
    $tweetsNew = json_decode($info);   
    $tweets = array_merge($tweets, $tweetsNew); 
  }

  return array_slice($tweets, 0, $numTweets);
  
}

function outputTweetsForSelect($tweets) {
  $output = ''; 
  foreach($tweets as $item) {
    $output .= '<li>
      <p><input type="checkbox" class="tweet" name="selectedTweets[]" value="'.$item->id_str.'" />
      <img src="'.$item->user->profile_image_url.'">' .$item->text . '<br>
      <span class="created">('.str_replace(' +0000','',$item->created_at).')</span>
      </p>
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
  $var = preg_replace('/(http[\S]+)/i','<a href="$1" target="_blank">$1</a>',$var);
  $var = preg_replace('/(@[\S]+)/i','<a href="http://twitter.com/$1" target="_blank">$1</a>',$var);
  $var = preg_replace('/(#[\S]+)/i','<a href="http://twitter.com/search/$1" target="_blank">$1</a>',$var);
  return $var;
}

function debugArray($var){
  echo "<pre>"; print_r($var ); echo "</pre>"; exit;
}
?>
