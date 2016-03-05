
var applicationUrl = "list-editor-with-rest-api/";
var viewsUrl = applicationUrl + "views/";
var apiUrl = applicationUrl + "rest/products.php";

var viewsList = {
    list: "product-list",
    crud: "product-crud"
};

var listEditorApp = angular.module("ListEditorApp", []);

listEditorApp.controller("ListEditorCtrl", function($scope, 
                                                    $exceptionHandler,
                                                    $http) {
                                                        
    $scope.refresh = function() {
        $http.get(apiUrl).then(function(response) {
            console.log(response);
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
        setCurrentView('list');
    };

    $scope.returnToList = function() {
        $scope.showListView();
        $scope.crudProduct = {};
    };

    $scope.showCrudView = function(product) {
        $scope.isCreating = !angular.isDefined(product);
        $scope.crudProduct = ($scope.isCreating ? {} : angular.copy(product));
        setCurrentView('crud');
    };

    $scope.deleteProduct = function(product) {
        if (angular.isDefined(product.name)) {
            if (confirm('Are you shure you want to delete product "' + product.name + '"?')) {
                $http.delete(apiUrl + "?id=" + product.id).then(function(response) {
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
                $http.put(apiUrl, product).then(function(response) {
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