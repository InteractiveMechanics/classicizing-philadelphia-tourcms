cms.controller('LoginController', function($scope, $rootScope, $location){
    if ($rootScope.user) {
        $location.path('/');
    }

    $scope.authenticate = function (){
        localStorage.setItem('email', $scope.loginEmail);
        $rootScope.user = $scope.loginEmail;
        $location.path('/dashboard');
    }
});