cms.controller('EditStopController', function($scope, $http, $location, $sanitize, Upload){
    var path = $location.path();
    var sid = path.split('/').pop();

    $http
        .get('/tours-cms/api/stops/' + sid)
        .success(function(response) {
            $scope.stop = response[0];
    });
    $http
        .get('/tours-cms/api/stops/' + sid + '/content')
        .success(function(response) {
            $scope.content = response;
    });
    
    $scope.upload = function (files) {
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
                            $scope.stop.fid = data;
                        } else {
            				console.log("Unable to add file to database.");
                        }
                    });
                });
            }
        }
    };

    $scope.submitStop = function() {
        var promise = $http.put('/tours-cms/api/stops/' + sid, $scope.stop);
        promise.success(function(data, status, headers, config){
            if (status == 200){
		        console.log("Stop updated.");
                $location.path('/stops');
            } else {
				console.log("Unable to update the stop.");
            }
        });
        promise.error(function(data, status, headers, config){
            console.log("Unable to update the stop.");
        });
    };
});