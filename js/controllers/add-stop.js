cms.controller('AddStopController', function(Upload, $scope, $http, $location, $sanitize, $rootScope){
    if (!$rootScope.user) {
        $location.path('/login');
    }

    $scope.content = [];
    $scope.content[0] = {};
    $scope.content[1] = {};
    $scope.content[2] = {};
    $scope.content[3] = {};
    $scope.content[0].type = 'overview';
    $scope.content[1].type = 'building';
    $scope.content[2].type = 'models';
    $scope.content[3].type = 'architect';

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
                            switch (source){
                                case 'stop':
                                    $scope.stop.fid = data;
                                    break;
                                case 'content[0]':
                                    $scope.content[0].fid = data;
                                    break;
                                case 'content[1]':
                                    $scope.content[1].fid = data;
                                    break;
                                case 'content[2]':
                                    $scope.content[2].fid = data;
                                    break;
                                case 'content[3]':
                                    $scope.content[3].fid = data;
                                    break;
                            }
                        } else {
            				console.log("Unable to add file to database.");
                        }
                    });
                });
            }
        }
    };

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