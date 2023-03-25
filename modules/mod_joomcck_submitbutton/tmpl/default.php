<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php foreach($types AS $type): ?>
	<?php
	$attr = "";
	if(!in_array($type->params->get('submission.submission'), JFactory::getUser()->getAuthorisedViewLevels()))
	{
		if(!JFactory::getUser()->get('id'))
		{
			$url = JRoute::_('index.php?option=com_users&view=login&return=' . Url::back());
			$attr = ' rel="tooltip" data-original-title="'.JText::sprintf('MOD_SB_REGISTER', $type->name).'" ';
		}
		else
		{
			continue;
		}
	}
	else
	{
		$url = JRoute::_(Url::add($section, $type, $category));
	}
	?>
	<a class="btn btn-block btn-large btn-primary btn-addnew" href="<?php echo $url; ?>"<?php echo $attr; ?>>
		<?php echo JText::sprintf($params->get('label', 'Add New Article'), $type->name); ?></a>
<?php endforeach; ?>