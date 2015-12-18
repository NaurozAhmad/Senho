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
		$scope.$parent.promise.then(function () {
			$scope.$parent.imagesPromise = apiService.getImages();

			$scope.$parent.imagesPromise.then(function () {
				$scope.imagePromise = apiService.getProjectImages(id);
				$scope.imagePromise.then(function (payload) {
					$scope.images = payload.data;
					console.log('scope images: ' + $scope.images.length);
				})
			});
		});
	}
});
