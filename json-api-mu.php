<?php

/*
  Plugin Name: JSON API Mu
  Plugin URI:
  Description: Extends the JSON API for RESTful create blog mu
  Version: 0.1
  Author: Quique Torras
  Author URI:
  License: GPLv3
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
define('JSON_API_MU_HOME', dirname(__FILE__));

if (!is_plugin_active('json-api/json-api.php')) {
    add_action('admin_notices', 'pim_mu_draw_notice_json_api');
    return;
}

add_filter('json_api_controllers', 'pimMuJsonApiController');
add_filter('json_api_mu_controller_path', 'setMuControllerPath');
load_plugin_textdomain('json-mu-user', false, basename(dirname(__FILE__)) . '/languages');

function pim_mu_draw_notice_json_api() {
    echo '<div id="message" class="error fade"><p style="line-height: 150%">';
    _e('<strong>JSON API MU</strong></a> requires the JSON API plugin to be activated. Please <a href="wordpress.org/plugins/json-api/‎">install / activate JSON API</a> first.', 'json-api-mu');
    echo '</p></div>';
}

function pimMuJsonApiController($aControllers) {
    $aControllers[] = 'MU';
    return $aControllers;
}

function setMuControllerPath($sDefaultPath) {
    return dirname(__FILE__) . '/controllers/MU.php';
}