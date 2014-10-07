<?php

require('./autoloader.php');

echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Request URL: " . $_SERVER['REQUEST_URI'] . "</p>";

$request = new Request('');

$sink = Request::get('sink', false);
echo "<p>$sink</p>";


Request::Test('/mkv25/', $_SERVER['REQUEST_URI']);