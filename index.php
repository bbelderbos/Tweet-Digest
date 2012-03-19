<?php
include 'functions.php';
include 'header.html';

echo '<div id="twitter">'; 
  if(!$_GET){
    include 'form.html';
    include 'footer.html';
  }

  $user = $_GET['user'];
  $numTweets = $_GET['numTweets'];
  $tweets = getTweets($user, $numTweets);
  //debugArray($tweets);
  
  echo '<form method="POST" id="returnHTML">
  <ul>
    <li><h2>Choose Tweets for Digest (max. 150)</h2></li>
    <li><input id="selectAll" type="checkbox" onclick="toggleChecked(this.checked)"> Select all <div id="counter">0 tweets in Tweet Digest</div></li>
    <div id="tweets">'. outputTweetsForSelect($tweets) .'</div>
    <li><p><input id="generateHtml" type="submit" class="submit" value="Digest Tweets!">
      <input type="checkbox" id="includeJs" name="includeJs" value="1" /> 
      <small>Include Javascript (<a href="img/preview_javascript.png" title="see example" target="_blank">nicer look</a> but more KBs than <a href="img/preview_text.png" title="see example" target="_blank">plain text</a>)</small>
      <a id="goback" href="index.php">Change user/ # tweets</a>
      </p>
    </li>
  </ul>
  </form>';    

echo '<div id="loader"></div>';

echo '</div>';

echo "<div id='preview'>
  <div id='previewHeader'></div>
  <div id='embeddedTweets'></div>
  
  <input type='submit' id='copy-button' class='submit' value='Copy HTML' /><br><br>
  <div id='codeWrapper' contenteditable='true'></div>
  </div>";

include 'footer.html';
?>