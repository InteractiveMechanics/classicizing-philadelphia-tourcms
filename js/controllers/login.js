cms.controller('LoginController', function($scope, $rootScope, $location){
    if ($rootScope.user) {
        $location.path('/');
    }

    $scope.error;

    $scope.authenticate = function (){
        if ($scope.loginEmail){
            if (validateEmail($scope.loginEmail)) {
                localStorage.setItem('email', $scope.loginEmail);
                $rootScope.user = $scope.loginEmail;
                $location.path('/dashboard');
            } else {
                $scope.error = 'Invalid email address or password.';
            }
        } else {
            $scope.error = 'Please enter a valid email address.';
        }
    }

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
});