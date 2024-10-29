<?php
/*
 * Admin Column
 */
defined('ABSPATH') or die();

function ad_column_custom_plugin_actions($actions = array())
{
	$pagenow = sanitize_text_field(isset($GLOBALS['pagenow']) ? $GLOBALS['pagenow'] : '');
	if ($pagenow != 'plugins.php') {
		return $actions;
	}

	$url_setting = esc_url(admin_url('options-general.php?page=admin-column-custom'));

	array_unshift($actions, "<a href=\"$url_setting\">" . __("Settings") . "</a>");

	return $actions;
}
add_filter("plugin_action_links_" . plugin_basename(ad_column_custom_index()), "ad_column_custom_plugin_actions");

/* ADD SETTINGS PAGE
------------------------------------------------------*/
function ad_column_custom_add_options_page()
{
	add_options_page(
		'Admin Column Settings',
		'Admin Column',
		'manage_options',
		'admin-column-custom',
		'ad_column_custom_setting_display'
	);
}
add_action('admin_menu', 'ad_column_custom_add_options_page');

function ad_column_custom_hidden_columns($hidden)
{
	$hidden[] = 'name';

	return $hidden;
}

/* SECTIONS - FIELDS
------------------------------------------------------*/
function ad_column_custom_init_theme_option()
{
	// add Setting
	add_settings_section(
		'admin-column_options_section',
		'Admin Column Options',
		'admin-column_options_section_display',
		'admin-column-options-section'
	);

	foreach (ad_column_custom_get_post_types() as $post_type => $title) {
		register_setting('ad_column_' . $post_type, 'ad_column_' . $post_type);
	}

	$url = ad_column_custom_assets_url();

	$page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';
	if ($page == 'admin-column-custom') {
		// Styles - Scripts
		wp_enqueue_style('admin-column', $url . 'admin.css');
		wp_enqueue_script('admin-column', $url . 'admin.min.js', array('jquery', 'jquery-ui-sortable'), '', true);

		// wp_enqueue_script('admin-column', $url . 'admin.js', array('jquery', 'jquery-ui-sortable'), time(), true); // dev
	}

	$pagenow = isset($GLOBALS['pagenow']) ? sanitize_text_field($GLOBALS['pagenow']) : '';
	if ($pagenow == 'edit.php') {
		// Styles - Scripts
		wp_enqueue_style('admin-column-edit', $url . 'edit.css');
	}
}
add_action('admin_init', 'ad_column_custom_init_theme_option');

function ad_column_custom_setting_display()
{
	load_template(ad_column_custom_path('includes/list.php'));
}
