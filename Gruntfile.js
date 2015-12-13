module.exports = function (grunt) {
	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		concat: {
			dist: {
				files: {
					'app/app.js': ['app/main.js', 'app/services/*.js', 'app/controllers/*.js']
				}
			},
		},
		watch: {
			scripts: {
				files: ['css/*.*', 'app/**/*.*', '!app/app.js', 'index.html'],
				tasks: ['concat'],
				options: {
					interrupt: true,
					livereload: {
						port: 1337
					}
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'css/main.min.css': [
						'css/skeleton/css/*.css',
						'css/font-awesome/css/font-awesome.min.css',
						'css/style.css'
					]
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	grunt.registerTask('default', ['concat', 'watch']);
};
