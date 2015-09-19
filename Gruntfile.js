module.exports = function(grunt) {
	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			my_target: {
				files: {
					'js/app.min.js': [
						'js/**/*.js',
						'!js/app.min.js'
					]
				}
			}
		},
		concat: {
			dist: {
				src: ['index-header.html', 'templates/*.html', 'templates/**/*.html', 'index-footer.html'],
				dest: 'index.html',
			},
		},
		watch: {
			scripts: {
				files: ['css/*.*', 'js/*.*', '!js/*.min.js', '!css/*.min.css', 'templates/*.html', 'templates/**/*.html', '*.html'],
				tasks: ['concat', 'uglify', 'cssmin'],
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

	grunt.registerTask('default', ['concat', 'cssmin', 'watch']);
};