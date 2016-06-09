angular.module('app.controllers')
	.controller('ProjectFileEditController', 
		['$scope','$routeParams','$location', 'ProjectFile', 
	function($scope, $routeParams , $location,  ProjectFile) {
	$scope.projectFile = ProjectFile.get({
		id: null,
		idFile: $routeParams.idFile
	});

	$scope.save = function () {
		if($scope.form.$valid){
			ProjectFile.update({
				id: $scope.projectFile.project_id , idFile: $scope.projectFile.id}, 
				$scope.projectFile,
				function() {
				$location.path('/project/' + $routeParams.id + '/files');
			});
			
		}
	}

}]);