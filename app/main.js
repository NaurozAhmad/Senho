var senhoApp = angular.module('senhoApp', ['ui.router', 'ngSanitize']);

senhoApp.config(function ($urlRouterProvider, $stateProvider) {
	$urlRouterProvider.otherwise('/main/home');
	$stateProvider
		.state('main', {
			url: '/main',
			abstract: true,
			templateUrl: 'app/templates/main.html',
			controller: 'MainCtrl as main'
		})
		.state('main.home', {
			url: '/home',
			views: {
				'offCanvasNav': {
					templateUrl: 'app/templates/partials/off-canvas-nav.html'
				},
				'pageContent': {
					templateUrl: 'app/templates/home.html',
					controller: 'HomeCtrl as home'
				}
			}
		})
		.state('main.projects', {
			url: '/projects/:projectId',
			views: {
				'pageContent': {
					templateUrl: 'app/templates/project.html',
					controller: 'ProjectCtrl as project'
				}
			}
		});
});
