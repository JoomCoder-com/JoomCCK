<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined ( '_JEXEC' ) or die ();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
JHtml::_('dropdown.init');
$this->_filters = true;
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_joomcck&view=auditlog&Itemid='.JFactory::getApplication()->input->getInt('Itemid')); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/audit.png">
			<?php echo JText::_('CAUDITLOG'); ?>
		</h1>
	</div>

	<div class="collapse fade" id="list-filters-box">
		<br>
		<div class="tabbable">
			<button class="btn float-end btn-primary" type="submit">
				<?php echo JText::_('CSEARCH');?>
			</button>
			<ul class="nav nav-tabs" id="filter-tabs">
				<?php if($this->sections):?>
					<li><a href="#section" data-toggle="tab">
					<?php if($this->state->get('auditlog.section_id')):?>
						<?php echo HTMLFormatHelper::icon('exclamation-diamond.png', JText::_('AL_FAPPLIED'));  ?>
					<?php endif;?>
					<?php echo JText::_('ALTAB_CSECITIONS')?></a></li>
				<?php endif;?>


				<?php if($this->types):?>
					<li><a href="#type" data-toggle="tab">
						<?php if($this->state->get('auditlog.type_id')):?>
							<?php echo HTMLFormatHelper::icon('exclamation-diamond.png', JText::_('AL_FAPPLIED'));  ?>
						<?php endif;?>
						<?php echo JText::_('ALTAB_CTYPES')?></a></li>
				<?php endif;?>

				<?php if($this->events):?>
					<li><a href="#event" data-toggle="tab">
						<?php if($this->state->get('auditlog.event_id')):?>
							<?php echo HTMLFormatHelper::icon('exclamation-diamond.png', JText::_('AL_FAPPLIED'));  ?>
						<?php endif;?>
						<?php echo JText::_('ALTAB_EVENTS')?></a></li>
				<?php endif;?>

				<?php if($this->users):?>
					<li><a href="#user" data-toggle="tab">
						<?php if($this->state->get('auditlog.user_id')):?>
							<?php echo HTMLFormatHelper::icon('exclamation-diamond.png', JText::_('AL_FAPPLIED'));  ?>
						<?php endif;?>
						<?php echo JText::_('ALTAB_CUSERS')?></a></li>
				<?php endif;?>

				<li><a href="#date" data-toggle="tab">
					<?php if($this->state->get('auditlog.fce') && $this->state->get('auditlog.fcs')):?>
						<?php echo HTMLFormatHelper::icon('exclamation-diamond.png', JText::_('AL_FAPPLIED'));  ?>
					<?php endif;?>
					<?php echo JText::_('ALTAB_CDATES')?></a>
				</li>
			</ul>

			<div class="tab-content">
				<?php _show_list_filters($this->sections, 'section', $this->state);?>
				<?php _show_list_filters($this->types, 'type', $this->state);?>
				<?php _show_list_filters($this->events, 'event', $this->state);?>
				<?php _show_list_filters($this->users, 'user', $this->state);?>

				<div class="tab-pane" id="date">
					<div class="container-fluid">
						<?php if(@$this->mtime):?>
							<div class="row">
								<p><?php echo JText::sprintf('CALSTARTED', $this->mtime)?></p>
							</div>
						<?php endif;?>
						<div class="row">
							<div class="float-start">
								<label>From</label>
								<?php echo JHtml::calendar((string)$this->state->get('auditlog.fcs'), 'filter_cal_start', 'fcs')?>
							</div>
							<div class="float-end">
								<label>To</label>
								<?php echo JHtml::calendar((string)$this->state->get('auditlog.fce'), 'filter_cal_end', 'fce')?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<br>
		<script>
		  jQuery(function () {
		    jQuery('#filter-tabs a:first').tab('show');
		  })
		</script>
	</div>
	<div class="clearfix"></div>

	<?php if($this->state->get('auditlog.section_id') || $this->state->get('auditlog.type_id')
			|| $this->state->get('auditlog.event_id') || $this->state->get('auditlog.user_id')
			|| ($this->state->get('auditlog.fce') && $this->state->get('auditlog.fcs'))): ?>
		<div class="alert alert-warning">
			<a class="close" data-dismiss="alert" href="#">X</a>
			<p><?php echo HTMLFormatHelper::icon('exclamation-diamond.png', JText::_('AL_FAPPLIED'));  ?> <?php echo JText::_('AL_FILTERS')?></p>
			<button type="button" class="btn btn-warning btn-sm" onclick="Joomla.submitbutton('auditlog.reset')"><?php echo JText::_('AL_RESET')?></button>
		</div>
	<?php endif;?>


	<?php if($this->items): ?>
		<br>
		<table class="table table-hover table-bordered">
		<thead>
			<tr>
				<th width="1%">#</th>
				<!-- <th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"" />-->
				</th>
				<th width="10%">
					<?php echo JText::_('CEVENT');/*JHtml::_('grid.sort',  'CEVENT', 'event', $listDirn, $listOrder)*/; ?>
				</th>
				<th width="1%">
					<?php echo JText::_('CVERS');/*JHtml::_('grid.sort',  'CEVENT', 'event', $listDirn, $listOrder)*/; ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'CRECORD', 'r.title', $listDirn, $listOrder); ?>
				</th>
				<th nowrap="nowrap" width="1%">
					<?php echo JHtml::_('grid.sort',  'CCREATED', 'al.ctime', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  'CEVENTER', 'u.username', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items AS $i => $item):?>
			<?php $params = json_decode($item->params);?>
			<tr class=" <?php echo $k = 1 - @$k?>" id="row-<?php echo $item->id ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<!-- <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>-->
				<td nowrap="nowrap">
					<?php echo JText::_($this->type_objects[$item->type_id]->params->get('audit.al'.$item->event.'.msg', 'CAUDLOG'.$item->event));?>
					<a onclick="Joomcck.checkAndSubmit('#fevent<?php echo $item->event; ?>', <?php echo $item->section_id; ?>)" href="javascript:void(0);" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERTIPEVENTS')?>">
						<?php echo HTMLFormatHelper::icon('funnel-small.png');  ?></a>

					<?php IF($item->event == ATlog::REC_TAGNEW || $item->event == ATlog::REC_TAGDELETE):?>
						<br /> <small><span class="label"><?php settype($params->new, 'array'); echo implode('</span>, <span class="label">', $params->new);?></span></small>
					<?php endif;?>
					<?php IF($item->event == ATlog::REC_FILE_DELETED || $item->event == ATlog::REC_FILE_RESTORED):?>
						<br /> <small>
							<?php //var_dump($params);  ?>
							<?php if(!empty($params->field)):?>
								<span class="label"><?php echo $params->field;?></span>
							<?php endif;?>
							<a href="<?php echo JRoute::_('index.php?option=com_joomcck&task=files.download&id='.@$params->file->id.'&fid='.$params->field_id.'&rid='.$item->record_id) ?>">
								<?php echo @$params->file->realname;?></a>
						</small>
					<?php endif;?>
				</td>
				<td><span class="badge bg-info">v.<?php echo (int)@$params->version;?></span></td>
				<td class="has-context">
					<?php ob_start ();?>

					<?php IF($item->event == ATlog::REC_FILE_DELETED):?>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::_('CRESTOREFILLE');?>" href="<?php echo Url::task('records.rectorefile', $item->record_id.'&fid='.$params->file->id.'&field_id='.$params->file->field_id)?>">
							<?php echo HTMLFormatHelper::icon('universal.png');  ?></a>
					<?php endif;?>
					<?php IF($item->event == ATlog::REC_NEW):?>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::_('CDELETE');?>" href="<?php echo Url::task('records.delete', $item->record_id)?>">
							<?php echo HTMLFormatHelper::icon('cross-button.png');  ?></a>
					<?php endif;?>
					<?php IF($item->event == ATlog::REC_PUBLISHED || ($item->event == ATlog::REC_NEW && @$params->published == 1)):?>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::_('CUNPUB');?>" href="<?php echo Url::task('records.sunpub', $item->record_id); ?>">
							<?php echo HTMLFormatHelper::icon('cross-circle.png');  ?></a>
					<?php endif;?>
					<?php IF($item->event == ATlog::REC_UNPUBLISHED || ($item->event == ATlog::REC_NEW && @$params->published == 0)):?>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::_('CPUB');?>" href="<?php echo Url::task('records.spub', $item->record_id); ?>">
							<?php echo HTMLFormatHelper::icon('tick.png');  ?></a>
					<?php endif;?>
					<?php IF($item->event == ATlog::REC_EDIT && $this->type_objects[$item->type_id]->params->get('audit.versioning')):?>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::_('CCOMPAREVERSION');?>"
							href="<?php echo $url = 'index.php?option=com_joomcck&view=diff&record_id=' . $item->record_id . '&version=' . ($params->version) . '&return=' . Url::back(); ?>">
							<?php echo HTMLFormatHelper::icon('edit-diff.png');  ?></a>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::sprintf('CROLLBACKVERSION', ($params->version - 1));?>"
							href="<?php echo Url::task('records.rollback', $item->record_id.'&version='.($params->version - 1)); ?>">
							<?php echo HTMLFormatHelper::icon('arrow-merge-180-left.png');  ?></a>
					<?php endif;?>
					<?php IF(!$item->isrecord):?>
						<a class="btn btn-sm btn-light border" rel="tooltip" data-original-title="<?php echo JText::_('CRESTORE');?>" href="<?php echo Url::task('records.restore', $item->record_id)?>">
							<?php echo HTMLFormatHelper::icon('universal.png');  ?></a>
					<?php endif;?>
					<?php $controls = ob_get_contents ();?>
					<?php ob_end_clean ()?>

					<?php if(trim($controls)):?>
						<div class="btn-group float-end" style="display: none;">
							<?php echo $controls;?>
						</div>
					<?php endif;?>


					<?php IF($item->isrecord):?>
						<span class="label label-inverse"><?php echo $item->record_id  ?></span>

						<a href="<?php echo JRoute::_(Url::record($item->record_id));?>">
							<?php echo $params->title;?>
						</a>
					<?php else:?>
						<?php echo $params->title;?>
					<?php endif;?>

					<a onclick="Joomcck.setAndSubmit('filter_search', 'rid:<?php echo $item->record_id;?>');" href="javascript:void(0);" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERTIPRECORD')?>;">
						<?php echo HTMLFormatHelper::icon('funnel-small.png');  ?></a>
					<div>
						<small>
							<?php echo JText::_('CTYPE'); ?>:
							<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERTIPETYPE');?>"
									onclick="Joomcck.checkAndSubmit('#ftype<?php echo $item->type_id; ?>', <?php echo $item->type_id; ?>)">
								<?php echo @$params->type_name;?></a> |

							<?php echo JText::_('CSECTION'); ?>:
							<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERTIPSECTION');?>"
									onclick="Joomcck.checkAndSubmit('#fsection<?php echo $item->section_id; ?>', <?php echo $item->section_id; ?>)">
								<?php echo @$params->section_name;?></a>

							<?php if(!empty($params->categories)): ?>
								<?php echo JText::_('CCATEGORY'); ?>:
									<?php foreach($params->categories AS $cat):?>
										<?php echo $cat;?>
									<?php endforeach;?>
							<?php endif;?>
						</small>
					</div>
				</td>
				<td nowrap><?php echo $item->date;?></td>
				<td nowrap>
					<?php echo $item->username;?>
					<a onclick="Joomcck.checkAndSubmit('#fuser<?php echo $item->user_id; ?>', <?php echo $item->section_id; ?>)" href="javascript:void(0);" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERTIPUSER')?>">
						<?php echo HTMLFormatHelper::icon('funnel-small.png');  ?></a>
				</td>
			</tr>
				<?php endforeach;?>
			</tbody>
	</table>
	<?php else: ?>
		<div class="alert alert-warning"><?php echo JText::_('CERR_NOLOG') ?></div>
	<?php endif; ?>

	<div style="text-align: center;">
		<small>
			<?php if($this->pagination->getPagesCounter()):?>
				<?php echo $this->pagination->getPagesCounter(); ?>
			<?php endif;?>
			<?php echo str_replace('<option value="0">'.JText::_('JALL').'</option>', '', $this->pagination->getLimitBox());?>
			<?php echo $this->pagination->getResultsCounter(); ?>
		</small>
	</div>
	<div style="text-align: center;" class="pagination">
		<?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
	</div>


	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
function _show_list_filters($list, $name, $state)
{
	$cols = 3;
	$i = 0;
?>
	<?php if($list):?>
		<div class="tab-pane" id="<?php echo $name;?>">
			<div class="container-fluid">
				<?php foreach ($list AS $item): ?>
					<?php if($i % $cols == 0):?>
					<div class="row">
					<?php endif;?>
						<div class="col-md-4">
							<label class="checkbox">
								<input id="f<?php echo $name.$item->value?>" type="checkbox" <?php echo in_array($item->value, (array)$state->get('auditlog.'.$name.'_id')) ? 'checked="checked"' : NULL;?> name="filter_<?php echo $name?>[]" value="<?php echo $item->value;?>">
								<?php echo $item->text;?>
							</label>
						</div>
					<?php if($i % $cols == ($cols - 1)):?>
					</div>
					<?php endif;$i++;?>
				<?php endforeach;?>
				<?php if($i % $cols != 0):?>
				</div>
				<?php endif;?>
			</div>
		</div>
	<?php endif;?>
<?php }?>