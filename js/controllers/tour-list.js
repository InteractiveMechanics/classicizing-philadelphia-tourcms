cms.controller('TourListController', function($scope, $http, $location, $rootScope){
    if (!$rootScope.user) {
        $location.path('/login');
    }

    $http
        .get('/tours-cms/api/tours')
        .success(function(response) {
            $scope.tours = response;
    });

    $scope.editTour = function() {
        var that = angular.element(this);
        var tid  = that[0].tour.tid;

        $location.path('/tours/edit/' + tid);
    };

    $scope.deleteTour = function() {
        var that = angular.element(this);
        var tid  = that[0].tour.tid;

        var promise = $http.delete('/tours-cms/api/tours/' + tid);
        promise.success(function(data, status, headers, config){
            if (status == 200){
		        console.log("Tour deleted.");
                $scope.tours.splice($scope.tours.indexOf(that[0].tour), 1)
            } else {
				console.log("Unable to delete the tour.");
            }
        });
    };
});