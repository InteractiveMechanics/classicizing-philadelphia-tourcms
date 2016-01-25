cms.controller('LogoutController', function($scope, $rootScope, $location){
    localStorage.removeItem('email');
    $rootScope.user = false;
    $location.path('/login');
});