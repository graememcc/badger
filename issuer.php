<?php
  session_start();

  function redirectWithError($errorVar) {
    $_SESSION[$errorVar] = true;
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();
  }

  function createURL($prefix, $email) {
    return $prefix
           . "?field0-0-0=resolution&type0-0-0=equals&value0-0-0=fixed"                  // Restrict search to fixed bugs AND
           . "&field1-0-0=assigned_to&type1-0-0=equals&value1-0-0=" . urlencode($email)  // Restrict to user in question AND
           . "&field2-0-0=target_milestone&type2-0-0=equals&value2-0-0=mozilla17"        // (Bug was fixed in mozilla17 OR
           . "&field2-0-1=target_milestone&type2-0-1=equals&value2-0-1=Firefox%2017"     // Bug was fixed in Firefox 17 OR
           . "&field2-0-2=cf_status_firefox17&type2-0-2=equals&value2-0-2=fixed";        // status-firefox17=fixed)
  }

  function gen_salt() {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!_-+=*0123456789";
    $size = strlen($chars);
    $salt = "";

    for ($i = 0; $i < 20; $i++) {
      $salt .= $chars[rand(0, $size - 1)];
    }

    return $salt;
  }

  if (empty($_POST["bugmail"])) {
    redirectWithError("empty");
  }

  if (!(filter_var($_POST["bugmail"], FILTER_VALIDATE_EMAIL))) {
    redirectWithError("bugmail");
  }

  if ($_POST["bugmail"] === "nobody@mozilla.org") {
    redirectWithError("nobody");
  }

  $bugmail = $_POST["bugmail"];
  $bugzilla = "https://bugzilla.mozilla.org/buglist.cgi";
  $api = "https://api-dev.bugzilla.mozilla.org/latest/bug";

  $file = file_get_contents(createURL($api, $bugmail) . "&include_fields=id");

  if (!$file) {
    redirectWithError("bzError");
  }

  $data = json_decode($file, true);

  if (!$data or !(isset($data["bugs"]))) {
    redirectWithError("jsonError");
  }

  $badgeFile = "assertions_issued/" . preg_replace("/@/", "", preg_replace("/\./", "_", $bugmail)) . ".json";
  if (file_exists($badgeFile)) {
    // If the file already exists and is still valid, go straight to badge issued
    // (perhaps the earner has removed the badge from their backpack and wants to restore it)
    $contents = file_get_contents($badgeFile);
    if ($contents === false) {
      redirectWithError("fileError");
    }

    $existing = json_decode($contents, true);
    if (!$existing) {
      redirectWithError("jsonError");
    }

    if (isset($existing["expires"])) {
      $existingExpiry = new DateTime($existing["expires"]);
      $now = new DateTime();
      if ($existingExpiry > $now) {
        $_SESSION["badgeIssued"] = $badgeFile;
        $_SESSION["expiry"] = $existingExpiry->format("F jS");
        header("Location: http://www.graememcc.co.uk/badger/badgeIssued.php");
        exit();
      }
    }

    // If we couldn't find an expiry date, or the badge is expired, we shall
    // issue a new badge with a fresh expiration date
    unlink($badgeFile);
  }

  if (count($data["bugs"]) === 0) {
    $_SESSION["nobugs"] = true;
    header("Location: http://www.graememcc.co.uk/badger/encourage.php");
    exit();
  }

  $handle = fopen($badgeFile, "w");
  if ($handle === NULL) {
    redirectWithError("fileError");
  }

  $salt = gen_salt();

  // Badge should expire in 28 days
  $interval = new DateInterval("P28D");
  $expiryDate = new DateTime();
  $expiryDate->add($interval);
  $_SESSION["expiry"] = $expiryDate->format("F jS");

  $badgeData = array(
                 "version" => "1.0.0",
                 "name" => "Firefox 17 Contributor (proof of concept)",
                 "image" => "http://www.graememcc.co.uk/badger/images/fx17.png",
                 "description" => "Contributed new code to Firefox 17",
                 "criteria" => "http://www.graememcc.co.uk/badger/badgeCriteria.html",
                 "issuer" => array(
                               "origin" => "http://www.graememcc.co.uk/",
                               "name" => "graememcc"));

  $contributorData = array(
                       "recipient" => "sha256$" . hash("sha256", $bugmail . $salt),
                       "salt" => $salt,
                       "evidence" => createURL($bugzilla, $bugmail),
                       "expires" => $expiryDate->format("Y-m-d"),
                       "badge" => $badgeData);

  if (fwrite($handle, json_encode($contributorData)) === false) {
    fclose($handle);
    redirectWithError("fileError");
  }

  fclose($handle);

  $_SESSION["badgeIssued"] = $badgeFile;
  header("Location: http://www.graememcc.co.uk/badger/badgeIssued.php");
  exit();
?>
