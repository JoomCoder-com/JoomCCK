<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$schema = $this->params->get('params.additional_fields');
$fields = @$data['add_field'];
settype($fields, 'array');
$add_fields_info = explode("\n", str_replace("\r", '', $schema));
?>

<table width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('E_URL')?></td>
        <td><a target="blank" href="<?php echo \Joomla\CMS\Router\Route::_(Url::record($record), TRUE, -1);?>"><?php echo $config->get('sitename');?></a></td>
    </tr>
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('E_TITLE') ?></td>
        <td><?php echo $record->title?></td>
    </tr>
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('E_FROM')?></td>
        <td><?php echo $data['name'] . ' (' . $data['email_from'] . ')'?></td>
    </tr>
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('E_MSG')?></td>
        <td><?php echo nl2br(strip_tags($data['body']))?></td>
    </tr>
	<?php if($schema && isset($data['add_field'])): ?>
		<?php foreach($add_fields_info as $f): ?>
			<?php
			if(!trim($f)) continue;
			$field_info = explode('::', $f);
			if(count($field_info) <= 2) continue;
			?>
			<tr>
				<td><?php echo \Joomla\CMS\Language\Text::_(trim($field_info[2])); ?></td>
				<td><?php echo (is_array($fields[$field_info[1]]) ? implode(',', $fields[$field_info[1]]) : nl2br($fields[$field_info[1]])) ?></td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>