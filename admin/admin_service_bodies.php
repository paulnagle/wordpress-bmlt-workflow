<?php

if (!defined('ABSPATH')) exit; // die if being called directly

use wbw\WBW_Debug;
use wbw\WBW_WP_Options;

$WBW_WP_Options = new WBW_WP_Options();

$wbw_bmlt_test_status = $WBW_WP_Options->wbw_get_option('wbw_bmlt_test_status', "failure");
if ($wbw_bmlt_test_status != "success") {
    wp_die("<h4>WBW Plugin Error: BMLT Server not configured and tested.</h4>");
}

wp_nonce_field('wp_rest', '_wprestnonce');

?>

<div class="wrap">
    <div id="icon-users" class="icon32"></div>
    <h2>Service Body Configuration</h2>
    <hr class="wbw-error-message">
    <div class="wbw_info_text">
        <br>Service bodies are retrieved from BMLT using the BMLT details configured on the option page.
        <br><br>You can configure which service areas are visible to the end-users using the <code>Display on end-user Form</code> checkbox.
        <br><br>You can select users from your Wordpress userlist and grant them access to your service areas in the <code>Wordpress Users with Access</code> column.
        These users will only be given access to the submission admin page, and only submissions from their service areas will be visible to approve.
        <br><br>Note: Settings will only be applied when the 'Save Settings' button is pressed.
        <br><br>
    </div>
    <br>
    <span class="spinner" id="wbw-form-spinner"></span>
    <table class="wbw-userlist-table" id="wbw-userlist-table" style="display: none;">
        <thead>
            <tr>
                <th class="wbw-userlist-header">Service Body</th>
                <th class="wbw-userlist-header">Description</th>
                <th class="wbw-userlist-header">Wordpress Users with Access</th>
                <th class="wbw-userlist-header">Display on end-user Form</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <button id="wbw_submit" style="display: none;">Save Settings</button><span class="spinner" id="wbw-submit-spinner"></span>
</div>