<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$m_list = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
if(!function_exists('dp_get_selected')){
	function dp_get_selected($default, $current, $type) {
		if(empty($default)) {
			return;
		}
		$date = JFactory::getDate($default);
		if((int)$current == $date->{$type}) {
			return 'selected';
		}
		return;
	}
}
?>

<input type="hidden" id="picker<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $this->default ?>" />
<div id="dp_simple<?php echo $this->id ?>" class="inline-form">
	<select <?php echo $this->attr ?> class="date_list" style="width:50px" name="dp_day_<?php echo $this->id;?>">
		<option value="0"><?php echo  JText::_('D_DAY');?></option>
		<?php for($i = 1; $i <= 31; $i ++):?>
			<option value="<?php echo $i;?>" <?php echo dp_get_selected($this->default, $i, 'day');?>>
				<?php echo $i;?>
			</option>
		<?php endfor; ?>
	</select>
	<select <?php echo $this->attr ?> class="date_list" style="width:100px" name="dp_month_<?php echo $this->id;?>">
		<option value="0"><?php echo  JText::_('D_MONTH');?></option>
		<?php for($i = 1; $i <= 12; $i ++): ?>
			<option value="<?php echo $i;?>" <?php echo dp_get_selected($this->default, $i, 'month');?>>
				<?php echo JText::_($m_list[($i - 1)]);?>
			</option>
		<?php endfor; ?>
	</select>
	<select <?php echo $this->attr ?> class="date_list" style="width:70px" name="dp_year_<?php echo $this->id;?>">
		<option value="0"><?php echo  JText::_('D_YEAR');?></option>
		<?php for($i = (date('Y') + $this->params->get('tmpl_simple_select.year_up', 80)); $i >= date('Y') - $this->params->get('tmpl_simple_select.year_down', 15); $i --):	?>
		<option value="<?php echo $i;?>" <?php echo dp_get_selected($this->default, $i, 'year');?>>
			<?php echo $i;?>
		</option>
		<?php endfor; ?>
	</select>
	<?php if($this->is_time): ?>
		<select <?php echo $this->attr ?> class="date_list" style="width:50px" name="dp_hour_<?php echo $this->id;?>">
			<option value=""><?php echo  JText::_('D_HOUR');?></option>
			<?php for($i = 0; $i <= 23; $i ++): ?>
			<option value="<?php echo $i;?>" <?php echo dp_get_selected($this->default, $i, 'hour');?>>
				<?php echo str_pad($i, 2, 0, STR_PAD_LEFT);?>
			</option>
			<?php endfor; ?>
		</select>

		<select <?php echo $this->attr ?> class="date_list" style="width:50px" name="dp_min_<?php echo $this->id;?>">
			<option value=""><?php echo  JText::_('D_MINUTE');?></option>
			<?php for($i = 0; $i < 60; $i ++): ?>
			<option value="<?php echo $i;?>" <?php echo dp_get_selected($this->default, $i, 'minute');?>>
				<?php echo str_pad($i, 2, 0, STR_PAD_LEFT);?>
			</option>
			<?php endfor; ?>
		</select>
	<?php endif; ?>
</div>
<script>
(function($){
	$('#dp_simple<?php echo $this->id ?> select').change(function(){
		update_time();
	});
	function update_time() {
		var all = true;
		var obj = {};
		$('#dp_simple<?php echo $this->id ?> select').each(function(index, element){
			if(element.value == 0) {
				all = false;
			}
			switch(element.name.substr(0, 5)) {
				case 'dp_ye': obj.year = element.value; break;
				case 'dp_mo': obj.month = element.value - 1; break;
				case 'dp_da': obj.day = element.value; break;
				case 'dp_ho': obj.hour = element.value; break;
				case 'dp_mi': obj.minute = element.value; break;
			}
		})
		if(all) {
			$('#picker<?php echo $this->id; ?>').val(moment(obj).format('<?php echo $this->db_format ?>'))
		}
	}
}(jQuery))
</script>