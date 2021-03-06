var cms = angular.module('cms', ['ngRoute', 'appControllers', 'checklist-model', 'ngSanitize', 'ngFileUpload', 'ui.bootstrap', 'textAngular']);
var appControllers = angular.module('appControllers', []);

cms.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider){
    $routeProvider
        .when('/', {
            templateUrl: 'js/views/dashboard.html',
            controller: 'DashboardController'
        })

        .when('/tours', {
            templateUrl: 'js/views/tours.html',
            controller: 'TourListController'
        })
        .when('/tours/add', {
            templateUrl: 'js/views/edit-tour.html',
            controller: 'AddTourController'
        })
        .when('/tours/edit/:sid', {
            templateUrl: 'js/views/edit-tour.html',
            controller: 'EditTourController'
        })

        .when('/stops', {
            templateUrl: 'js/views/stops.html',
            controller: 'StopListController'
        })
        .when('/stops/add', {
            templateUrl: 'js/views/edit-stop.html',
            controller: 'AddStopController'
        })
        .when('/stops/edit/:sid', {
            templateUrl: 'js/views/edit-stop.html',
            controller: 'EditStopController'
        })

        .when('/login', {
            templateUrl: 'js/views/login.html',
            controller: 'LoginController'
        })
        .when('/logout', {
            templateUrl: 'js/views/login.html',
            controller: 'LogoutController'
        })
        .otherwise({
            redirectTo: '/'
        });
    $locationProvider.html5Mode(true);
}]);

cms.run(['$rootScope', '$location', function($rootScope, $location){
    var path = function() { return $location.path(); };
    var email = localStorage.getItem('email');
    
    if (email) {
        $rootScope.user = email;
    } else {
        $rootScope.user = false;
    }

    $rootScope.$watch(path, function(newVal, oldVal){
        $rootScope.activetab = newVal;
    });
}]);