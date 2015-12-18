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
	var images;
	return {
		setProjects: function (data) {
			projects = data;
			/*for (var i = 0; i < projects.length; i++) {
				var images = projects[i].images.split(',');
				projects[i].images = JSON.parse(JSON.stringify(images));
				projects[i].images = projects[i].images.sort();
			}*/
		},
		setImages: function (data) {
			images = data;
		},
		getProjects: function () {
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
		getImages: function () {
			var deferred = $q.defer();
			var url = 'http://senhoit.com/get-images.php';
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
		},
		getProjectImages: function (id) {
			var deferred = $q.defer();
			var projectImages = [];
			for (var i = 0; i < images.length; i++) {
				if (id === images[i].p_id) {
					projectImages.push(images[i]);
				}
				if (i === images.length - 1) {
					console.log('Found images: ' + images.length);
					deferred.resolve({
						data: projectImages
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
		$scope.imagesPromise = apiService.getImages();
		$scope.imagesPromise.then(function (data) {
			$scope.images = data.data;
			console.log('Got images from server: ' + $scope.images.length);
			apiService.setImages($scope.images);
		});
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
	if ($scope.$parent.promise) {
		$scope.$parent.promise.then(function () {
			$scope.promise = apiService.getProject(id);
			$scope.promise.then(function (payload) {
				$scope.project = payload.data;
				console.log($scope.project);
			});
		});
	} else {
		$scope.$parent.promise = apiService.getProjects();
		$scope.$parent.promise.then(function () {
			$scope.promise = apiService.getProject(id);
			$scope.promise.then(function (payload) {
				$scope.project = payload.data;
				console.log($scope.project);
			});
		});
	}
	if ($scope.$parent.imagesPromise) {
		$scope.$parent.imagesPromise.then(function () {
			$scope.imagePromise = apiService.getProjectImages(id);
			$scope.imagePromise.then(function (payload) {
				$scope.images = payload.data;
				console.log('scope images: ' + $scope.images.length);
			})
		});
	} else {
		$scope.$parent.imagesPromise = apiService.getImages();
		$scope.$parent.imagesPromise.then(function () {
			$scope.imagePromise = apiService.getProjectImages(id);
			$scope.imagePromise.then(function (payload) {
				$scope.images = payload.data;
				console.log('scope images: ' + $scope.images.length);
			})
		});
	}
});
