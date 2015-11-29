<?php
function getTweets($user, $numTweets) {
  require_once('TwitterAPIExchange.php');
  require_once('config.php');
  $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
  $getfield = "?screen_name=$user&count=$numTweets";
  $requestMethod = 'GET';
  $twitter = new TwitterAPIExchange($settings);
  $response = $twitter->setGetfield($getfield)
      ->buildOauth($url, $requestMethod)
      ->performRequest();
  return json_decode($response);
}

function outputTweets($tweets) {
  $output = ''; 
  foreach($tweets as $item) {
    $output .= '<li>
      <input type="checkbox" class="tweet" name="selectedTweets[]" value="'.$item->id.'" />
      <img src="'.$item->user->profile_image_url.'">
      <div class="tweetToCopy">
        <blockquote class="twitter-tweet"><p>'.makeLinks($item->text).'</p>&mdash; '.$item->user->name.' (@'.$item->user->screen_name.') <a href="https://twitter.com/'.$item->user->screen_name.'/status/'.$item->id.'" data-datetime="'.convertDate($item->created_at).'">'.convertDateReadable($item->created_at).'</a></blockquote>
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
?>
