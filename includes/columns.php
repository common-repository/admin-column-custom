<?php
/*
 * Admin Column
 */
defined('ABSPATH') or die();

/* Posts */

/*
 * Since 1.0.0
 * 
 * Update 1.0.10
 * 
 * Display column value;
 */
function ad_column_custom_post_column($column = '', $post_id = null) 
{
	$post = get_post($post_id);

	if (is_object($post) && isset($post->ID))
	{
		$data = ad_column_custom_get_column($post->post_type, $column);

		$name = isset($data['name']) ? $data['name'] : '';
		$type = isset($data['type']) ? $data['type'] : '';

		if ($name == '') return;
		
		$value = '';

		if ($type == 'post_field')
		{
			if ($name == 'path') {
				$value = ad_column_get_page_path($post_id);
			} else {
				$value = get_post_field($name, $post);
			}

			$value = esc_attr($value);
		}
		else if ($type == 'post_meta')
		{
			$value = get_post_meta($post_id, $column, true);

			if (is_array($value)) {
				$value = implode(', ', $value);
			}

			$value = esc_attr($value);
		}
		else if ($type == 'media')
		{
			$size = 'thumbnail';
			
			$media_id = (int) get_post_meta($post_id, $column, true);
			
			if ($column == $size) {
				$value = get_the_post_thumbnail($post, $size, ['class'=>'ad-column-img']);
			} else if ($media_id>0){				
				$value = wp_get_attachment_link($media_id, $size, false, true, false, ['class'=>'ad-column-img']);
			}
		}

		echo $value;
	}
}

/*
 * Since 1.0.0
 * 
 * @param array $columns
 * @param string $type
 * 
 * @return array;
 */
function ad_column_custom_add_post_column($columns = array(), $type = 'page')
{
	$data = ad_column_get_option($type);

	if (is_array($data) == false || count($data) == 0 || count($columns) == 0) {
		return $columns;
	}

	$list = array();

	foreach($data as $i => $item) {
		if ($item['after'] == 'first') {
			$list[$item['name']] = esc_attr(__($item['title']));
			unset($data[$i]);
		}
	}

	foreach ($columns as $key => $value) {
		$list[$key] = $value;
	
		foreach($data as $i => $item) {
			if ($item['after'] == $key) {
				$list[$item['name']] = esc_attr(__($item['title']));
				unset($data[$i]);
			}
		}
	}
	
	return $list;
}

/*
 * Since 1.0.1
 * 
 * Update 1.0.2
 */
function ad_column_custom_list_page()
{
	/* posts */
	foreach(ad_column_custom_get_post_types('key') as $post_type) {
		add_filter('manage_'. $post_type .'s_columns', 'ad_column_custom_add_post_column', 10, 2);
		add_action('manage_'. $post_type .'s_custom_column', 'ad_column_custom_post_column', 10, 2);
	}

	/* user */
	add_filter('manage_users_columns', 'ad_column_custom_add_user_column');
	add_filter('manage_users_custom_column', 'ad_column_custom_user_column', 10, 3);
}
add_action('admin_init' , 'ad_column_custom_list_page');

/* Users */

/*
 * Since 1.0.0
 * 
 * @param array $columns
 * 
 * @return array;
 */
function ad_column_custom_add_user_column($columns) 
{
	return ad_column_custom_add_post_column($columns, 'user');
}

/*
 * Since 1.0.0
 * 
 * @param string $value
 * @param string $column
 * @param number $user_id
 * 
 * @return string;
 */
function ad_column_custom_user_column($value = '', $column = '', $user_id = 0) 
{
	if ($user_id>0) {
		$data = ad_column_custom_get_column('user', $column);
		if (isset($data['name'])) {
			$value = get_user_meta($user_id, $column, true);

			if (is_array($value)) {
				$value = implode(', ', $value);
			}

			$value = esc_attr($value);
		}
	}
	
	return $value;
}

/*
 * Since 1.0.1
 * 
 * @param array $columns
 * 
 * @return array;
 */
function ad_column_custom_manage_columns($columns = array())
{
	// $columns['url'] = 'URL';

	return $columns;
}
add_filter('manage_edit-page_columns', 'ad_column_custom_manage_columns');

/**
 * Can't hide these for they are special.
 * 
 * wp-admin/includes/class-wp-screen.php
 * 
 * Line 1164 in function `render_list_table_columns_preferences`;
 * 
 * @return array;
 */
function ad_column_custom_get_special_columns()
{
	$special = array('_title', 'cb', 'comment', 'media', 'name', 'title', 'username', 'blogname');

	return $special;
}

/*
 * Since 1.0.1
 * 
 * Update 1.0.2
 * 
 * @param string $type
 * 
 * @return array;
 */
function ad_column_custom_get_default_columns($type = '')
{
	$columns = array();

	if ($type == 'user') {
		$names = array('username', 'name', 'email', 'role', 'posts');
	} else if (in_array($type,['post','page'])) {
		/* posts */ 
		
		$names = array('title', 'author');

		if ($type == 'post') {
			$names = array_merge($names, array('categories', 'tags'));
		}

		$names = array_merge($names, array('comments', 'date', 'modified'));
	} else {
		$names = array('title');
	}
	
	foreach ($names as $name) {
		$columns[] = array(
			'title' => __(ucwords($name), 'admin-column-custom'),
			'name' => $name,
			'default' => 1
		);
	}

	return $columns;
}

/*
 * Since 1.0.1
 * 
 * @param string $type
 * @param array $columns
 * 
 * @return array;
 */
function ad_column_custom_get_columns($type = '', $columns = array())
{
	if (count($columns) == 0) {
		$columns = ad_column_custom_get_default_columns($type);
	}

	$data = ad_column_get_option($type);

	if (is_array($data) && count($data)) {
		$list = array();

		foreach($data as $i => $item) {
			if ($item['after'] == 'first') {
				$item['default'] = 0;
				$list[] = $item;
				unset($data[$i]);
			}
		}

		foreach($columns as $column) {
			$list[] = $column;

			foreach($data as $i => $item) {
				if ($item['after'] == $column['name']) {
					$item['default'] = 0;
					$list[] = $item;
					unset($data[$i]);
				}
			}
		}

		$columns = $list;
	}

	return $columns;
}

/*
 * Since 1.0.1
 * 
 * @param string $type
 * @param string $name
 * @param string $key
 * 
 * @return array|string;
 */
function ad_column_custom_get_column($type = '', $name = '', $key = '')
{
	global $ad_column_data;

	if (empty($ad_column_data[ $type ])) {
		$ad_column_data[ $type ] = ad_column_get_option($type);
	}

	$data = $ad_column_data[ $type ];

	if (is_array($data) && count($data)>0) {
		foreach($data as $item) {
			if ($item['name'] == $name) {

				if ($key!='' && isset($item[$key])) {
					return $item[$key];
				}

				return $item;
			}
		}

		return $data;
	}

	if ($key!='') {
		return '';
	}

	return array();
}

/*
 * Since 1.0.2
 * 
 * Update 1.0.10
 * 
 * @param string $type 
 * 
 * @return array;
 */
function ad_column_custom_get_column_types($type = '')
{
	if ($type == 'user') {
		$column_types = array(
			'user_meta' 	=> __('User Meta', 'admin-column-custom'),
		);
	} else {
		$column_types = array(
			'post_field' 	=> __('Post Field', 'admin-column-custom'),
			'post_meta' 	=> __('Post Meta', 'admin-column-custom'),
			'media' 		=> __('Media', 'admin-column-custom'),
		);
	}

	return $column_types;
}
