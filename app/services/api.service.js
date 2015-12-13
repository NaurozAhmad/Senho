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
