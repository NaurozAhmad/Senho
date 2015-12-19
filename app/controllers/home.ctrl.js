senhoApp.controller('HomeCtrl', function ($log, $scope, apiService) {
	$log.log('Hello from your Controller: HomeCtrl in module main:. This is your controller:', this);
	$scope.sending = false;
	$scope.validation = '';
	$scope.email = {
		email: '',
		message: ''
	};
	$scope.sendMessage = function () {
		console.log('sending message "' + $scope.email.message + '"" from ' + $scope.email.email);
		if ($scope.email.email === '' || $scope.email.message === '') {
			$scope.validation = 'Please fill in your email and your message.';
			swal('', 'Please fill in your email and message.', 'info');
			console.log($scope.validation);
		} else {
			$scope.validation = '';
			$scope.sending = true;
			$scope.promise = apiService.sendEmail($scope.email);
			$scope.promise.then(function (data) {
				if (data.data === "1") {
					swal('Thank you!', 'We\'ll get back to you soon.', 'success');
					$scope.email.email = '';
					$scope.email.message = '';
				} else {
					swal('Sorry...', 'Something went wrong with the server. Please try again.', 'error');
				}
				console.log(data.data);
				$scope.sending = false;
			})
		};
	}
});
