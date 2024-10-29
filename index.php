<?php
/*
Plugin Name: Admin Column
Plugin URI: http://photoboxone.com/donate/?for=admin-column
Description: Customise columns on the administration screens for posts, pages and users.
Author: DevUI
Author URI: http://photoboxone.com/donate/?developer=devui
Version: 1.0.18
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: admin-column-custom
*/

defined('ABSPATH') or die();

function ad_column_custom_index()
{
    return __FILE__;
}

if (is_admin()) {
    require( __DIR__ . '/includes/functions.php');
    require( __DIR__ . '/includes/columns.php');
    require( __DIR__ . '/includes/setting.php');
}