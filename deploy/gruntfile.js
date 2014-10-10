module.exports = function(grunt) {
	
	// ftp host
	var FTP_HOST = 'ftp.mkv25.net';
	var FTP_USER_LIVE = 'mkv25-live'
	var FTP_USER_STAGE = 'mkv25-stage'
	
	// files to upload and exclude
	var FTP_LOCAL_FOLDER = "../";
	var FTP_DEST_FOLDER = "";
	var FTP_EXCLUSIONS = ['.ftp*', '.git*', '.hta*', 'deploy', '*.fdproj', 'tasklist.md'];

	// load plugins
	grunt.loadNpmTasks('grunt-ftp-deploy');
	grunt.loadNpmTasks('grunt-debug-task');

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		"ftp-deploy": {
			stage: {
				auth: {
					host: FTP_HOST,
					port: 21,
					authKey: FTP_USER_STAGE
				},
				src: FTP_LOCAL_FOLDER,
				dest: FTP_DEST_FOLDER,
				exclusions: FTP_EXCLUSIONS
			},
			live: {
				auth: {
					host: FTP_HOST,
					port: 21,
					authKey: FTP_USER_LIVE
				},
				src: FTP_LOCAL_FOLDER,
				dest: FTP_DEST_FOLDER,
				exclusions: FTP_EXCLUSIONS
			}
		}
	});
	
	// Default task(s).
	grunt.registerTask('stage', ['ftp-deploy:stage']);
	grunt.registerTask('release', ['ftp-deploy:live']);
}
