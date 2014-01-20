<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
{
	exit();
}

function xfac_options_init()
{
	$config = xfac_option_getConfig();
	
	$tagForumMappings = get_option('xfac_tag_forum_mappings');
	if (!is_array($tagForumMappings))
	{
		$tagForumMappings = array();
	}

	$tags = get_terms('post_tag', array('hide_empty' => false));

	if (!empty($config))
	{
		$forums = xfac_api_getForums($config);
	}
	else
	{
		$forums = null;
	}
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br />
	</div><h2><?php _e('XenForo API Consumer', 'xenforo-api-consumer'); ?></h2>

	<form method="post" action="options.php">
		<?php settings_fields('xfac'); ?>

		<table class="form-table">
			<?php if (xfac_option_getWorkingMode() === 'network'): ?>
			<tr valign="top">
				<th scope="row"><label for="xfac_root"><?php _e('API Root', 'xenforo-api-consumer'); ?></label></th>
				<td>
				<input name="xfac_root" type="text" id="xfac_root" value="<?php echo esc_attr($config['root']); ?>" class="regular-text" disabled="disabled" />
				</td>
			</tr>
			<?php else: ?>
			<tr valign="top">
				<th scope="row"><label for="xfac_root"><?php _e('API Root', 'xenforo-api-consumer'); ?></label></th>
				<td>
				<input name="xfac_root" type="text" id="xfac_root" value="<?php echo esc_attr($config['root']); ?>" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="xfac_client_id"><?php _e('Client ID', 'xenforo-api-consumer'); ?></label></th>
				<td>
				<input name="xfac_client_id" type="text" id="xfac_client_id" value="<?php echo esc_attr($config['clientId']); ?>" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="xfac_client_secret"><?php _e('Client Secret', 'xenforo-api-consumer'); ?></label></th>
				<td>
				<input name="xfac_client_secret" type="text" id="xfac_client_secret" value="<?php echo esc_attr($config['clientSecret']); ?>" class="regular-text" />
				</td>
			</tr>
			<?php endif; ?>

			<tr valign="top">
				<th scope="row"><label for="xfac_tag_forum_mappings"><?php _e('Tag / Forum Mappings', 'xenforo-api-consumer'); ?></label></th>
				<td>
					<?php
						foreach(array_values($tagForumMappings) as $i => $tagForumMapping)
						{
							if (empty($tagForumMapping['term_id']) OR empty($tagForumMapping['forum_id']))
							{
								continue;
							}

							_xfac_options_renderTagForumMapping($tags, $forums, $i, $tagForumMapping);
						}

						if (empty($tags))
						{
							_e('No tags found', 'xenforo-api-consumer');
						}
						elseif (empty($forums))
						{
							_e('No forums found', 'xenforo-api-consumer');
						}
						else
						{
							_xfac_options_renderTagForumMapping($tags, $forums, ++$i, null);
						}
					?>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes'); ?>"  />
		</p>
	</form>

</div>

<?php
}

function _xfac_options_renderTagForumMapping($tags, $forums, $i, $tagForumMapping)
{
	// generate fake forum in case we lost connection
	if (empty($forums['forums']) AND !empty($tagForumMapping['forum_id']))
	{
		$forums = array('forums' => array(array(
			'forum_id' => $tagForumMapping['forum_id'],
			'forum_title' => '#' . $tagForumMapping['forum_id'],
		)));
	}
	
?>
<div class="<?php echo ($tagForumMapping ? 'TagForumMapping_Record' : 'TagForumMapping_Template'); ?>" data-i="<?php echo $i; ?>">
	<select name="xfac_tag_forum_mappings[<?php echo $i; ?>][term_id]">
		<option value="0">&nbsp;</option>
		<?php foreach ($tags as $tag): ?>
			<option value="<?php echo esc_attr($tag->term_id); ?>"<?php if (!empty($tagForumMapping['term_id']) AND $tagForumMapping['term_id'] == $tag->term_id) echo ' selected="selected"'; ?>><?php echo esc_html($tag->name); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="xfac_tag_forum_mappings[<?php echo $i; ?>][forum_id]">
		<option value="0">&nbsp;</option>
		<?php foreach ($forums['forums'] as $forum): ?>
			<option value="<?php echo esc_attr($forum['forum_id']); ?>"<?php if (!empty($tagForumMapping['term_id']) AND $tagForumMapping['forum_id'] == $forum['forum_id']) echo ' selected="selected"'; ?>><?php echo esc_html($forum['forum_title']); ?></option>
		<?php endforeach; ?>
	</select>
</div>
<?php
}

function xfac_wpmu_options()
{
	$config = xfac_option_getConfig();
	
?>

<h3><?php _e('XenForo API Consumer', 'xenforo-api-consumer'); ?></h3>
<table id="xfac" class="form-table">
	<tr valign="top">
		<th scope="row"><label for="xfac_root"><?php _e('API Root', 'xenforo-api-consumer'); ?></label></th>
		<td>
		<input name="xfac_root" type="text" id="xfac_root" value="<?php echo esc_attr($config['root']); ?>" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="xfac_client_id"><?php _e('Client ID', 'xenforo-api-consumer'); ?></label></th>
		<td>
		<input name="xfac_client_id" type="text" id="xfac_client_id" value="<?php echo esc_attr($config['clientId']); ?>" class="regular-text" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="xfac_client_secret"><?php _e('Client Secret', 'xenforo-api-consumer'); ?></label></th>
		<td>
		<input name="xfac_client_secret" type="text" id="xfac_client_secret" value="<?php echo esc_attr($config['clientSecret']); ?>" class="regular-text" />
		</td>
	</tr>
</table>

<?php
}
add_action('wpmu_options', 'xfac_wpmu_options');

function xfac_update_wpmu_options()
{
	$options = array(
		'xfac_root',
		'xfac_client_id',
		'xfac_client_secret',
	);

	foreach ($options as $optionName)
	{
		if (!isset($_POST[$optionName]))
		{
			continue;
		}

		$optionValue = wp_unslash($_POST[$optionName]);
		update_site_option($optionName, $optionValue);
	}
}
add_action('update_wpmu_options', 'xfac_update_wpmu_options');
