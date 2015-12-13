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
