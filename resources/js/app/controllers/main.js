/**
 * Created by Rio Permana on 06/09/2015.
 */

(function(){
    'use strict';

    angular.module('PHPInstaller', [
        'ngAnimate',
        'mgcrea.ngStrap',
        'ngRoute',
        'httpx'
    ])

        .controller('MainCtrl', ['$rootScope', '$scope', '$httpx', function($rootScope, $scope, $httpx){

            $scope.localSettings = {
                isLoading: false,
                configsVisible: {}
            };
            $scope.models = {
                configs: []
            };

            $scope.loadConfigs = function(){
                $scope.localSettings.isLoading = true;
                $httpx.post($httpx.site_url('welcome/configs'))
                    .success(function(data){
                        $scope.localSettings.isLoading = false;
                        if(data.status){
                            $scope.models.configs = data.result;
                            $scope.localSettings.configsVisible = {};
                            angular.forEach(data.result, function(item, i){
                                $scope.localSettings.configsVisible[item.group] = (i == 0);
                            });
                        }else{
                            $scope.models.configs = [];
                        }
                    })
                    .error(function(){
                        $scope.localSettings.isLoading = false;
                    })
                ;
            };

            $scope.executeAction = function(){
                $scope.localSettings.isLoading = true;
                $httpx.post($httpx.site_url('welcome/execute'), {configs: angular.copy($scope.models.configs)})
                    .success(function(data){
                        $scope.localSettings.isLoading = false;
                        if(data.status){
                            alert('Install success');
                            window.location = "../";
                        }else{
                            alert(data.message || 'Instalation failed, please try again');
                        }
                    })
                    .error(function(){
                        $scope.localSettings.isLoading = false;
                        alert('Instalation failed, please try again');
                    })
                ;
            };

            $scope.showTab = function(cfg){
                angular.forEach($scope.localSettings.configsVisible, function(val, key){
                    $scope.localSettings.configsVisible[key] = (cfg.group == key);
                });
            };

            $scope.loadConfigs();
        }])

    ;

})();