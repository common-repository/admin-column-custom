<?php

$type = isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'post';

$columns = ad_column_custom_get_columns($type);

$column_types = ad_column_custom_get_column_types($type);

$sub = ad_column_custom_get_post_types();

$option_name = 'ad_column_' . $type;

$row = 0;

$column_default = array();

?>
<div class="wrap">
  <h1 class="wp-heading-inline"><?php _e('List custom columns', 'admin-column-custom'); ?></h1>
  <hr class="wp-header-end">
  <ul class="subsubsub">
    <?php foreach ($sub as $i => $t) : ?>
      <li class="<?php echo $i ?>">
        <a href="<?php echo admin_url('options-general.php?page=admin-column-custom&type=' . $i); ?>" <?php echo $type == $i ? 'class="current"' : ''; ?>><?php echo $t ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
  <form action="options.php" id="custom-columns-form" method="post">
    <?php settings_fields($option_name); ?>
    <table class="wp-list-table widefat fixed striped table-view-list pages">
      <thead>
        <tr>
          <td id="cb" class="manage-column column-cb check-column">#</td>
          <th scope="col" id="title" class="manage-column column-title column-primary"><?php _e('Title', 'admin-column-custom'); ?></th>
          <th scope="col" id="name" class="manage-column column-name"><?php _e('Name', 'admin-column-custom'); ?></th>
          <th scope="col" id="type" class="manage-column column-type"><?php _e('Type', 'admin-column-custom'); ?></th>
          <th scope="col" id="default" class="manage-column column-default"><?php _e('Default', 'admin-column-custom'); ?></th>
        </tr>
      </thead>
      <tbody id="list-custom-columns" data-prefix="<?php echo $option_name; ?>">
        <?php foreach ($columns as $i => $column) :
          if ($column['default'] == 1) {
            $column_default[] = $column;
          }
        ?>
          <tr class="hentry-<?php esc_attr_e($column['default'] == 1 ? 'default' : 'drag'); ?>" data-name="<?php esc_attr_e($column['default'] == 1 ? $column['name'] : ''); ?>">
            <th scope="row" class="check-column">
              <label class="screen-reader-text" for="cb-select-220"><?php esc_attr_e($column['title']); ?></label>
            </th>
            <td class="column-title column-primary">
              <?php if ($column['default'] == 1) : ?>
                <?php esc_attr_e($column['title']); ?>
              <?php else : ?>
                <input type="text" name="<?php echo $option_name; ?>[<?php echo $row; ?>][title]" class="input-title" value="<?php esc_attr_e($column['title']); ?>" min-length="5" max-length="100" />
              <?php endif; ?>
              <button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_attr_e('Show more details'); ?></span></button>
            </td>
            <td class="column-name" data-colname="Name" data-field="<?php esc_attr_e($column['name']); ?>">
              <?php if ($column['default'] == 1) : ?>
                <?php esc_attr_e($column['name']); ?>
              <?php else : ?>
                <input type="text" name="<?php echo $option_name; ?>[<?php echo $row; ?>][name]" class="input-name" value="<?php esc_attr_e($column['name']); ?>" min-length="5" max-length="100" />
              <?php endif; ?>
            </td>
            <td class="column-type" data-colname="Type">
              <?php if ($column['default'] == 1) : ?>
                <?php _e(ucwords($type . ' field'), 'admin-column-custom'); ?>
              <?php else : ?>
                <select name="<?php echo $option_name; ?>[<?php echo $row; ?>][type]" class="select-type">
                  <?php foreach ($column_types as $value => $title) : ?>
                    <option value="<?php esc_attr_e($value); ?>" <?php esc_attr_e($value == $column['type'] ? 'selected' : ''); ?>><?php esc_attr_e($title); ?></option>
                  <?php endforeach; ?>
                </select>
              <?php endif; ?>
            </td>
            <td class="column-default" data-colname="Default">
              <?php if ($column['default'] == 1) : ?>
                <span class="dashicons dashicons-yes"></span>
              <?php else : ?>
                <input type="hidden" class="input-after" name="<?php echo $option_name; ?>[<?php echo $row++; ?>][after]" value="<?php echo $column['after']; ?>" />
                <a href="#remove" class="btn-remove"><span class="dashicons dashicons-remove"></span></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if($type == 'page') : ?>
    <p>Field: [name,path]</p>
    <?php elseif($type == 'post') : ?>
    <p>Field: [name,excerpt]</p>
    <?php elseif($type == 'user') : ?>
    <p>Field: [name]</p>
    <?php endif; ?>
    <div class="tablenav bottom">
      <div class="alignleft actions bulkactions">
        <input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Changes'); ?>">
      </div>
      <div class="alignright actions bulkactions">
        <button type="button" class="button button-primary btn-add"><?php _e('Add Row'); ?></span>
      </div>
      <br class="clear">
    </div>
  </form>
</div>
<?php

$column_last = end($column_default);

?>
<script type="text/template" id="template_tr">
<tr class="hentry-drag">
  <th scope="row" class="check-column">
    <label class="screen-reader-text field-title" for="cb-select-220"></label>
  </th>
  <td class="column-title column-primary">
    <input type="text" name="<?php echo $option_name; ?>[index][title]" class="input-title" value="" min-length="5" max-length="100" />
    <button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_attr_e('Show more details'); ?></span></button>
  </td>
  <td class="column-name" data-colname="Name">
    <input type="text" name="<?php echo $option_name; ?>[index][name]" class="input-name" value="" min-length="5" max-length="100" />
  </td>
  <td class="column-type" data-colname="Type">
    <select name="<?php echo $option_name; ?>[index][type]" class="select-type">
      <?php foreach ($column_types as $value => $title) : ?>
      <option value="<?php esc_attr_e($value); ?>"><?php esc_attr_e($title); ?></option>
      <?php endforeach; ?>
    </select>
  </td>
  <td class="column-default" data-colname="Default">
    <input type="hidden" name="<?php echo $option_name; ?>[index][after]" class="input-after" value="<?php echo $column_last['name']; ?>" />
    <a href="#remove" class="btn-remove"><span class="dashicons dashicons-remove"></span></a>
  </td>
</tr>
</script>