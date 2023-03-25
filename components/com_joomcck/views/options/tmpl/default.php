<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$back = NULL;
$user = JFactory::getUser();
$app = JFactory::getApplication();
if($app->input->getString('return'))
{
	$back = Url::get_back('return');
}
$num = 0;
$num = CEventsHelper::showNum('total', 0);
$r=0;
$section = $this->section;
?>
<style>
div.btn-group[data-toggle=buttons-radio] input[type=radio] {
  display:    block;
  position:   absolute;
  top:        0;
  left:       0;
  width:      100%;
  height:     100%;
  opacity:    0;
}​
</style>

<div class="page-header">
	<button class="btn float-end" type="button" onclick="window.location = '<?php echo JRoute::_('index.php?option=com_joomcck&view=notifications')?>'">
		<?php echo HTMLFormatHelper::icon('bell.png');  ?>
		<?php echo JText::_('CNOTCENTR');?>
		<?php if($num):?>
			<?php echo $num?>
		<?php endif;?>
	</button>
	<h1 class="title">
		<?php echo JText::sprintf('CNOTCENTERCONF');?>
	</h1>
</div>


<form name="adminForm" id="adminForm" method="post" class="form-horizontal">
	<div class="btn-toolbar clearfix">
		<div class="float-end">
			<button class="btn" onclick="Joomla.submitbutton('options.saveoptions')">
				<?php echo HTMLFormatHelper::icon('disk.png');  ?>
				<?php echo JText::_('CSAVE');?>
			</button>

			<?php if($back):?>
				<button class="btn" type="button" onclick="Joomla.submitbutton('options.saveoptionsclose')">
					<?php echo HTMLFormatHelper::icon('disk--minus.png');  ?>
					<?php echo JText::_('CSAVECLOSE');?>
				</button>
			<?php endif;?>
		</div>

		<?php if($back):?>
			<button type="button" class="btn float-start" onclick="location.href = '<?php echo $back;?>'">
				<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
				<?php echo JText::_('CBACKTOSECTION'); ?>
			</button>
		<?php endif;?>
	</div>


	<legend><?php echo JText::_('CEMAILSETINGS');?></legend>
	<div class="control-group">
		<div class="control-label col-md-2"><?php echo $this->form->getLabel('schedule'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('schedule'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label col-md-2"><?php echo $this->form->getLabel('language'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('language'); ?></div>
	</div>


	<legend><?php echo JText::_('CSECTIONS');?></legend>
	<div class="tabbable tabs-left">
		<ul class="nav nav-tabs nav-stacked">
			<?php foreach ($this->usub AS $k => $s):?>
				<li <?php echo ($s->id == $app->input->getInt('section_id') ? 'class="active"' : NULL)?>>
					<a href="<?php echo JRoute::_('index.php?option=com_joomcck&view=options&section_id='.$s->id.'&return='.$app->input->getString('return'));?>"><?php echo $s->name;?></a>
				</li>
			<?php endforeach;?>
		</ul>

		<div class="tab-content">

			<?php if(!empty($this->section->id)):?>
				<div class="btn-toolbar">
					<div class="float-end">
						<?php echo $this->sec_follow;?>
					</div>
				</div>
				<label class="float-start checkbox">
					<input <?php if(isset($this->data['autofollow'][$section->id])) {echo ' checked="checked"';} ?>
						type="checkbox" name="jform[autofollow][<?php echo $section->id?>]"
						value="<?php echo $section->id?>" />
						<?php echo JText::_('CAUTSUBDESC')?>
				</label>
				<div class="clearfix"> </div>
				<br />

				<div class="tabbable">
					<ul class="nav nav-tabs" id="tabs-list">
						<?php if(in_array($this->section->params->get('events.subscribe_category'), $user->getAuthorisedViewLevels()) && $this->section->categories):?>
							<li class=""><a href="#page-catfollows" data-toggle="tab"><?php echo JText::_('CCATFOOLOWS')?></a></li>
						<?php endif; ?>
						<?php if(in_array($this->section->params->get('events.subscribe_user'), $user->getAuthorisedViewLevels()) && $this->section->params->get('personalize.personalize')):?>
							<li class=""><a href="#page-userfollows" data-toggle="tab"><?php echo JText::_('CUSERFOOLOWS')?></a></li>
						<?php endif; ?>
						<li class=""><a href="#page-recfollows" data-toggle="tab"><?php echo JText::_('CARTFOOLOWS')?></a></li>
						<?php if(!empty($this->notyfications['r']) || !empty($this->notyfications['c'])):?>
							<li class=""><a href="#page-notif" data-toggle="tab"><?php echo JText::_('CNOTIFICATIONS')?></a></li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="tab-content well" id="tabs-content">

					<!-- Categories tab -->
					<?php if(in_array($this->section->params->get('events.subscribe_category'), $user->getAuthorisedViewLevels()) && $this->section->categories):?>
						<div class="tab-pane" id="page-catfollows">
							<?php if($this->section->follow == 1 ):?>
								<p><?php echo JText::_('CYOUFOLLOWALLCAT')?></p>
								<?php if($this->cats):?>
									<p><?php echo JText::_('CYOUFOLLOWALLCATEXP')?></p>
								<?php endif;?>
							<?php else:?>
								<?php if(!$this->cats):?>
									<p><?php echo JText::_('CYOUFOLLOWALLCATNO')?></p>
								<?php endif;?>
							<?php endif;?>
							<?php if($this->cats):?>
							<table class="table table-striped">
								<thead>
									<th width="1%">#</th>
									<th><?php echo JText::_('CTITLE')?></th>
									<th width="1%"><?php echo JText::_('CACTION')?></th>
								</thead>
								<tbody>
									<?php foreach ($this->cats AS $k => $cat):?>
										<tr class="list-row cat-list-row<?php echo $r = 1 - $r; ?>">
											<td><?php echo $k;?></td>
											<td><a href="<?php echo JRoute::_(Url::records($section, $cat));?>"><?php echo $cat->title;?></a></td>
											<td nowrap="nowrap"><?php echo HTMLFormatHelper::followcat($cat->id, $section);?></td>
										</tr>
									<?php endforeach;?>
								</tbody>
							</table>
							<?php endif;?>
						</div>
					<?php endif;?>

					<!-- User tab -->
					<?php if(in_array($this->section->params->get('events.subscribe_user'), $user->getAuthorisedViewLevels()) && $this->section->params->get('personalize.personalize')):?>
						<div class="tab-pane" id="page-userfollows">
							<?php if($this->section->follow == 1 ):?>
								<p><?php echo JText::_('CYOUFOLLOWALLUSR')?></p>
								<?php if($this->users):?>
									<p><?php echo JText::_('CYOUFOLLOWALLUSREXP')?></p>
								<?php endif;?>
							<?php else:?>
								<?php if(!$this->users):?>
									<p><?php echo JText::_('CYOUFOLLOWALLUSRNO')?></p>
								<?php endif;?>
							<?php endif;?>
							<?php if(count($this->users)):?>
							<table class="table table-striped">
								<thead>
									<th width="1%">#</th>
									<th><?php echo JText::_('CTITLE')?></th>
									<th width="1%"><?php echo JText::_('CACTION')?></th>
								</thead>
								<tbody>
									<?php foreach ($this->users AS $k => $u):?>
										<tr class="list-row cat-list-row<?php echo $r = 1 - $r; ?>">
											<td><?php echo $k;?></td>
											<td><?php echo CCommunityHelper::getName($u->id, $section);?></td>
											<td nowrap="nowrap"><?php echo HTMLFormatHelper::followuser($u->id, $section);?></td>
										</tr>
									<?php endforeach;?>
								</tbody>
							</table>
							<?php endif; ?>
						</div>
					<?php endif;?>

					<!-- Articles tab -->
					<div class="tab-pane" id="page-recfollows">
						<div class="alert alert-info" id="rec-msg" style="display: none;">

						</div>
						<div class="clearfix"></div>
						<div class="float-end">
							<button type="button" <?php echo ($section->records ? NULL : 'style="display: none;"');?> class="btn" id="unsub-<?php echo $section->id?>" onclick="Joomcck.ajax_unsubscr_sec(<?php echo $section->id;?>)">
								<?php echo JText::_('CUNSUBREC')?>
								<span class="badge bg-light text-muted border"><?php echo $section->records;?></span>
							</button>
							<button class="btn" type="button" <?php echo (($section->records_total && ($section->records_total > $section->records)) ? NULL : 'style="display: none;"');?> id="sub-<?php echo $section->id?>" onclick="Joomcck.ajax_subscr_sec(<?php echo $section->id;?>)" href="javascript:void(0);">
								<?php echo JText::_('CSUBRECART')?>
								<span class="badge bg-light text-muted border"><?php echo $section->records_total - $section->records;?></span>
							</button>
						</div>
						<div id="bro-ba" style="width:50px; display:none;" class="progress progress-striped active float-start">
							<div class="bar" style="width: 100%;"></div>
						</div>
						<div class="clearfix"></div>

						<br />
						<?php if(!empty($this->articles)):?>
							<table class="table table-striped" id="table-recs">
								<thead>
									<th width="1%">#</th>
									<th><?php echo JText::_('CTITLE')?></th>
									<th width="1%"><?php echo JText::_('CACTION')?></th>
								</thead>
								<tbody>
									<?php foreach ($this->articles AS $k => $record):?>
										<tr class="list-row cat-list-row<?php echo $r = 1 - $r; ?>">
											<td><?php echo $this->pag->getRowOffset($k); ?></td>
											<td><?php echo $record->title;?></td>
											<td><?php echo HTMLFormatHelper::follow($record, ItemsStore::getSection($record->section_id))?></td>
										</tr>
									<?php endforeach;?>
								</tbody>
							</table>
							<div class="pagination">
								<p class="counter">
									<?php echo $this->pag->getPagesCounter(); ?>
									<?php echo $this->pag->getLimitBox();?>
								</p>
								<?php echo $this->pag->getPagesLinks(); ?>
							</div>
						<?php else:?>
							<div class="alert alert-warning" id="no-recs"><?php echo JText::_('CYODONOTFOLLOW')?></div>
						<?php endif;?>
					</div>

					<!-- Notifications tab -->
					<?php if(!empty($this->notyfications['r']) || !empty($this->notyfications['c'])):?>
						<div class="tab-pane form-horizontal" id="page-notif">
						<?php if($this->notyfications['r']):?>
						 	<legend><?php echo JText::_('CRECNOTIF')?></legend>
							<div class="row">
								<?php foreach ($this->notyfications['r'] AS $type):?>
									<div class="control-group">
										<div class="control-label span4"><?php echo ucfirst(JText::sprintf('alert_event_'.$type, strtolower($section->params->get('general.item_label', 'item'))))?></div>
										<div class="controls"><?php yesnobutton($type, $section->id, $this->data);?></div>
									</div>
								<?php endforeach;?>
							</div>
						<?php endif;?>
						<?php if($this->notyfications['c']):?>
							<legend><?php echo JText::_('CCOMNOTIF')?></legend>
							<div class="row">
								<?php foreach ($this->notyfications['c'] AS $type):?>
									<div class="control-group">
										<div class="control-label span4"><?php echo JText::_('alert_event_'.$type)?></div>
										<div class="controls"><?php yesnobutton($type, $section->id, $this->data);?></div>
									</div>
								<?php endforeach;?>
							</div>
						<?php endif;?>
						</div>
					<?php endif;?>

				</div>
				<script>
					(function($){
						$('.btn-notify').each(function(index){
							var y = $('.btn-yes', this);
							var n = $('.btn-no', this);
							y.on('click', function(){
								y.addClass('btn-success');
								n.removeClass('btn-danger');
							});
							n.on('click', function(){
								n.addClass('btn-danger');
								y.removeClass('btn-success');
							});
						});

						$('#tabs-list').children('li')[0].addClass('active');
						$('#tabs-content').children('div.tab-pane')[0].addClass('active');
					}(jQuery))

				</script>

			<?php else:?>
				<div class="alert alert-warning"><?php echo JText::_('CPLEASESELECTSECTION');?></div>
			<?php endif;?>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
function yesnobutton($type, $section_id, $default)
{
	$yes = !empty($default['notification'][$section_id][$type]) ? TRUE : (isset($default['notification'][$section_id]) ? FALSE : TRUE);
	?>
	<div class="btn-group btn-notify" data-toggle="buttons-radio">
		<span class="btn-yes btn<?php echo $yes ? ' active btn-success' : NULL ?>">
			<?php echo JText::_('JYES')?>
			<input <?php echo ($yes ? ' checked="checked"' : NULL);?> type="radio" name="jform[notification][<?php echo $section_id?>][<?php echo $type?>]" value="1" />
		</span>
		<span class="btn-no btn<?php echo !$yes ? ' active btn-danger' : NULL ?>">
			<?php echo JText::_('JNO')?>
			<input <?php echo (!$yes ? ' checked="checked"' : NULL);?> type="radio" name="jform[notification][<?php echo $section_id?>][<?php echo $type?>]" value="0" />
		</span>
	</div>
	<?php
}
?>
<script>
!function($)
{
	Joomcck.ajax_unsubscr_sec = function (id)
	{
		if(!confirm('<?php echo JText::_('CSURE')?>')) return;
		$('#bro-ba').slideDown('quick', function(){
			$.ajax({
				url: '<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.unfollowallsection&tmpl=component', FALSE); ?>',
				dataType: 'json',
				type: 'POST',
				data:{
					section_id:id
				}
			}).done(function(data) {
				$('#bro-ba').slideUp('slow');

				if(!data) return;

				if(data.error)
				{
					$('#rec-msg').append(data.error).removeClass('alert-info').addClass('alert-error').slideDown('slow');
					setTimeout(function(){
						$('#rec-msg').slideUp('quick')}, 5000);
					return;
				}

				// empty all records
				// alert message
				$('#rec-msg').addClass('alert-info').html('<?php echo JText::sprintf('CUNFOLLOEDOK', "' + data.rows + '")?>')
					.slideDown('slow');

				setTimeout(function(){
					$('#rec-msg').slideUp('quick')}, 5000);
				$('#table-recs').slideUp('quick');
				$('div.pagination').remove();
				$('#unsub-<?php echo $section->id?>').css('display', 'none');
				//$('#sub-<?php echo $section->id?>').css('display', 'inline');
			});
		});
	}
	Joomcck.ajax_subscr_sec = function (id)
	{
		$('#bro-ba').slideDown('quick', function(){
			$.ajax({
				url: '<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.followallsection&tmpl=component', FALSE); ?>',
				dataType: 'json',
				type: 'POST',
				data:{
					section_id:id
				}
			}).done(function(data) {
				$('#bro-ba').slideUp('slow');

				if(!data) return;

				if(data.error)
				{
					$('#rec-msg').append(data.error).removeClass('alert-info').addClass('alert-error').slideDown('slow');
					setTimeout(function(){
						$('#rec-msg').slideUp('quick')}, 5000);
					return;
				}

				// empty all records
				// alert message
				$('#rec-msg').addClass('alert-info').html('<?php echo JText::sprintf('CFOLLOEDOK', "' + data.rows + '")?>')
					.slideDown('slow');

				setTimeout(function(){
					$('#rec-msg').slideUp('quick')}, 5000);
				$('#sub-<?php echo $section->id?>').css('display', 'none');
				//$('#unsub-<?php echo $section->id?>').css('display', 'inline');
				var r = $('#no-recs');
				if(r) r.remove();
			});
		});
	}
}(jQuery);
</script>