<?php
  session_start();
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Coding contribution badges: POC</title>
    <meta charset="utf-8" />
    <link type="text/css" rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Open Badges for Firefox releases</h1>
    <p>
      This is a proof-of-concept for issuing badges recognising contributions to a particular Firefox release.
    </p>

<?php

  function outputError($errorVar, $spanText) {
    if (isset($_SESSION[$errorVar])) {
      echo '<span class="error">' . $spanText . "</span>";
      unset($_SESSION[$errorVar]);
    }
  }

  outputError("empty", "Please enter an email address");
  outputError("emailError", "Please enter a valid email address");
  outputError("bzError", "Bugzilla error");
  outputError("jsonError", "JSON error");
  outputError("fileError", "File error");
  unset($_SESSION["badgeIssued"]);
  unset($_SESSION["nobugs"]);
  unset($_SESSION["expiry"]);

?>
    <form action="issuer.php" method="post" id="theform">
      <label for="bugmail">Enter your bugmail:</label>
      <input type="email" id="bugmail" name="bugmail">
      <input type="submit" id="submit" value="Submit">
    </form>
  </body>
</html>
