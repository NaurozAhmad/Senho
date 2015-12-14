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

senhoApp.factory('apiService', function ($http, $q, $log, $window) {
	var projects;
	return {
		setProjects: function (data) {
			projects = data;
			for (var i = 0; i < projects.length; i++) {
				var images = projects[i].images.split(',');
				projects[i].images = JSON.parse(JSON.stringify(images));
				projects[i].images = projects[i].images.sort();
			}
		},
		getProjects: function (userId) {
			var deferred = $q.defer();
			var url = 'http://senhoit.com/get-data.php';
			$http.get(url)
				.success(function (response) {
					deferred.resolve({
						data: response
					});
				})
				.error(function (msg, code) {
					deferred.reject(msg);
					$log.error(msg, code);
				});
			return deferred.promise;
		},
		getProject: function (id) {
			var deferred = $q.defer();
			for (var i = 0; i < projects.length; i++) {
				if (id === projects[i].id) {
					console.log('Found');
					deferred.resolve({
						data: projects[i]
					});
				}
			}
			return deferred.promise;
		}
	}
})

senhoApp.controller('HomeCtrl', function ($log, $scope) {
	$log.log('Hello from your Controller: HomeCtrl in module main:. This is your controller:', this);

});

senhoApp.controller('MainCtrl', function ($log, $scope, apiService, $sce) {
	$log.log('Hello from your Controller: MainCtrl in module main:. This is your controller:', this);
	$scope.promise = apiService.getProjects();
	$scope.promise.then(function (data) {
		$scope.projects = data.data;
		console.log('Length is: ' + $scope.projects.length);
		apiService.setProjects($scope.projects);
	});
	$scope.promise.catch(function () {
		console.log('Failed to get projects');
	});
	$scope.renderHTML = function (data) {
		return $sce.trustAsHtml(data);
	};
	$scope.sendMessage = function () {
		$.ajax({
			url: 'includes/contact-form/phpmailer.php',
			type: 'POST',
			data: {
				email: $('#name').val(),
				message: $('#message').val()
			}
		}).done(function (data) {
			alert(JSON.stringify(data));
		});
	}
});

senhoApp.controller('ProjectCtrl', function ($log, $scope, apiService, $stateParams, $q) {
	$log.log('Hello from your Controller: ProjectCtrl in module main:. This is your controller:', this);
	var id = $stateParams.projectId;
	$scope.$parent.promise.then(function () {
		$scope.promise = apiService.getProject(id);
		$scope.promise.then(function (payload) {
			$scope.project = payload.data;
			console.log($scope.project);
		});
	})
});
