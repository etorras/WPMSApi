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

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
include_once(ABSPATH . 'wp-includes/pluggable.php');

define('JSON_API_MU_HOME', dirname(__FILE__));

if (!is_plugin_active('json-api/json-api.php')) {
    add_action('admin_notices', 'pim_mu_draw_notice_json_api');
    return;
}
//if Api key is empty
if(get_option('wp_mu_apikey') == ''){
  add_option( 'wp_mu_apikey', wp_generate_password());
}  
add_action('admin_menu', 'custom_api_key');

function custom_api_key (){
  add_options_page('Api Key Page', 'Api Key', 10, 'custom_api_key_file', 'custom_api_key_setting');
}
function custom_api_key_setting (){
  $api_key_saved = get_option('wp_mu_apikey');
        
      if(isset($_POST['Submit']))   {
      $api_key_saved = $_POST["api_key"];
          update_option( 'wp_mu_apikey', $api_key_saved );   
 ?>
<div class="updated"><p><strong><?php _e('Api Key Saved', 'mt_trans_domain' ); ?></strong></p></div>
<?php  }  ?>
   
<div class="wrap">
  <form method="post" name="options" target="_self">
    <h2>Set the Api Key</h2>
      <table width="100%" cellpadding="10" class="form-table">
       
        <tr>
          <td align="left" scope="row">      
            <label><strong>Api Key: </strong></label><input name="api_key" value="<?php echo $api_key_saved ?>" />   
          </td>   
        </tr>
      </table>
      <p class="submit">
        <input type="submit" name="Submit" value="Actualizar" />
      </p>
  </form>
</div>
<?php
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
