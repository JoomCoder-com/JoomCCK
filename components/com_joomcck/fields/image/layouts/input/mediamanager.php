<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);


$directory = $current->directory;

$form = Form::getInstance("image", JPATH_ROOT . "/components/com_joomcck/fields/image/form/media.xml", ['control' => 'jform[fields]['.$current->id.']']);

if(isset($current->value['image'])){
	$prefillData = array("image" => stripslashes($current->value['image']));
	$form->bind($prefillData);
}

// set directory
$directory = str_replace(array('images/', '/'), '', $directory);
$form->setFieldAttribute('image','directory',$directory);


?>

<div class="d-inline-block">
	<div class="mb-2">
		<?php echo $form->getInput('image'); ?>
	</div>
	<input id="imagetitle<?php echo $current->id;?>" type="text"  style="width:400px" placeholder="<?php echo \Joomla\CMS\Language\Text::_('IMAGETITLE');?>" name="jform[fields][<?php echo $current->id;?>][image_title]"
	       value="<?php echo (isset($current->value['image_title']) ? stripslashes($current->value['image_title']) : '');?>" class="form-control"/>
</div>