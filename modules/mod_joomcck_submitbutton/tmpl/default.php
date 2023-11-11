<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

?>
<?php foreach($types AS $type): ?>
	<?php
	$attr = "";
	if(!in_array($type->params->get('submission.submission'), \Joomla\CMS\Factory::getUser()->getAuthorisedViewLevels()))
	{
		if(!\Joomla\CMS\Factory::getUser()->get('id'))
		{
			$url = \Joomla\CMS\Router\Route::_('index.php?option=com_users&view=login&return=' . Url::back());
			$attr = ' rel="tooltip" data-bs-title="'.\Joomla\CMS\Language\Text::sprintf('MOD_SB_REGISTER', $type->name).'" ';
		}
		else
		{
			continue;
		}
	}
	else
	{
		$url = \Joomla\CMS\Router\Route::_(Url::add($section, $type, $category));
	}
	?>
	<a class="btn btn-block btn-large btn-primary btn-addnew" href="<?php echo $url; ?>"<?php echo $attr; ?>>
		<?php echo \Joomla\CMS\Language\Text::sprintf($params->get('label', 'Add New Article'), $type->name); ?></a>
<?php endforeach; ?>