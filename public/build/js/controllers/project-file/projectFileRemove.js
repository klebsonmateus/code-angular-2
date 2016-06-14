angular.module('app.controllers')
	.controller('ProjectFileRemoveController', 
		['$scope','$routeParams','$location', 'ProjectFile', 
	function($scope, $routeParams, $location,  ProjectFile) {
	$scope.projectFile = ProjectFile.get({
		id: null,
		idFile: $routeParams.idFile
	});

	$scope.remove = function () {
		$scope.projectFile.$delete({
			id: null , idFile: $scope.projectFile.id
		}).then(function(){
			$location.path('/project/' + $routeParams.id + '/files');
		});
	}

}]);