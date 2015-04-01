<?php

// Secret environment config template
// Should be named: "secret.config.php"

Environment::register('CLEF_AUTH',
	Array(
		"appId" => false,
		"appSecret" => false
	)
);

Environment::register('SQL_CONNECTION_DETAILS',
	Array(
		'host',
		'database',
		'username',
		'password'
	)
);

Environment::register('TWITTER', 
	Array(
		"CONSUMER_KEY" => false,
		"CONSUMER_SECRET" => false,
		"ACCESS_TOKEN" => false,
		"ACCESS_TOKEN_SECRET" => false
	)
);
