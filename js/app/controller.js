senhoApp.controller('appCtrl', function($log, $scope) {
    
    $scope.offCanvasNav = 'off-canvas-nav.html';
    
    
    $log.log('Hello from your Controller: MainCtrl in module main:. This is your controller:', this);
});
senhoApp.controller('mainCtrl', function($log, $scope) {
	$scope.featured = 'featured.html';
    $scope.figures = 'figures.html';
    $scope.footer = 'footer.html';
    $scope.fullWidthImage = 'full-width-image.html';
    $scope.header = 'header.html';
    $scope.heroImage = 'hero-image.html';
    $scope.imageSlider = 'image-slider.html';
    $scope.intro = 'intro.html';
    $scope.overview = 'overview.html';
    $scope.process = 'process.html';
    $scope.services = 'services.html';
    $scope.signup = 'signup.html';
    $scope.sponsors = 'sponsors.html';
    $scope.statement = 'statement.html';
})
