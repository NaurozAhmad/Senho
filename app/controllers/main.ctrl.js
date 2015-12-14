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
