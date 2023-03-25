<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();
if($this->items)
{
	$this->_filters = TRUE;
}

HTMLHelper::_('bootstrap.collapse');

?>
	<script type="text/javascript">
		(function($) {
			Joomcck.removeNtf = function(id) {
				$.ajax({
					url: '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.remove_notification&tmpl=component", FALSE); ?>',
					dataType: 'json',
					type: 'POST',
					context: $('#ntfctn-' + id),
					data: {id: id}
				}).done(function(json) {
					if(!json) {
						return;
					}
					if(!json.success) {
						alert(json.error);
						return;
					}

					if(id == 'all') {
						location.reload();
					}
					else {
						$(this).remove();
					}
				});
			};

			Joomcck.removeNtfBy = function(type) {
				if(!confirm('<?php echo JText::_("EVENT_CONFIRM_DELETE")?>')) {
					return;
				}

				if(type == 'selected') {
					var inputs = $('[name^="ntfcs"]:checked');
				}
				else {
					var inputs = $('[name^="clear_' + type + '"]:checked');
				}

				var list = [];
				$.each(inputs, function(key, val) {
					list.push($(val).val());
				});

				$.ajax({
					url: '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.remove_notification_by&tmpl=component", FALSE); ?>',
					dataType: 'json',
					type: 'POST',
					data: {type: type, list: list}
				}).done(function(json) {
					if(!json) {
						return;
					}
					if(!json.success) {
						alert(json.error);
						return;
					}
					location.reload();
				});
			};

			Joomcck.markRead = function(id) {
				$.ajax({
					url: '<?php echo JRoute::_("index.php?option=com_joomcck&task=ajax.mark_notification&tmpl=component", FALSE); ?>',
					dataType: 'json',
					type: 'POST',
					context: $('#ntfctn-' + id),
					data: {id: id, client: 'component'}
				}).done(function(json) {
					if(!json) {
						return;
					}
					if(!json.success) {
						alert(json.error);
						return;
					}
					if(id == 'all') {
						$('tr.info').removeClass('info');
						$('[id^="btn-mark-"]').remove();
					}
					else {
						$(this).removeClass('info');
						$('#btn-mark-' + id).remove();
					}
				});
			};

			Joomcck.selectAll = function(checked, type) {
				if(type == 'selected') {
					var inputs = $('[name^="ntfcs"]');
				} else {
					var inputs = $('[name^="clear_' + type + '"]');
				}

				$.each(inputs, function(key, obj) {
					if(checked) {
						obj.checked = true;
					} else {
						obj.checked = false;
					}

				});
			};
		}(jQuery));
	</script>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

	<form action="<?php echo JRoute::_('index.php?option=com_joomcck&view=notifications&Itemid=' . JFactory::getApplication()->input->getInt('Itemid')); ?>"
		  method="post" name="adminForm" id="adminForm">

		<?php if($this->state->get('filter.search') || (!$this->state->get('filter.search') && $this->items)): ?>
			<?php echo HTMLFormatHelper::layout('search', $this); ?>
		<?php endif; ?>

		<div class="page-header">
			<h1>
				<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/bell.png">
				<?php echo JText::_('CNOTIFICATIONS'); ?>
			</h1>
		</div>

		<div class="collapse" id="list-filters-box">
			<div class="well well-small">
				<?php echo $this->list['show_new']; ?>
				<?php echo $this->list['events']; ?>
				<?php echo $this->list['sections']; ?>
			</div>
		</div>
		<div class="clearfix"></div>

		<div>
			<div class="float-start">
				<?php if($this->items) : ?>
					<button class="btn" type="button" onclick="Joomcck.markRead('all');">
						<?php echo HTMLFormatHelper::icon('asterisk.png'); ?>
						<?php echo JText::_("CMARKALLREAD"); ?>
					</button>
					<button class="btn" type="button" data-bs-toggle="collapse" data-target="#clear_list_block"
							rel="{onClose: function() {}}">
						<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
						<?php echo JText::_("CCLEAREVENTS"); ?>
					</button>
				<?php endif; ?>
			</div>
			<div class="float-end">
				<button class="btn float-end" type="button"
						onclick="window.location = '<?php echo JRoute::_('index.php?option=com_joomcck&view=options') ?>'">
					<?php echo HTMLFormatHelper::icon('gear.png'); ?>
					<?php echo JText::_('CEVENTS_SETTINGS'); ?>
				</button>
			</div>
		</div>
		<div class="clearfix"></div>

		<?php if($this->items) : ?>
			<div class="tabbable fade collapse" id="clear_list_block">
				<br>
				<ul class="nav nav-tabs">
					<li class="active"><a href="#page-selected" data-bs-toggle="tab"><?php echo JText::_('CCLEARSELECTED') ?></a></li>
					<li><a href="#page-events" data-toggle="tab"><?php echo JText::_('CCLEARBYEVENTTYPES') ?></a></li>
					<li><a href="#page-sections" data-toggle="tab"><?php echo JText::_('CCLEARBYSECTIONS') ?></a></li>
					<li><a href="#page-user" data-toggle="tab"><?php echo JText::_('CCLEARBYUSER') ?></a></li>

				</ul>
				<div class="tab-content">
					<div class="tab-pane active form-inline" id="page-selected">
						<label class="checkbox">
							<input type="checkbox" value="1"
								   onclick="Joomcck.selectAll(this.checked, 'selected'); return;"> <?php echo JText::_('CCHECKALL'); ?>
						</label>

						<div class="form-actions">
							<button class="btn" type="button" onclick="Joomcck.removeNtfBy('selected'); return;">
								<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
								<?php echo JText::_("CCLEARSELECTED"); ?>
							</button>
							<button class="btn" type="button" onclick="Joomcck.removeNtfBy('read'); return;">
								<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
								<?php echo JText::_("CCLEARREAD"); ?>
							</button>
							<button class="btn float-end btn-danger" type="button"
									onclick="if(!confirm('<?php echo JText::_("EVENT_CONFIRM_DELETE") ?>') ) {return;} Joomcck.removeNtf('all'); return;">
								<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
								<?php echo JText::_("CCLEARHISTORY"); ?>
							</button>
						</div>
					</div>
					<div class="tab-pane" id="page-events">
						<label class="checkbox">
							<input type="checkbox" value="1"
								   onclick="Joomcck.selectAll(this.checked, 'event'); return;"> <?php echo JText::_('CCHECKALL'); ?>
						</label>
						<table class="table">
							<?php
							$tmp = 0;
							foreach($this->list['clear_list'] as $key => $list):?>
								<?php if($tmp % 2 == 0): ?><tr><?php endif; ?>
								<td><label class="checkbox"><input type="checkbox" name="clear_event"
																   value="<?php echo $key; ?>"> <?php echo $list; ?></label>
								</td>
								<?php if($tmp % 2 == 1): ?></tr><?php endif; ?>
								<?php
								$tmp++;
							endforeach; ?>
						</table>

						<div class="form-actions">
							<button class="btn" type="button" onclick="Joomcck.removeNtfBy('event'); return;">
								<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
								<?php echo JText::_("JSEARCH_FILTER_CLEAR"); ?>
							</button>
						</div>
					</div>

					<div class="tab-pane" id="page-sections">
						<label class="checkbox">
							<input type="checkbox" value="1"
								   onclick="Joomcck.selectAll(this.checked, 'section'); return;"> <?php echo JText::_('CCHECKALL'); ?>
						</label>
						<table class="table">
							<?php $tmp = 0;
							foreach($this->sections as $section):
								$section = ItemsStore::getSection($section); ?>
								<?php if($tmp % 2 == 0): ?><tr><?php endif; ?>
								<td><label class="checkbox"><input type="checkbox" name="clear_section"
																   value="<?php echo $section->id; ?>"> <?php echo $section->name; ?>
										<span class="badge bg-light text-muted border"><?php echo $this->num_sections[$section->id]; ?></span></label>
								</td>
								<?php if($tmp % 2 == 1): ?></tr><?php endif; ?>
								<?php $tmp++;
							endforeach; ?>
						</table>
						<div class="form-actions">
							<button class="btn" type="button" onclick="Joomcck.removeNtfBy('section'); return;">
								<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
								<?php echo JText::_("JSEARCH_FILTER_CLEAR"); ?>
							</button>
						</div>
					</div>

					<div class="tab-pane" id="page-user">
						<label class="checkbox">
							<input type="checkbox" value="1"
								   onclick="Joomcck.selectAll(this.checked, 'eventer'); return;"> <?php echo JText::_('CCHECKALL'); ?>
						</label>
						<table class="table">
							<?php $tmp = 0;
							foreach($this->users as $user => $num):
								$name = CCommunityHelper::getName($user, $this->section_id); ?>
								<?php if($tmp % 2 == 0): ?><tr><?php endif; ?>
								<td><label class="checkbox"><input type="checkbox" name="clear_eventer"
																   value="<?php echo $user; ?>"> <?php echo $name; ?> <span
											class="badge bg-light text-muted border"><?php echo $num; ?></span></label></td>
								<?php if($tmp % 2 == 1): ?></tr><?php endif; ?>
								<?php $tmp++;
							endforeach; ?>
						</table>
						<div class="form-actions">
							<button class="btn" type="button" onclick="Joomcck.removeNtfBy('eventer'); return;">
								<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
								<?php echo JText::_("JSEARCH_FILTER_CLEAR"); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>


			<?php if(count($this->sort_items['today'])): ?>
				<?php drowEventTable(JText::_('CTODAY'), $this->sort_items['today']); ?>
			<?php endif; ?>

			<?php if(count($this->sort_items['yesterday'])): ?>
				<?php drowEventTable(JText::_('CYESTERDAY'), $this->sort_items['yesterday']); ?>
			<?php endif; ?>

			<?php if(count($this->sort_items['thisweek'])): ?>
				<?php drowEventTable(JText::_('CTHISWEEK'), $this->sort_items['thisweek']); ?>
			<?php endif; ?>

			<?php if(count($this->sort_items['lastweek'])): ?>
				<?php drowEventTable(JText::_('CLASTWEEK'), $this->sort_items['lastweek']); ?>
			<?php endif; ?>

			<?php if(count($this->sort_items['older'])): ?>
				<?php drowEventTable(JText::_('COLDER'), $this->sort_items['older']); ?>
			<?php endif; ?>

			<div style="text-align: center;">
				<small>
					<?php if($this->pagination->getPagesCounter()): ?>
						<?php echo $this->pagination->getPagesCounter(); ?>
					<?php endif; ?>
					<?php echo str_replace('<option value="0">' . JText::_('JALL') . '</option>', '', $this->pagination->getLimitBox()); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</small>
			</div>
			<div style="text-align: center;" class="pagination">
				<?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
			</div>

		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="limitstart" value="0"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>

