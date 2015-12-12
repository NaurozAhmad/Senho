var senhoApp = angular.module('senhoApp', ['ngRoute']);

senhoApp.config(function($routeProvider) {
	$routeProvider
		.when('/main', {
			templateUrl: 'main.html',
			controller: 'mainCtrl'
		})
		.when('/project', {
			templateUrl: 'project.html'
		})
		.otherwise({
			redirectTo: '/main'
		})
});