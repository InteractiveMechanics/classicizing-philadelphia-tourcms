cms.controller('AddTourController', function(Upload, $scope, $http, $location, $sanitize, $rootScope){
    if (!$rootScope.user) {
        $location.path('/login');
    }

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
        var promise = $http.post('/tours-cms/api/tours', $scope.tour);
        promise.success(function(data, status, headers, config){
            if (status == 200){
		        console.log("Tour created.");
                $location.path('/tours');
            } else {
				console.log("Unable to create the tour.");
            }
        });
        promise.error(function(data, status, headers, config){
            console.log("Unable to create the tour.");
        });
    };
});