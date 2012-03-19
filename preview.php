<?php
include 'functions.php';

if($_POST) {  
  if(empty($_POST['selectedTweets'])) {
    echo "No tweets selected!";
  } else {
    $output = ''; $i = 0;
    foreach($_POST['selectedTweets'] as $id) {
      if($i == 150) break;
      $output .= getTweetHtml($id); 
      $i++;
    }
    if(isset($_POST['js']) && $_POST['js'] == 1) {
      $output .= '<script src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
    }
    echo $output;
  }
}
?>