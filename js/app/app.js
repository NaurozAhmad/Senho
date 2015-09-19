var senhoApp = angular.module('senhoApp', ['ngRoute']);

senhoApp.config(function($routeProvider) {
	$routeProvider
		.when('/main', {
			templateUrl: 'main.html',
			controller: 'mainCtrl'
		})
		.otherwise({
			redirectTo: '/main'
		})
});