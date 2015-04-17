<?php

Routes::addRoute('subpath/test', 'Home');

Routes::addRoute('home', 'Home');
Routes::addRoute('news', 'News');
Routes::addRoute('games', 'Games');
Routes::addRoute('open-source', 'OpenSource');
Routes::addRoute('forums', 'Forums');
Routes::addRoute('forums/', 'Forums');
Routes::addRoute('store', 'Store');
Routes::addRoute('about', 'About');
Routes::addRoute('history', 'History');
Routes::addRoute('scrapbook', 'Scrapbook');
Routes::addRoute('scrapbook/', 'Scrapbook');
Routes::addRoute('content', 'ContentRenderer');
Routes::addRoute('auth/logout', 'AuthLogout');
Routes::addRoute('auth/login', 'AuthLogin');
Routes::addRoute('auth/signout', 'AuthLogout');
Routes::addRoute('auth/signin', 'AuthLogin');
Routes::addRoute('remote/procedure/', 'RemoteProcedureCall');