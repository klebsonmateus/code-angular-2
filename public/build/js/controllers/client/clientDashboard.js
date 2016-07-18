angular.module('app.controllers')
	.controller('ClientDashboardController', 
		['$scope','$routeParams','$location', 'Client', 
	function($scope, $routeParams , $location,  Client) {
	Client.query({
		orderBy: 'created_at',
		sortedBy: 'desc',
		limit: 8
	},function(response){
		$scope.clients = response.data;
	});

}]);