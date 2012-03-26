<?php
$str = $_POST['str']; 

$words = str_word_count($str, 1, '#');

$hashtags = array();

foreach ($words as $w) {
  if(strstr($w, '#')) $hashtags[strtolower($w)]++;
}

if(empty($hashtags)) {
  echo "No hashtags found in selection";
} else {
  ksort($hashtags);
  foreach ($hashtags as $k=>$v) {
    // echo "$k (x$v)  ";
    echo "$k ";
  }  
}
?>
