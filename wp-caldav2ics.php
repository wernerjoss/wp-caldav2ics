<?php
/*
   Plugin Name: WP-CalDav2ICS
   Plugin URI: http://wordpress.org/extend/plugins/wp-caldav2ics/
   Version: 1.3.4
   Author: Werner Joss
   Description: Create ICS File from CalDav Calendar
   Text Domain: wp-caldav2ics
   License: GPLv3
  */

/* 
    Credits:
    The Structure of this Plugin is based on Michael Simpson's  "WordPress Plugin Template" : http://plugin.michael-simpson.com/ ,
    which is free Software, GPLv3.
    The Plugin itself however, is NOT a Library nor a Boilerplate, it just does what it is intended for :)
*/

$Caldav2ics_minimalRequiredPhpVersion = '5.6';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function Caldav2ics_noticePhpVersionWrong() {
    global $Caldav2ics_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "WP-CalDav2ICS" requires a newer version of PHP to be running.',  'wp-caldav2ics').
            '<br/>' . __('Minimal version of PHP required: ', 'wp-caldav2ics') . '<strong>' . $Caldav2ics_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'wp-caldav2ics') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}

function Caldav2ics_PhpVersionCheck() {
    global $Caldav2ics_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $Caldav2ics_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'Caldav2ics_noticePhpVersionWrong');
        return false;
    }
    return true;
}

/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Caldav2ics_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('wp-caldav2ics', false, $pluginDir . '/languages/');
}

//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loaded','Caldav2ics_i18n_init');
// was: add_action('plugins_loadedi','Caldav2ics_i18n_init');
// see http://plugin.michael-simpson.com/?page_id=43

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (Caldav2ics_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('caldav2ics_init.php');
    Caldav2ics_init(__FILE__);
}
