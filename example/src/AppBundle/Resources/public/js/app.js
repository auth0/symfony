angular.module( 'sample', [
  'auth0.lock',
  'ngRoute',
  'sample.home',
  'sample.login',
  'angular-storage',
  'angular-jwt'
])
.config( function myAppConfig ( $routeProvider, lockProvider, $httpProvider, $locationProvider,
  jwtInterceptorProvider) {
  $routeProvider
    .when( '/', {
      controller: 'HomeCtrl',
      templateUrl: '/bundles/app/home/home.html',
      pageTitle: 'Homepage',
      requiresLogin: true
    })
    .when( '/login', {
      controller: 'LoginCtrl',
      templateUrl: '/bundles/app/login/login.html',
      pageTitle: 'Login'
    });


  lockProvider.init({
    domain: AUTH0_DOMAIN,
    clientID: AUTH0_CLIENT_ID,
    loginUrl: '/login'
  });
  
  jwtInterceptorProvider.tokenGetter = function(store) {
    return store.get('token');
  }

  // Add a simple interceptor that will fetch all requests and add the jwt token to its authorization header.
  // NOTE: in case you are calling APIs which expect a token signed with a different secret, you might
  // want to check the delegation-token example
  $httpProvider.interceptors.push('jwtInterceptor');
}).run(function($rootScope, lock, store, jwtHelper, $location) {
    $rootScope.$on('$locationChangeStart', function () {
      var token = store.get('token');
      if (token) {
        if (!jwtHelper.isTokenExpired(token)) {
          lock.getProfile(token, function (error, profile) { store.set('profile', profile); });
        } else {
          $location.path('/login');
        }
      } else {
        $location.path('/login');
      }

    });
    
    lock.on('authenticated', function (authResult) {
      store.set('token', authResult.idToken);
      lock.getProfile(authResult.idToken, function (err, profile) {
        if (err) {
          console.error(err);
          return;
        }
        store.set('profile', profile);
        $location.path("/");
      });
    });
  })
.controller( 'AppCtrl', function AppCtrl ( $scope, $location ) {
  $scope.$on('$routeChangeSuccess', function(e, nextRoute){
    if ( nextRoute.$$route && angular.isDefined( nextRoute.$$route.pageTitle ) ) {
      $scope.pageTitle = nextRoute.$$route.pageTitle + ' | Auth0 Sample' ;
    }
  });
})

;
