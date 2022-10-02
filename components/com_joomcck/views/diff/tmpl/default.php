<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
include_once JPATH_COMPONENT. DIRECTORY_SEPARATOR .'api.php';
$back = NULL;
$user = JFactory::getUser();
if(JFactory::getApplication()->input->getString('return'))
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
	<?php echo JText::_('CAUDITVERSIONSCOMPARE')?> : <?php echo $this->record->title;?> v.<?php echo $this->record->version;?>
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

<button type="button" class="btn" onclick="location.href = '<?php echo $back;?>'">
	<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
	<?php echo JText::_('CGOBACK'); ?>
</button>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="1%"><?php echo JText::_('CFIELD');?></th>
			<th width=""><?php echo JText::_('CCURRENT');?> v.<?php echo $this->record->version;?></th>
			<th width="1%" align="center" style="width: 150px"><?php echo JText::_('CEQUAL');?></th>
			<th width="">
				<?php echo JText::_('CVERSION');?> v.<?php echo $this->item->version;?>
				<?php if($this->versions):?>
					<div class="btn-group float-end">
						<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php foreach ($this->versions as $value):?>
								<li>
									<a href="<?php echo JRoute::_('index.php?option=com_joomcck&view=diff&record_id='.$this->record->id.'&version='.$value->version.'&return='.$this->input->getBase64('return')); ?>">
										<?php echo JText::_('CVERSION');?> v.<?php echo $value->version; ?>
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
		_drawLine(JText::_('CTITLE'), $this->record->title, $this->item->record->title);
		_drawLine(JText::_('CALIAS'), $this->record->alias, $this->item->record->alias);
		_drawLine(JText::_('CCATEGORIES'), json_decode($this->record->categories, TRUE), json_decode($this->item->record->categories, TRUE));
		_drawLine(JText::_('CTAGS'), json_decode($this->record->tags, TRUE), json_decode($this->item->record->tags, TRUE));

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

	return md5($val);
}
function _renderCol($var, $field)
{
	if(!$var) return;

	if($field)
	{
		$record = ItemsStore::getRecord(JFactory::getApplication()->input->getInt('record_id'));
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
		<td nowrap="nowrap" class="key"><?php echo JText::_($field_title); ?></td>
		<td><?php echo _renderCol($cur, $field)?></td>
		<td align="center" class="middlerow" nowrap="nowrap">
			<?php if($cur):?>
				<span class="btn btn-micro" rel="popover-left" data-original-title="<?php echo JText::_('CROAWDATA')?>" data-content="<?php echo str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', nl2br(htmlspecialchars(print_r($cur, TRUE), ENT_COMPAT, 'UTF-8')));?>">
					<?php echo HTMLFormatHelper::icon('control-180-small.png');  ?>
				</span>
			<?php else:?>
				<img src="<?php echo JURI::root(TRUE);?>/media/mint/blank.png" width="26" height="16" />
			<?php endif;?>

			<?php if($equal):?>
				<?php echo HTMLFormatHelper::icon('status.png', JText::_('CEQUAL'));  ?>
			<?php else:?>
				<?php echo HTMLFormatHelper::icon('status-away.png', JText::_('CNOTEQUAL'));  ?>
			<?php endif;?>

			<?php if($ver):?>
				<span class="btn btn-micro" rel="popover-right" data-original-title="<?php echo JText::_('CROAWDATA')?>" data-content="<?php echo str_replace('    ', '&nbsp;&nbsp;&nbsp;&nbsp;', nl2br(htmlspecialchars(print_r($ver, TRUE), ENT_COMPAT, 'UTF-8')));?>">
					<?php echo HTMLFormatHelper::icon('control-000-small.png');  ?>
				</span>
			<?php else:?>
				<img src="<?php echo JURI::root(TRUE);?>/media/mint/blank.png" width="26" height="16" />
			<?php endif;?>
		</td>
		<td><?php echo _renderCol($ver, $field)?></td>
	</tr>
<?php
}
?>