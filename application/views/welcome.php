<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="en" ng-app="PHPInstaller" class="initialized">
<head>
	<meta charset="utf-8">
	<title>PHP-Installer</title>

    <link href="<?=base_url('resources/styles/dist/common.css');?>" rel="stylesheet">
    <link href="<?=base_url('resources/vendors/raw/angular-motion/dist/angular-motion.min.css');?>" rel="stylesheet">
    <script type="text/javascript">var baseurl = '<?=site_url();?>/';</script>
</head>
<body ng-controller="MainCtrl">
<div id="header" class="container-fluid"></div>
<div id="main-content" class="container-fluid">
    <div class="row">
        <div class="col-xs-pull-0 col-sm-push-1 col-md-push-1 col-xs-12 col-sm-10 col-md-10">
            <div class="row">
                <div class="col-md-1" style="height: 100%;">
                    <div class="v-tab collapsed floating">
                        <a class="v-tab-item" ng-click="showTab(config)" ng-repeat="config in models.configs" ng-class="{'selected': localSettings.configsVisible[config.group]}">
                            <span class="icon">
                                <i class="fa fa-gear" ng-if="!config.icon"></i>
                                <i class="fa {{config.icon}}" ng-if="config.icon"></i>
                            </span>
                            <span class="caption">{{config.label}}</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="row" ng-repeat="config in models.configs" ng-show="localSettings.configsVisible[config.group]">
                        <div class="col-md-6">
                            <form ng-attr-name="{{config.group}}">
                                <fieldset>
                                    <legend>{{config.label}}</legend>
                                    <div ng-class="{'required': field.required}" class="form-group" ng-repeat="field in config.fields" ng-switch="field.type">
                                        <label ng-disabled="localSettings.isLoading" ng-required="field.required" ng-attr-for="{{field.name}}" ng-init="field.value = field.default">{{field.label}}</label>
                                        <input ng-disabled="localSettings.isLoading" ng-readonly="field.readonly" ng-required="field.required" class="form-control" ng-switch-when="text" type="text" ng-attr-id="{{field.name}}" ng-attr-name="{{field.name}}" ng-model="field.value">
                                        <input ng-disabled="localSettings.isLoading" ng-readonly="field.readonly" ng-required="field.required" class="form-control" ng-switch-when="password" type="password" ng-attr-id="{{field.name}}" ng-attr-name="{{field.name}}" ng-model="field.value">
                                        <input ng-disabled="localSettings.isLoading" ng-readonly="field.readonly" ng-required="field.required" class="form-control" ng-switch-when="number" type="number" ng-attr-id="{{field.name}}" ng-attr-name="{{field.name}}" ng-model="field.value">
                                        <label ng-disabled="localSettings.isLoading" ng-switch-when="bool">
                                            <span class="fa-stack">
                                                <i class="fa fa-square-o fa-stack-2x"></i>
                                                <i class="fa fa-check fa-stack-1x" ng-show="field.value"></i>
                                            </span>
                                            <input ng-disabled="localSettings.isLoading" ng-readonly="field.readonly" style="display: none;" type="checkbox" ng-attr-id="{{field.name}}" ng-attr-name="{{field.name}}" ng-model="field.value">
                                        </label>
                                        <textarea ng-disabled="localSettings.isLoading" ng-readonly="field.readonly" ng-required="field.required" ng-list="&#10;" ng-trim="false" class="form-control" ng-switch-when="string_lists" ng-attr-id="{{field.name}}" ng-attr-name="{{field.name}}" ng-model="field.value"></textarea>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-left">
                            <button ng-disabled="localSettings.isLoading" ng-click="executeAction()" type="button" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="footer" class="container-fluid"></div>
</body>

<script type="text/javascript" src="<?=base_url('resources/vendors/raw/jquery/dist/jquery.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/vendors/raw/underscore/underscore-min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/vendors/raw/angular/angular.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/vendors/raw/angular-animate/angular-animate.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/vendors/raw/angular-route/angular-route.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/vendors/raw/angular-strap/dist/angular-strap.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/vendors/raw/angular-strap/dist/angular-strap.tpl.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/js/system/services/httpx.js');?>"></script>
<script type="text/javascript" src="<?=base_url('resources/js/app/controllers/main.js');?>"></script>
</html>