<?php function drowEventTable($caption, $list)
{
	?>
	<h3><?php echo $caption; ?></h3>
	<table class="table table-hover table-bordered">
		<thead>
		<tr>
			<th></th>
			<th><?php echo JText::_('CEVENT') ?></th>
			<th><?php echo JText::_('CDATE') ?></th>
			<th width="50px"><?php echo JText::_('CACT') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($list as $item) : ?>
			<tr id="ntfctn-<?php echo $item->id; ?>" class="<?php if($item->state_new)
			{
				echo 'info';
			} ?>">
				<td>
					<input type="checkbox" name="ntfcs" value="<?php echo $item->id; ?>">
				</td>
				<td>
					<span><?php echo $item->html; ?></span>
				</td>
				<td nowrap>
					<span><?php echo $item->date ?></span>
				</td>
				<td nowrap>
					<div class="btn-group">
						<?php if($item->state_new): ?>
							<a class="btn btn-micro" id="btn-mark-<?php echo $item->id ?>"
							   onclick="Joomcck.markRead(<?php echo $item->id; ?>);" rel="tooltip"
							   data-original-title="<?php echo JText::_('CMARKASREAD') ?>">
								<?php echo HTMLFormatHelper::icon('asterisk.png'); ?>
							</a>
						<?php endif; ?>
						<a class="btn btn-micro"
						   onclick="if(!confirm('<?php echo JText::_("EVENT_CONFIRM_DELETE") ?>') ) {return;} Joomcck.removeNtf(<?php echo $item->id; ?>);">
							<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
						</a>
					</div>
				</td>
			</tr>
		<?php endforeach;; ?>
		</tbody>
	</table>

<?php } ?>