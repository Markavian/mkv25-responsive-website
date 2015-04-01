<?php

// Common environment config

Environment::register('TITLE', 'Common Home');
Environment::register('SHOWCASE_BASE_URL', '//mkv25.net/');
Environment::register('SHOWCASE_ICON_URL', '//mkv25.net/site/icons/');
Environment::register('CLEF_REDIRECT_URL', 'https://mkv25.net/auth/login');

Environment::register('CACHED_CONTENT_DIRECTORY', 'cache');
Environment::register('ARTICLE_CONTENT_DIRECTORY', 'articles');

require('secret.config.php');
