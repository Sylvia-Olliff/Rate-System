(function() {
	var app = angular.module('RateMileQuery', []);

	app.controller('QueryController', [ '$http', '$scope', function($http, $scope) {
		this.queryData = {
			MODE : 'TLD'
		};

		this.MODES = modeTypes;
		this.SUCCESS = false;

		$scope.FCITYrequired = false;
		$scope.toggleFCITY = function() {
			if ($scope.FCITYrequired == false) {
				$scope.FCITYrequired = true;
			} else {
				$scope.FCITYrequired = false;
			}
		}

		this.querySearch = function(QueryCtrl) {
			QueryCtrl.response = {};
			$http({
				method : "post",
				url : "getRoutes.php",
				data : {data : QueryCtrl.queryData}
			}).then(function(response) {
				QueryCtrl.response = response.data;
				QueryCtrl.SUCCESS = true;
			}, function(response){
				QueryCtrl.response = {data : "There was an error"};
			});
		};

		this.show = function(QueryCtrl) {
			if (QueryCtrl.SUCCESS) {
				return true;
			} else {
				return false;
			}
		};	
	}]);

	app.filter('ampersand', function(){
    	return function(input){
    	    return input ? input.replace(/&amp;/g, '&') : '';
    	}
	});


	
	var modeTypes = [
			{value : "AIR",
				displayName : "AIR" },
			{value : "BLK",
				displayName : "BLK"},
			{value : "CTR",
				displayName : "CTR"},
			{value : "FBD",
				displayName : "FBD"},
			{value : "HCR",
				displayName : "HCR"},
			{value : "HTK",
				displayName : "HTK"},
			{value : "IMO",
				displayName : "IMO"},
			{value : "LTL",
				displayName : "LTL"},
			{value : "ODM",
				displayName : "ODM"},
			{value : "RCT",
				displayName : "RCT"},
			{value : "REF",
				displayName : "REF"},
			{value : "RTP",
				displayName : "RTP"},
			{value : "SDK",
				displayName : "SDK"},
			{value : "TLD",
				displayName : "TLD"},
			{value : "TTK",
				displayName : "TTK"},
			{value : "XPD",
				displayName : "XPD"},
			{value : "STR",
				displayName : "STR"},
			{value : "CUR",
				displayName : "CUR"},
			{value : "SWG",
				displayName : "SWG"},
			{value : "LTR",
				displayName : "LTR"},
			{value : "FLR",
				displayName : "FLR"},
			{value : "CUS",
				displayName : "CUS"}
		];

})();