<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');


include_once JPATH_COMPONENT. DIRECTORY_SEPARATOR .'api.php';
$back = NULL;
$user = \Joomla\CMS\Factory::getUser();
if(\Joomla\CMS\Factory::getApplication()->input->getString('return'))
{
	$back = Url::get_back('return');
}
if(!$back)
{
	$back = Url::record($this->record);
}
JHTML::_('bootstrap.popover', '[rel="popover-left"]', array('placement' => 'left', 'trigger' => 'click'));
JHTML::_('bootstrap.popover', '[rel="popover-right"]', array('placement' => 'right', 'trigger' => 'click'));
?>
<h1>
	<?php echo \Joomla\CMS\Language\Text::_('CAUDITVERSIONSCOMPARE')?> : <?php echo $this->record->title;?> v.<?php echo $this->record->version;?>
</h1>

<style>
td.key {
	max-width: 120px;
	overflow: hidden;
	font-weight: bold;
}
.popover-content {
	font-size: 11px;
}
</style>

<button type="button" class="btn btn-light border" onclick="location.href = '<?php echo $back;?>'">
	<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
	<?php echo \Joomla\CMS\Language\Text::_('CGOBACK'); ?>
</button>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="1%"><?php echo \Joomla\CMS\Language\Text::_('CFIELD');?></th>
			<th width=""><?php echo \Joomla\CMS\Language\Text::_('CCURRENT');?> v.<?php echo $this->record->version;?></th>
			<th width="1%" align="center" style="width: 150px"><?php echo \Joomla\CMS\Language\Text::_('CEQUAL');?></th>
			<th width="">
				<?php echo \Joomla\CMS\Language\Text::_('CVERSION');?> v.<?php echo $this->item->version;?>
				<?php if($this->versions):?>
					<div class="btn-group float-end">
						<a class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" href="#"><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php foreach ($this->versions as $value):?>
								<li>
									<a  class="dropdown-item" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=diff&record_id='.$this->record->id.'&version='.$value->version.'&return='.$this->input->getBase64('return')); ?>">
										<?php echo \Joomla\CMS\Language\Text::_('CVERSION');?> v.<?php echo $value->version; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		_drawLine(\Joomla\CMS\Language\Text::_('CTITLE'), $this->record->title, $this->item->record->title);
		_drawLine(\Joomla\CMS\Language\Text::_('CALIAS'), $this->record->alias, $this->item->record->alias);
		_drawLine(\Joomla\CMS\Language\Text::_('CCATEGORIES'), json_decode($this->record->categories, TRUE), json_decode($this->item->record->categories, TRUE));
		_drawLine(\Joomla\CMS\Language\Text::_('CTAGS'), json_decode((string)$this->record->tags, TRUE), json_decode((string)$this->item->record->tags, TRUE));

		if(!empty($this->all_field_keys))
		{
			foreach ($this->all_field_keys as $field_id)
			{
				if(!isset($this->fields[$field_id])) continue;

				$cur =  isset($this->current_fields[$field_id]) ? $this->current_fields[$field_id] : '';
				$ver =  isset($this->version_fields[$field_id]) ? $this->version_fields[$field_id] : '';

				$field = $this->fields[$field_id];

				_drawLine($field->label,  $cur, $ver, $field);
			}
		}
		?>
	</tbody>
</table>

<?php
function _getValMd5($val)
{
	if(is_object($val) || is_array($val))
	{
		$val = json_encode($val);
	}

	return md5((string)$val);
}
function _renderCol($var, $field)
{
	if(!$var) return;

	if($field)
	{
		$record = ItemsStore::getRecord(\Joomla\CMS\Factory::getApplication()->input->getInt('record_id'));
		return JoomcckApi::renderField($record, $field->id, JoomcckApi::FIELD_FULL, $var);
	}

	if(is_array($var))
	{
		$var = implode(', ', $var);
	}

	return $var;
}

function _drawLine($field_title, $cur, $ver, $field = FALSE)
{
	$equal = _getValMd5($cur) == _getValMd5($ver);
?>
	<tr class="<?php echo $equal ? '' : 'error'; ?>">
		<td nowrap="nowrap" class="key"><?php echo \Joomla\CMS\Language\Text::_($field_title); ?></td>
		<td><?php echo _renderCol($cur, $field)?></td>
		<td align="center" class="middlerow" nowrap="nowrap">
			<?php if($cur):?>
				<span class="btn btn-micro" rel="popover-left" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CROAWDATA')?>" data-content="<?php echo str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', nl2br(htmlspecialchars(print_r($cur, TRUE), ENT_COMPAT, 'UTF-8')));?>">
					<?php echo HTMLFormatHelper::icon('control-180-small.png');  ?>
				</span>
			<?php else:?>
				<img src="<?php echo JURI::root(TRUE);?>/media/com_joomcck/blank.png" width="26" height="16" />
			<?php endif;?>

			<?php if($equal):?>
				<?php echo HTMLFormatHelper::icon('status.png', \Joomla\CMS\Language\Text::_('CEQUAL'));  ?>
			<?php else:?>
				<?php echo HTMLFormatHelper::icon('status-away.png', \Joomla\CMS\Language\Text::_('CNOTEQUAL'));  ?>
			<?php endif;?>

			<?php if($ver):?>
				<span class="btn btn-micro" rel="popover-right" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CROAWDATA')?>" data-content="<?php echo str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', nl2br(htmlspecialchars(print_r($ver, TRUE), ENT_COMPAT, 'UTF-8')));?>">
					<?php echo HTMLFormatHelper::icon('control-000-small.png');  ?>
				</span>
			<?php else:?>
				<img src="<?php echo JURI::root(TRUE);?>/media/com_joomcck/blank.png" width="26" height="16" />
			<?php endif;?>
		</td>
		<td><?php echo _renderCol($ver, $field)?></td>
	</tr>
<?php
}
?>