
var applicationUrl = "list-editor-with-rest-api/";
var viewsUrl = applicationUrl + "views/";
var apiUrl = "products-rest-api/products/";
var scriptFileName = "/list-editor-with-rest-api.html";

var listEditorApp = angular.module("ListEditorApp", ["ngRoute"]);

listEditorApp.config(function($routeProvider, $locationProvider) {
    
    $locationProvider.html5Mode(true);
    
    $routeProvider.when(scriptFileName, {
        templateUrl: viewsUrl + "/product-list.html"
    });
    
    $routeProvider.when(scriptFileName + "/edit", {
        templateUrl: viewsUrl + "/product-crud.html"
    });
    
    $routeProvider.when(scriptFileName + "/create", {
        templateUrl: viewsUrl + "/product-crud.html"
    });
    
    $routeProvider.otherwise({
        templateUrl: viewsUrl + "/product-list.html"
    });
    
});

listEditorApp.controller("ListEditorCtrl", function($scope, 
                                                    $exceptionHandler,
                                                    $http,
                                                    $location) {
                                                        
    $scope.refresh = function() {
        $http.get(apiUrl).then(function(response) {
            $scope.products = response.data;
        });
    };
    
    /**
     * Set current view cpecified in object viewsList
     * @param {String} view
     */                                                        
    function setCurrentView(view) {
        if (angular.isDefined(viewsList[view])) {
            $scope.currentView = viewsUrl + viewsList[view] + ".html";
        } else {
            $exceptionHandler('Specified view "' + view + '" not exist', 
                              "setCurrentView");
        }
    }

    $scope.showListView = function() {
        $location.path(scriptFileName);
    };

    $scope.returnToList = function() {
        $scope.showListView();
        $scope.crudProduct = {};
    };

    $scope.showCrudView = function(product) {
        $scope.isCreating = !angular.isDefined(product);
        $scope.crudProduct = ($scope.isCreating ? {} : angular.copy(product));
        
        if ($scope.isCreating) {
            $location.path(scriptFileName + '/create');
        } else {
            $location.path(scriptFileName + '/edit');
        }
    };

    $scope.deleteProduct = function(product) {
        if (angular.isDefined(product.name)) {
            if (confirm('Are you shure you want to delete product "' + product.name + '"?')) {
                $http.delete(apiUrl + product.id + "/").then(function(response) {
                    $scope.products.splice($scope.products.indexOf(product), 1);
                });
            }
        }
    };

    $scope.crud = function(product) {
        if ($scope.isCreating) {
            create(product);
        } else {
            update(product);
        }
        $scope.returnToList();
    };

    function update(product) {
        for (var i in $scope.products) {
            if ($scope.products[i].id === product.id) {
                $http.put(apiUrl + product.id + "/", product).then(function(response) {
                    angular.extend($scope.products[i], product);
                });
                break;
            }
        }
    }

    function create(product) {
        $http.post(apiUrl, product).then(function(response) {
            if (angular.isDefined(response.data.id)) {
                $scope.products.push(response.data);
            }
        });
    }

    $scope.refresh();
    $scope.showListView();

    $scope.getDeliveredInputLabel = function(delivered) {
        return (delivered ? "Yes" : "No");
    };

    $scope.getDeliveredInputStyle = function(delivered) {
        return (delivered ? "color:green;font-weight:bold;" : "color:red;");
    };

});