<?php
  session_start();

  if (!isset($_SESSION["badgeIssued"])) {
    header("Location: http://www.graememcc.co.uk/badger/index.php");
    exit();
  }
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Coding contribution badges: Congratulations!</title>
    <meta charset="utf-8" />
    <link type="text/css" rel="stylesheet" href="css/style.css" />
    <script type="application/javascript" src="http://beta.openbadges.org/issuer.js"></script>
    <script type="application/javascript">
      function onClick(e) {
        e.preventDefault();
        var assertionURL = "http://www.graememcc.co.uk/badger/<?php echo $_SESSION["badgeIssued"]; ?>";
        OpenBadges.issue([assertionURL], issueCallback);
      }

      function issueCallback(errors, successes) {
        // Ignore error and success messages for the moment
      }

      document.addEventListener("load", function() {
        document.removeEventListener("load", arguments.callee, true);
        var button = document.getElementById("addButton");
        button.addEventListener("click", onClick, true);
      }, true);
    </script>
  </head>
  <body>
    <h1>Open Badges for Firefox releases</h1>
    <h2>Congratulations!</h2>
    <p>
      You contributed to Firefox 17 - you're awesome! Here's your badge:
    </p>
    <p>
      <img src="http://www.graememcc.co.uk/badger/images/fx17.png">
    </p>
    <button id="addButton">Add this badge to your backpack</button>
    <p>
      <noscript class="error">You must have Javascript enabled in order to add your badge to the backpack</noscript>
    </p>

    <!-- Twitter goo -->
      <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.graememcc.co.uk/badger" data-text="I earned the &quot;Firefox 17 contributor&quot; badge. Did you?" data-via="graememcc">Tweet</a>
      <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
   </p>
  </body>
</html>
