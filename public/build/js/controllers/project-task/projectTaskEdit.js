angular.module('app.controllers')
	.controller('ProjectTaskEditController', 
		['$scope','$routeParams','$location', 'ProjectTask','appConfig', 
	function($scope, $routeParams , $location,  ProjectTask, appConfig) {
		$scope.projectTask = new ProjectTask.get({
			id: $routeParams.id,
			taskId: $routeParams.taskId
		});

		$scope.status = appConfig.projectTask.status;

		$scope.start_date = {
			status: {
				opened: false
			}
		};

		$scope.due_date = {
			status: {
				opened: false
			}
		};

		$scope.openStartDatePicker = function ($event) {
			$scope.start_date.status.opened = true;
		};

		$scope.openDueDatePicker = function ($event) {
			$scope.due_date.status.opened = true;
		};

		$scope.save = function () {
			if ($scope.form.$valid) {
				ProjectTask.update({
					id: $routeParams.id, 
					taskId: $scope.projectTask.id
				},$scope.projectTask,function () {
					$location.path('/project/'+$routeParams.id+'/tasks');
				});
			}
		};
}]);