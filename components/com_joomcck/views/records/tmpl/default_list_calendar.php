<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$params = $this->tmpl_params['list'];

$doc = JFactory::getDocument();

$doc->addStyleSheet(JUri::root(TRUE) . '/components/com_joomcck/fields/datetime/assets/css/calendar.min.css');

$tag = JFactory::getLanguage()->getTag();
$file = '/components/com_joomcck/fields/datetime/assets/language/' . $tag . '.js';
if(JFile::exists(JPATH_ROOT . $file))
{
	$doc->addScript(JUri::root(TRUE) . $file);
}

$doc->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/datetime/assets/underscore-min.js');
$doc->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/datetime/assets/calendar.min.js');

?>

<?php if($params->get('tmpl_params.cal_weekday')): ?>
	<select id="first_day" class="float-end">
		<option value="1"><?php echo JText::_('COFDMO'); ?></option>
		<option value="2"><?php echo JText::_('COFDSU'); ?></option>
	</select>
	<div class="clearfix"></div>
<?php endif; ?>
	<table width="100%">
		<tr>
			<td>
				<?php if($params->get('tmpl_params.cal_nav')): ?>
					<div class="float-start form-inline">
						<div class="btn-group">
							<button class="btn btn-sm btn-primary"
									data-calendar-nav="prev"><?php echo HTMLFormatHelper::icon('arrow-180.png'); ?> <?php echo JText::_('Prev'); ?>
							</button>
							<button class="btn-sm btn-light border" data-calendar-nav="today"><?php echo JText::_('Today'); ?></button>
							<button class="btn btn-sm btn-primary" data-calendar-nav="next">
								<?php echo JText::_('Next'); ?> <?php echo HTMLFormatHelper::icon('arrow.png'); ?></button>
						</div>
					</div>
				<?php endif; ?>
			</td>
			<td align="center"><h3 id="cal-title"></h3></td>
			<td>
				<div class="float-end form-inline">
					<div class="btn-group">
						<?php if($params->get('tmpl_params.cal_view_year')): ?>
							<button class="btn btn-sm btn-warning"
									data-calendar-view="year"><?php echo JText::_('Year'); ?></button>
						<?php endif; ?>
						<?php if($params->get('tmpl_params.cal_view_month')): ?>
							<button class="btn btn-sm btn-warning active"
									data-calendar-view="month"><?php echo JText::_('Month'); ?></button>
						<?php endif; ?>
						<?php if($params->get('tmpl_params.cal_view_week')): ?>
							<button class="btn btn-sm btn-warning"
									data-calendar-view="week"><?php echo JText::_('Week'); ?></button>
						<?php endif; ?>
						<?php if($params->get('tmpl_params.cal_view_day')): ?>
							<button class="btn btn-sm btn-warning"
									data-calendar-view="day"><?php echo JText::_('Day'); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<div class="clearfix"></div>
	<br>

	<div id="calendar"></div>

	<?php
	$url = 'index.php?option=com_joomcck&task=ajax.field_call&tmpl=component&section_id='.$this->section->id.'&field_id='.$params->get('tmpl_params.field_id_cal').'&func=getCalendarEvents';
	if($this->input->get('cat_id'))
		$url .= '&cat_id='.$this->input->get('cat_id');
	if($this->input->get('view_what'))
	{
		$url .= '&view_what='.$this->input->get('view_what');

		if($this->input->get('_rrid'))
			$url .= '&_rrid='.$this->input->get('_rrid');
		if($this->input->get('_rfid'))
			$url .= '&_rfid='.$this->input->get('_rfid');
		if($this->input->get('_rmrid'))
			$url .= '&_rmrid='.$this->input->get('_rmrid');
		if($this->input->get('_rmfid'))
			$url .= '&_rmfid='.$this->input->get('_rmfid');
		if($this->input->get('_rmstrict'))
			$url .= '&_rmstrict='.$this->input->get('_rmstrict');
	}
	if($this->input->get('user_id'))
		$url .= '&user_id='.$this->input->get('user_id');
	?>
	<script type="text/javascript">
		(function($) {
			var calendar = $('#calendar').calendar({
				events_source:   '<?php echo JRoute::_($url, FALSE); ?>',
				view:            '<?php echo $params->get('tmpl_params.view', 'month'); ?>',
				tmpl_path:       '<?php echo JUri::root(TRUE); ?>/components/com_joomcck/fields/datetime/assets/tmpls/',
				tmpl_cache: false,
				day:             '<?php echo $params->get('tmpl_params.cal_start', date('Y-m-d')); ?>',
				first_day:       1,
				language:        '<?php echo $tag; ?>',
				views:           {
					year:  {
						enable: <?php echo (int)$params->get('tmpl_params.cal_view_year'); ?>
					},
					month: {
						enable: <?php echo (int)$params->get('tmpl_params.cal_view_month'); ?>
					},
					week:  {
						enable: <?php echo (int)$params->get('tmpl_params.cal_view_week'); ?>
					},
					day:   {
						enable: <?php echo (int)$params->get('tmpl_params.cal_view_day'); ?>
					}
				},
				onAfterViewLoad: function(view) {
					$('h3#cal-title').text(this.getTitle());
					$('.btn-group button').removeClass('active');
					$('button[data-calendar-view="' + view + '"]').addClass('active');
				}
			});

			$('.btn-group button[data-calendar-nav]').each(function() {
				var $this = $(this);
				$this.click(function() {
					calendar.navigate($this.data('calendar-nav'));
				});
			});

			$('.btn-group button[data-calendar-view]').each(function() {
				var $this = $(this);
				$this.click(function() {
					calendar.view($this.data('calendar-view'));
				});
			});

			$('#first_day').change(function() {
				calendar.setOptions({first_day: $(this).val()});
				calendar.view();
			});
		}(jQuery))
	</script>


<?php if($params->get('tmpl_core.tmpl_list')): ?>
	<br>
	<h2><?php echo JText::_($params->get('tmpl_core.list_title')); ?></h2>
	<?php
	$tmpl = explode('.', $params->get('tmpl_core.tmpl_list'));
	$this->section->params->set('general.tmpl_list', $params->get('tmpl_core.tmpl_list'));
	$this->tmpl_params['list'] = CTmpl::prepareTemplate('default_list_', 'tmpl_core.tmpl_list', $params);
	include_once(JPATH_ROOT . "/components/com_joomcck/views/records/tmpl/default_list_{$tmpl[0]}.php");
	?>
<?php endif; ?>