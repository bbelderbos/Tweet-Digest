<?php
$mysqli = new mysqli("localhost", "bobbelde_tweets", "dmal@#l?dd%", "bobbelde_tweets");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>
