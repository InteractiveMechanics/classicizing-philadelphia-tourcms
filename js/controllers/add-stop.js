cms.controller('AddStopController', function($scope, $http, $location, $sanitize){
    $scope.content = [];
    $scope.content[0] = {};
    $scope.content[1] = {};
    $scope.content[2] = {};
    $scope.content[3] = {};
    $scope.content[0].type = 'overview';
    $scope.content[1].type = 'building';
    $scope.content[2].type = 'models';
    $scope.content[3].type = 'architect';

    $scope.submitStop = function() {
        var promise = $http.post('/tours-cms/api/stops', $scope.stop);
        promise.success(function(data, status, headers, config){
            if (status == 200){
                var id = data;

                angular.forEach($scope.content, function(value, key){
                    var promise = $http.post('/tours-cms/api/stops/' + id + '/content', value);
                    promise.success(function(data, status, headers, config){
                        console.log("Stop content created.");
                    });
                });
                
		        console.log("Stop created.");
                $location.path('/stops');
            } else {
				console.log("Unable to create the stop.");
            }
        });
        promise.error(function(data, status, headers, config){
            console.log("Unable to create the stop.");
        });
    };
});