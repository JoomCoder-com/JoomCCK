<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

extract($displayData);

$directory = $current->directory;

$imageFiles = \Joomla\Filesystem\Folder::files(JPATH_SITE . '/' . $directory, NULL, $current->params->get('params.show_subfolders', 0), TRUE);
$images = array(HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('JOPTION_SELECT_IMAGE')));

foreach ($imageFiles as $file)
{
	if (preg_match('#(bmp|gif|jpg|png|webp|avif)$#', $file))
	{
		$file = str_replace(array(JPATH_ROOT, '\\', '//'), array('', '/', '/'), $file);
		$file = ltrim($file, '/');
		$images[] = HTMLHelper::_('select.option', $file, str_replace($directory, '', $file));
	}
}
?>

<div class="mb-3">
	<?php echo HTMLHelper::_(
		'select.genericlist',
		$images,
		'jform[fields]['.$current->id.'][image]',
		array(
			'list.attr' => 'class="form-select" size="1" data-image-field-id="'.$current->id.'"',
			'list.select' => @$current->value['image']
		)
	); ?>
</div>

<?php echo Layout::render('preview',$displayData,__DIR__) ?>