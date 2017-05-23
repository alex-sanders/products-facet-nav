//wrapping your JS in a closure is a good habit
(function (){
/*('uxprototypeApp') = App name; [] includes all 
app dependencies, a blank value means there are no
dependencies.
*/

var uxprototypeApp = angular.module('uxprototypeApp', []);

                var uniqueItems = function(data, key) {
                    var result = [];
                    for (var i = 0; i < data.length; i++) {
                        var value = data[i][key];
                        if (result.indexOf(value) === -1) {
                            result.push(value);
                        }
                    }
                    return result;
                };


/*
Notice that the controller (SearchCtrl) is attached to inside our app uxprototypeApp

This dirrective (uxprototypeApp) tells the AngularJS that the new 
uxprototypeApp is responsible for the element with 
the directive and all of the element's children. 


Note: dependencies in the []


*/
uxprototypeApp.
config(["$locationProvider", function($locationProvider) {
	$locationProvider.html5Mode(true).hashPrefix('!');
}]).
factory("ProductCallouts", [
	"$location",
	"pathPrefix",
	function($location, pathPrefix) {
		callouts = {
			init: function() {
				var callouts = this;
				var activeCallouts = $location.path().substr(pathPrefix.length ), callouts;
				activeCallouts = activeCallouts.replace(/^\//, "");
				activeCallouts = activeCallouts ? activeCallouts.split(/\//) : [];
				angular.forEach(callouts.types, function(callout) {
					callout.checked = (activeCallouts.indexOf(callout.path) != -1);
				});
			},
			update: function() {
				var callouts = this;
				var activeCallouts = [];
				angular.forEach(callouts.types, function(callout) {
					if (callout.checked) {
						activeCallouts.push(callout.path);
					}
				});
				if (activeCallouts.length) {
					activeCallouts.sort();
					$location.path(pathPrefix+"/"+activeCallouts.join("/"));
				} else {
					$location.path(pathPrefix);
				}
			},
			types: [
				{name: "Sale", path: "sale"},
				{name: "Clearance", path: "clearance"},
				{name: "Bonus Offer", path: "bonus-offer"}
			]
		};
		callouts.init();
		return callouts;
	}
]).
controller("ProductCalloutsCtrlr", [
	"$scope",
	"ProductCallouts",
	function($scope, callouts) {
		$scope.callouts = callouts;
		$scope.updateCallouts = function() {
			callouts.update();
		};
		$scope.$on("$locationChangeSuccess", function(){
			callouts.init();
		});		
	}
]).
filter("inProductCallouts", [
	"ProductCallouts",
	function(callouts) {
		return function(products) {
			var out = [], calloutsLen = callouts.types.length, i, areCallouts = false;
			
			for (i = 0; i < calloutsLen; i++) {
				if (callouts.types[i].checked) {
					areCallouts = true;
					break;
				}
			}

			if (areCallouts) {
				angular.forEach(products, function(product) {
					var i;
					for (i = 0; i < calloutsLen; i++) {
						if ((product.Callout.indexOf(callouts.types[i].name) != -1) && callouts.types[i].checked) {
							out.push(product);
							break;
						}
					}
				});
			} else {
				out = products;
			}
			return out;
		}
	}
]).
controller('SearchCtrl', ['$scope', "$http", "ProductCallouts", "pathPrefix", function($scope, $http, callouts, pathPrefix) {
//Scopes here help watch expressions and propagate events
                    $scope.useBrands = {};
                    $scope.filters = {};
                    $scope.filters.Label = {};

                    $scope.maxBrands = 5;
                    $scope.maxLabels = 5;

                    $scope.layout = 'grid';

										//
										// Setup Callout
										$scope.filters.Callout = callouts.types;										

                    //
                    // Load products from external JSON
                    $scope.products = [];
										$http({
											method: 'GET',
											url: pathPrefix+"/products.json"
										}).then(
											function(response) {
												$scope.products = response.data;
											},
											function(reason) {
												// Error loading products...
												console.log(reason);
											}
										);
										
                    $scope.sorting = {
                        id: "1",
                        order: "Name",
                        direction: "false"
                    };

                    $scope.setOrder = function(id, order, reverse) {
                        $scope.sorting.id = id;
                        $scope.sorting.order = order;
                        $scope.sorting.direction = reverse;
                    };

                    //Watch the Price that are selected
                    $scope.$watch(function() {
                        return {
                            products: $scope.products,
                            useBrands: $scope.useBrands,
                        }
                    }, function(value) {
                        var selected;

                        $scope.count = function(prop, value) {
                            return function(el) {
                                return el[prop] == value;
                            };
                        };

                        $scope.brandsGroup = uniqueItems($scope.products, 'Brand');
                        var filterAfterBrands = [];
                        selected = false;
                        for (var j in $scope.products) {
                            var p = $scope.products[j];
                            for (var i in $scope.useBrands) {
                                if ($scope.useBrands[i]) {
                                    selected = true;
                                    if (i === p.Brand) {
                                        filterAfterBrands.push(p);
                                        break;
                                    }
                                }
                            }
                        }
                        if (!selected) {
                            filterAfterBrands = $scope.products;
                        }

                        $scope.filteredProducts = filterAfterBrands;
                    }, true);

                    $scope.$watch('filtered', function(newValue) {
                        if (angular.isArray(newValue)) {
                            console.log(newValue.length);
                        }
                    }, true);

             }]); //end controler

			 
                uxprototypeApp.filter('count', function() {
                    return function(collection, key) {
                        var out = "test";
                        for (var i = 0; i < collection.length; i++) {
                            //console.log(collection[i].Price);
                            //var out = myApp.filter('filter')(collection[i].Price, "42", true);
                        }
                        return out;
                    }
                }); //end filter

})();