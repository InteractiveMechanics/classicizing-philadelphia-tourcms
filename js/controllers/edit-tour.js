cms.controller('EditTourController', function($scope, $http, $location, $sanitize, Upload, $rootScope){
    if (!$rootScope.user) {
        $location.path('/login');
    }

    var path = $location.path();
    var tid = path.split('/').pop();

    $http
        .get('/tours-cms/api/tours/' + tid)
        .success(function(response) {
            $scope.tour = response[0];
            $scope.tour.stops = JSON.parse($scope.tour.stops);
    });
    $http
        .get('/tours-cms/api/stops')
        .success(function(response) {
            $scope.stops = response;
    });

    $scope.upload = function (files, source) {
        if (files && files.length) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                Upload.upload({
                    url: '/tours-cms/api/upload.php',
                    file: file
                }).success(function (data, status, headers, config) {
                    var fileData  = {
                       'lastModified'     : file.lastModified,
                       'lastModifiedDate' : file.lastModifiedDate,
                       'file_name'        : data,
                       'file_size'        : file.size,
                       'file_type'        : file.type
                    };

                    var promise = $http.post('/tours-cms/api/files', fileData);
                    promise.success(function(data, status, headers, config){
                        if (status == 200){
            		        console.log("File added to database.");
                            $scope.tour.fid = data;
                        } else {
            				console.log("Unable to add file to database.");
                        }
                    });
                });
            }
        }
    };

    $scope.submitTour = function() {
        var promise = $http.put('/tours-cms/api/tours/' + tid, $scope.tour);
        promise.success(function(data, status, headers, config){
            if (status == 200){
		        console.log("Tour updated.");
                $location.path('/tours');
            } else {
				console.log("Unable to update the tour.");
            }
        });
        promise.error(function(data, status, headers, config){
            console.log("Unable to update the tour.");
        });
    };
});