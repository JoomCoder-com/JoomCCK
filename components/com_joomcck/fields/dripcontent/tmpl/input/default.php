<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$user = JFactory::getUser();
$list[] = JHtml::_('select.option', 'manual', JText::_('XML_OPT_AMMANUAL'));
$list[] = JHtml::_('select.option', 'auto', JText::_('XML_OPT_AMAUTO'));
if($this->params->get('params.quiz'))
{
	$list[] = JHtml::_('select.option', 'quiz', JText::_('XML_OPT_QUIZ'));
}
?>

<?php echo $this->inputvalue;?>

<div class="clearfix"></div>

<?php if(in_array($this->params->get('params.who_set_mode'), $user->getAuthorisedViewLevels())): ?>
	<h3><?php echo JText::_('F_ACTIVATE'); ?></h3>
	<div class="form-inline">
		<?php echo JHtml::_('select.genericlist', $list, "jform[fields][$this->id][method]", null, 'value', 'text', $this->method); ?>

		<label id="dripmanual<?php echo $this->id; ?>" class="hide"></label>
		<label id="dripauto<?php echo $this->id; ?>" class="hide">
			<?php echo JText::sprintf('F_INNUMBEROFDAYS',
				'<input type="text" style="width:20px;" value="'.$this->days.'" name="jform[fields]['.$this->id.'][days]">'); ?>
		</label>
		<?php if($this->params->get('params.quiz')): ?>
			<label id="dripquiz<?php echo $this->id; ?>" class="hide">
				<?php echo $this->_get_quiz_select($this->quiz); ?>
			</label>
		<?php endif; ?>
	</div>

	<script type="text/javascript">
		(function($){
			$('#jformfields<?php echo $this->id; ?>method').change(function(){
				updatestate(this.value);
			});

			updatestate($('#jformfields<?php echo $this->id; ?>method').val());

			function updatestate(m) {
				$('#dripmanual<?php echo $this->id; ?>').hide();
				$('#dripauto<?php echo $this->id; ?>').hide();
				$('#dripquiz<?php echo $this->id; ?>').hide();
				$('#drip' + m + '<?php echo $this->id; ?>').show();
			}
		}(jQuery))
	</script>
<?php endif;?>

<?php if($this->plans):?>
	<?php echo $this->plans;?>
<?php endif;?>
