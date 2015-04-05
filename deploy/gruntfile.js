module.exports = function(grunt) {
	
	// ftp host
	var FTP_HOST = 'ftp.mkv25.net';
	var FTP_USER_LIVE = 'mkv25-live'
	var FTP_USER_STAGE = 'mkv25-stage'
	
	// files to upload and exclude
	var FTP_LOCAL_FOLDER = "../";
	var FTP_DEST_FOLDER = "";
	var FTP_EXCLUSIONS_COMMON = ['.ftp*', '.git*', '.hta*', 'deploy', '*.fdproj', 'tasklist.md', 'readme.md', '.sublime'];
	var FTP_EXCLUSIONS_IMAGES = FTP_EXCLUSIONS_COMMON.concat(['*.png', '*.jpg', '*.psp']);
	var FTP_EXCLUSIONS_NON_IMAGES = FTP_EXCLUSIONS_COMMON.concat(['*.php', '*.md', '*.css', '*.html']);

	// load plugins
	grunt.loadNpmTasks('grunt-ftp-deploy');
	grunt.loadNpmTasks('grunt-debug-task');

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		"ftp-deploy": {
			"stage-code": {
				auth: {
					host: FTP_HOST,
					port: 21,
					authKey: FTP_USER_STAGE
				},
				src: FTP_LOCAL_FOLDER,
				dest: FTP_DEST_FOLDER,
				exclusions: FTP_EXCLUSIONS_IMAGES,
				forceVerbose: true
			},
			"stage-assets": {
				auth: {
					host: FTP_HOST,
					port: 21,
					authKey: FTP_USER_STAGE
				},
				src: FTP_LOCAL_FOLDER,
				dest: FTP_DEST_FOLDER,
				exclusions: FTP_EXCLUSIONS_NON_IMAGES,
				forceVerbose: true
			},
			"stage-articles": {
				auth: {
					host: FTP_HOST,
					port: 21,
					authKey: FTP_USER_STAGE
				},
				src: FTP_LOCAL_FOLDER + 'site/php/articles/',
				dest: FTP_DEST_FOLDER + 'site/php/articles/',
				forceVerbose: true
			},
			"live": {
				auth: {
					host: FTP_HOST,
					port: 21,
					authKey: FTP_USER_LIVE
				},
				src: FTP_LOCAL_FOLDER,
				dest: FTP_DEST_FOLDER,
				exclusions: FTP_EXCLUSIONS_COMMON,
				forceVerbose: true
			}
		}
	});
	
	// Default task(s).
	grunt.registerTask('stage-code', ['ftp-deploy:stage-code']);
	grunt.registerTask('stage-assets', ['ftp-deploy:stage-assets']);
	grunt.registerTask('stage-articles', ['ftp-deploy:stage-articles']);
	grunt.registerTask('stage-full', ['stage-code', 'stage-assets']);
	grunt.registerTask('release', ['ftp-deploy:live']);
}
