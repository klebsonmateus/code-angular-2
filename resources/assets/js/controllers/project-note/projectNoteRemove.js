angular.module('app.controllers')
	.controller('ProjectNoteRemoveController', 
		['$scope','$routeParams','$location', 'ProjectNote', 
	function($scope, $routeParams, $location,  ProjectNote) {
	$scope.projectNote = ProjectNote.get({id: $routeParams.id});

	$scope.remove = function () {
		$scope.projectNote.$delete().then(function(){
			$location.path('/project/' + $routeParams.id + '/notes');
		});
	}

}]);