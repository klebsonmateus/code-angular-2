angular.module('app.controllers')
	.controller('ProjectTaskRemoveController', 
		['$scope','$location', '$routeParams', 'ProjectTask', 
	function($scope,  $location, $routeParams,  ProjectTask) {
	$scope.projectTask = ProjectTask.get({
		id: $routeParams.id,
		taskId: $routeParams.taskId
	});

	$scope.remove = function () {
		$scope.projectTask.$delete({
			id: $routeParams.id , 
			taskId: $routeParams.taskId
		}).then(function(){
			$location.path('/project/' + $routeParams.id + '/tasks');
		});
	}

}]);