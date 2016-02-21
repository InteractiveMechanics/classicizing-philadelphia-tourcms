cms.controller('StopListController', function($scope, $http, $location, $rootScope){
    if (!$rootScope.user) {
        $location.path('/login');
    }

    $http
        .get('/tours-cms/api/stops')
        .success(function(response) {
            $scope.stops = response;
    });

    $scope.editStop = function() {
        var that = angular.element(this);
        var sid  = that[0].stop.sid;

        $location.path('/stops/edit/' + sid);
    };

    $scope.deleteStop = function() {
        var that = angular.element(this);
        var sid  = that[0].stop.sid;

        var promise = $http.delete('/tours-cms/api/stops/' + sid);
        promise.success(function(data, status, headers, config){
            if (status == 200){
		        console.log("Stop deleted.");
                $scope.stops.splice($scope.stops.indexOf(that[0].stop), 1)
            } else {
				console.log("Unable to delete the stop.");
            }
        });
    };
});