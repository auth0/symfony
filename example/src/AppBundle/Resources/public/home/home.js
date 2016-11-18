angular.module( 'sample.home', [
'auth0.lock'
])
.controller( 'HomeCtrl', function HomeController( $scope, lock, $http, $location, store ) {
  $scope.profile = store.get('profile');

  $scope.callApi = function() {
    // Just call the API as you'd do using $http

    $http({
      url: 'http://localhost:8000/api/ping',
      method: 'GET'
    }).then(function(response) {
      alert(response.data.status);
    }, function() {
      alert("Ups!");
    });
  }

  $scope.logout = function() {
    store.remove('profile');
    store.remove('token');
    $location.path('/login');
  }

});
