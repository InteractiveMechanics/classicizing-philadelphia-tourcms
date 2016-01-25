cms.controller('DashboardController', function($scope, $rootScope, $location){
    if (!$rootScope.user) {
        $location.path('/login');
    }
});