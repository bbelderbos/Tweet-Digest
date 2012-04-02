<?php
include 'functions.php';
include 'header.html';

echo '<div id="twitter">'; // gets closed in footer.html
  if(!$_GET){
    include 'form.html';
    include 'footer.html';
  }

  $user = $_GET['user'];
  $numTweets = $_GET['numTweets'];
  if(!is_numeric($numTweets)) $numTweets = 20;

  // write stats
  insertStat($user, $numTweets);
  
  // get tweets
  // a. via search.twitter (rate limit !)
  // $tweets = getTweets($user, $numTweets);
  // b. via yal
  //$tweets = getTweetsViaYql($user, $numTweets);
  // c. via api.twitter / xml to get RTs
  $tweets = getTweetsXml($user, $numTweets);
  
  //debugArray($tweets);
  
  echo '<form method="POST" id="returnHTML" action="index.php">
  <ul>
    <li><h2>Choose Tweets for Digest</h2></li>
    <li><input id="selectAll" type="checkbox" onclick="toggleChecked(this.checked)"> Select all <div id="counter">0 tweets in Tweet Digest&nbsp;|&nbsp;<a href="index.php">Change @user &amp; # of tweets</a></div></li>';
  
  // a. via search.twitter (rate limit !)
  // echo '<div id="tweets">'. outputTweetsForSelect($tweets) .'</div>';
  // b. via yal
  //echo '<div id="tweets">'. outputTweetsForSelectYql($tweets) .'</div>';
  // c. via xml
  echo '<div id="tweets">'. outputTweetsForSelectXml($tweets) .'</div>';
  
  echo '</ul>
  </form>';    

echo '<div id="loader"></div>';

echo "<div id='preview'>
    <div id='previewHeader'></div>
    <div id='embeddedTweets'></div>
      
    <div id='codeWrapperDiv'>
      <a href='#' id='copyHtml'>Copy HTML to clipboard</a><br>
      <div id='codeWrapper' contenteditable='true'></div>  
    
      <p>
        <br><br>
        <input type='checkbox' class='ckBoxStyle' id='includeJs' value='1' />
         Include Javascript (<a href='img/preview_javascript.png' title='see example' 
          target='_blank'>nicer look</a> but more KBs)

        <br><a href='#' id='copyJs'>Copy JS to clipboard</a><br>
        <textarea id='jsLib'><script src='//platform.twitter.com/widgets.js'></script></textarea>
        
        <br><br>
        <input type='checkbox' class='ckBoxStyle' id='hashTags' value='1' />
         Include a summary of Hash tags (#) found in your selected tweets

        <br><a href='#' id='copyTags'>Copy Hashtags to clipboard</a><br>
        <textarea id='hTag'></textarea> 
        
      </p>
      
    </div>
  </div><!-- end preview -->";

include 'footer.html';
?>