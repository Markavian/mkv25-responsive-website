module.exports = function(grunt) {

	// ftp host
	var FTP_HOST = 'ftp.mkv25.net';
	var FTP_USER_LIVE = 'mkv25-live'
	var FTP_USER_STAGE = 'mkv25-stage'

	// files to upload and exclude
	var FTP_LOCAL_FOLDER = "../";
	var FTP_DEST_FOLDER = "";
	var FTP_EXCLUSIONS_COMMON = ['.ftp*', '.git*', '.hta*', 'deploy', '*.fdproj', 'tasklist.md', 'readme.md', '.sublime'];

	// auth details for stage
	var FTP_STAGE_AUTH = {
		host: FTP_HOST,
		port: 21,
		authKey: FTP_USER_STAGE
	};

	// auth details for live
	var FTP_LIVE_AUTH = {
		host: FTP_HOST,
		port: 21,
		authKey: FTP_USER_LIVE
	};

	// load plugins
	grunt.loadNpmTasks('grunt-ftp-deploy');
	grunt.loadNpmTasks('grunt-debug-task');

	function ftpStageConfigFor(path, exclusions) {
        exclusions = exclusions || [];
		return {
			auth: FTP_STAGE_AUTH,
			src: FTP_LOCAL_FOLDER + path,
			dest: FTP_DEST_FOLDER + path,
			exclusions: [].concat(FTP_EXCLUSIONS_COMMON, exclusions),
			forceVerbose: true
		};
	}

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		"ftp-deploy": {
			"stage-articles": ftpStageConfigFor('site/articles/'),
			"stage-projects": ftpStageConfigFor('site/projects/'),
			"stage-content": ftpStageConfigFor('site/content/'),
			"stage-images": ftpStageConfigFor('site/images/'),
			"stage-php": ftpStageConfigFor('site/php/', ['external']),
			"stage-php-lib": ftpStageConfigFor('site/php/lib'),
			"stage-scripts": ftpStageConfigFor('site/scripts/'),
			"stage-stylesheets": ftpStageConfigFor('site/stylesheets/'),
			"stage-templates": ftpStageConfigFor('site/templates/'),
			"live": {
				auth: FTP_LIVE_AUTH,
				src: FTP_LOCAL_FOLDER,
				dest: FTP_DEST_FOLDER,
				exclusions: FTP_EXCLUSIONS_COMMON,
				forceVerbose: true
			}
		}
	});

	// Default task(s).
	grunt.registerTask('stage-php', ['ftp-deploy:stage-php']);
	grunt.registerTask('stage-php-lib', ['ftp-deploy:stage-php-lib']);
	grunt.registerTask('stage-scripts', ['ftp-deploy:stage-scripts']);
	grunt.registerTask('stage-stylesheets', ['ftp-deploy:stage-stylesheets']);
	grunt.registerTask('stage-templates', ['ftp-deploy:stage-templates']);

	grunt.registerTask('stage-content', ['ftp-deploy:stage-content']);
	grunt.registerTask('stage-articles', ['ftp-deploy:stage-articles']);
	grunt.registerTask('stage-projects', ['ftp-deploy:stage-projects']);
	grunt.registerTask('stage-images', ['ftp-deploy:stage-images']);

	grunt.registerTask('stage-code', ['stage-php', 'stage-scripts', 'stage-stylesheets', 'stage-templates']);
	grunt.registerTask('stage-all', ['stage-code', 'stage-content', 'stage-articles', 'stage-projects', 'stage-images']);

	grunt.registerTask('release', ['ftp-deploy:live']);
}
