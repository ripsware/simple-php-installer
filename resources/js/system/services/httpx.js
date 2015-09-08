/**
 * Created by Rio Permana on 24/08/2015.
 */

(function(){
    'use strict';

    if(!window.baseurl){
        window.baseurl = 'http://localhost/php-installer/index.php';
    }

    var baseurl = window.baseurl;

    angular.module('httpx', [])

        .factory('$httpx', ['$http', '$filter', function($http, $filter){
            var $this = this;
            this.site_url = function(url){
                return baseurl + (url || '');
            };
            this.post = function(url, param){
                param = $this.formatParam(param);
                return $http({
                    method: 'POST',
                    url: url,
                    data: param ? $.param(param) : null,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                });
            };
            this.get = function(url, param){
                param = $this.formatParam(param);
                return $http({
                    method: 'GET',
                    url: url + (param ? ((url.indexOf('?') == -1 ? '?' : '&') + $.param(param)): ''),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                });
            };
            this.formatParam = function(param){
                if(param && param instanceof Date){
                    return param.toJSON();
                }else if(param instanceof Object){
                    angular.forEach(param, function(val, i){
                        param[i] = $this.formatParam(val);
                    });
                }
                return param;
            };
            return this;
        }])


        .filter('dirname', [function(){
            return function (f) {
                if(f){
                    return f.substr(0, f.lastIndexOf('/')).replace(/file\:\/\//gi, '').replace('file:/', '');
                }
                return "";
            }
        }])
        .filter('filename', [function(){
            return function (f) {
                if(f){
                    return f.substr(f.lastIndexOf('/') + 1);
                }
                return "";
            }
        }])
        .filter('site_url', [function () {
            return function (val) {
                return baseurl + (val || '');
            };
        }])
        .filter('file_url', [function () {
            return function (val) {
                if(val){
                    if(val.indexOf('http://') > -1 || val.indexOf('https://') > -1){
                        return val;
                    }
                    return baseurl + 'fileman/file?e=0&file=' + val.replace(/file:\/\//gi, '');
                }
                return "";
            };
        }])
        .filter('range', function() {
            return function(input, total) {
                total = parseInt(total);
                for (var i=0; i<total; i++)
                    input.push(i);
                return input;
            };
        })
        .filter('clean', [function(){
            return function(val){
                if(val == '0000-00-00 00:00:00'){
                    return null;
                }
                return val;
            }
        }])

    ;

})();
