<?php
  session_start();

  if (!isset($_SESSION["nobugs"])) {
    header("Location: http://www.graememcc.co.uk/badger/index.php");
    exit();
  }
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Coding contribution badges: Did you miss the train?</title>
    <meta charset="utf-8" />
    <link type="text/css" rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Open Badges for Firefox releases</h1>
    <p class="left">
      It doesn't look like that you commited any new code to Firefox 17.
    </p>
    <p class="left">
      The good news is that it's easy to get involved in Firefox development! Why not visit <a href="http://www.whatcanidoformozilla.org">whatcanidoformozilla.org</a> to find out more?
    </p>
    <p class="left">
      (If you did contribute code to Firefox 17, email graememcc_firefox at graeme-online.co.uk with a link to a bug you fixed, so he can figure out what went wrong!)
    </p>
  </body>
</html>
