<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$app = JFactory::getApplication();
$params = $this->params;
$text = htmlspecialchars(stripslashes( (string) $this->value), ENT_QUOTES, 'UTF-8');
$height = $params->get('params.height', '300');
?>

<?php if ($params->get('params.short', 0) && !$app->isClient('administrator')):?>
	<?php echo $this->editor->display('jform[fields]['. $this->id.']', $text, '100%', $height, '60', '20', $this->buttons, 'field_'.$this->id, null, $this->user->id, $this->editorParams);?>
<?php else : ?>
	<?php
	echo $this->editor->display('jform[fields]['. $this->id.']', $text, '100%', $height, '60', '20', $this->buttons, 'field_'.$this->id, null, $this->user->id);?>
<?php endif; ?>

	<div class="clearfix"></div>
		
<?php $m = array();
if (in_array($params->get('params.allow_html', 3), $this->user->getAuthorisedViewLevels())) :
	$m[] = JText::_('H_TAGSALLOWED');
else :
	$m[] = JText::_('H_SOMETAGSALLOWED');
	
	$tags = explode(',', $params->get('params.filter_tags'));
	ArrayHelper::trim_r($tags);
	ArrayHelper::clean_r($tags);
	$li[] = ($this->params->get('params.tags_mode', 0) ? JText::_("H_FOLLOWINGTAGSNOTALLOWED") : JText::_('H_FOLLOWINGTAGSALLOED')) . ': ' . htmlspecialchars('<' . implode('>, <', $tags) . '>');
	
	$attr = explode(',', $params->get('params.filter_attr'));
	ArrayHelper::trim_r($attr);
	ArrayHelper::clean_r($attr);
	$li[] = ($this->params->get('params.attr_mode', 0) ? JText::_('H_FOLLOWINGATTRSNOTALLOWED') : JText::_('H_FOLLOWINGATTRSALLOWED')) . ': ' . htmlspecialchars(implode('="", ', $attr)) . '=""';
	
	$m[] = '<ul><li>' . implode('</li><li>', $li) . '</li></ul>';
	
	if ($m) :?>
	<br><small><?php echo implode("\n", $m);?></small>
	<?php endif; ?>
<?php endif; ?>