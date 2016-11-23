angular.module( 'sample.login', [
  'auth0.lock'
])
.controller( 'LoginCtrl', function HomeController( $scope, lock, $location, store ) {
  $scope.lock = lock;

});
