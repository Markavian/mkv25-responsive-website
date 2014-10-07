<?php

require('./autoloader.php');

$view = new DefaultView();
$view->responseCode(404, 'File not found, no route set');
$view->routeInfo();
