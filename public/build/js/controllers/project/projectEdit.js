angular.module('app.controllers')
	.controller('ProjectEditController', 
		['$scope','$routeParams','$location', 'ProjectNote', 
	function($scope, $routeParams , $location,  ProjectNote) {
	$scope.projectNote = ProjectNote.get({
		id: $routeParams.id,
		idNote: $routeParams.idNote
	});

	$scope.save = function () {
		if($scope.form.$valid){
			ProjectNote.update({
				id: $scope.projectNote.project_id , idNote: $scope.projectNote.id}, 
				$scope.projectNote,
				function() {
				$location.path('/project/' + $routeParams.id + '/notes');
			});
			
		}
	}

}]);