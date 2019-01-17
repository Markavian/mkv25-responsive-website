<?php

if (!isset($_SERVER['HTTPS'])) {
  $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  header("Location: $url");
  exit("Redirecting to location: $url");
}

require('./common.php');
require('./autoloader.php');
require('./routes.php');

Router::handleRouting();
