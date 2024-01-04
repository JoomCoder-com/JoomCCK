<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Form\Form;

defined('_JEXEC') or die();
$doc = \Joomla\CMS\Factory::getDocument();


$directory = $this->directory;
?>

<?php switch ($this->params->get('params.select_type', 0)):?>
<?php
	case 0: //
		$imageFiles = \Joomla\Filesystem\Folder::files(JPATH_SITE . '/' . $directory, NULL, $this->params->get('params.show_subfolders', 0), TRUE);
		$images = array(\Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('JOPTION_SELECT_IMAGE')));

		foreach ($imageFiles as $file)
		{
			if (preg_match('#(bmp|gif|jpg|png|webp|avif)$#', $file))
			{
				$file = str_replace(array(JPATH_ROOT, '\\', '//'), array('', '/', '/'), $file);
				$file = ltrim($file, '/');
				$images[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $file, str_replace($directory, '', $file));
			}
		}

		echo \Joomla\CMS\HTML\HTMLHelper::_(
			'select.genericlist',
			$images,
			'jform[fields]['.$this->id.'][image]',
			array(
				'list.attr' => 'class="form-select" size="1" data-image-field-id="'.$this->id.'"',
				'list.select' => @$this->value['image']
			)
		);
	break;
?>

<?php case 1: // joomla core media manager ?>

		<?php

		$form = Form::getInstance("image", JPATH_ROOT . "/components/com_joomcck/fields/image/form/media.xml", ['control' => 'jform[fields]['.$this->id.']']);

		if(isset($this->value['image'])){
			$prefillData = array("image" => stripslashes($this->value['image']));
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
            <input id="imagetitle<?php echo $this->id;?>" type="text"  style="width:400px" placeholder="<?php echo \Joomla\CMS\Language\Text::_('IMAGETITLE');?>" name="jform[fields][<?php echo $this->id;?>][image_title]"
                   value="<?php echo (isset($this->value['image_title']) ? stripslashes($this->value['image_title']) : '');?>" class="form-control"/>
        </div>



	<a class="modal memodal-button btn btn-warning btn-sm" rel="{handler: 'iframe', size: {x: 800, y: 500}}" onclick="return false;"
		href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_media&view=images&folder='.str_replace(array('images/', '/'), '', $directory).'&tmpl=component&fieldid='.$this->id.'&asset=com_media&author=');?>"
		title="<?php echo \Joomla\CMS\Language\Text::_('I_SELECTIMG');?>"><?php echo \Joomla\CMS\Language\Text::_('I_SELECTIMG');?></a>

	<input type="hidden" size="40"  name="jform[fields][<?php echo $this->id;?>][image]" id="jformfields<?php echo $this->id;?>image"
	value="<?php echo (isset($this->value['image']) ? stripslashes($this->value['image']) : '');?>" readonly="readonly"/>
<?php break;?>

<?php case 2: // upload field ?>
	<div class="form-inline">
		<input type="file" name="fields<?php echo $this->id;?>image" id="jformfields<?php echo $this->id;?>image"/>
		<input type="hidden" size="40"  name="jform[fields][<?php echo $this->id;?>][image]" id="jformfields<?php echo $this->id;?>hiddenimage"
		value="<?php echo (isset($this->value['image']) ? stripslashes($this->value['image']) : '');?>"/>
		<button type="button" class="btn btn-danger btn-sm" data-image-path="<?php echo $this->value['image']; ?>" data-image-field-id="<?php echo $this->id;?>"><?php echo \Joomla\CMS\Language\Text::_('F_DELETE_IMAGE')?></button>
	</div>
<?php break;?>

<?php endswitch; ?>

<?php if($this->params->get('params.select_type', 0) !=1): ?>
<div class="card d-inline-block">
	<img src="<?php echo (!empty($this->value['image']) ? \Joomla\CMS\Uri\Uri::root(TRUE).'/'.$this->value['image'] : \Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/blank.png');?>" alt="<?php echo \Joomla\CMS\Language\Text::_('I_IMGPREVIEW');?>"
		 name="imagelib<?php echo $this->id;?>" id="imagelib<?php echo $this->id;?>" style="max-width: 440px; border: 0px solid;" />
	<?php if($this->params->get('params.allow_caption')): ?>
        <div class="form-inline">
            <input id="imagetitle<?php echo $this->id;?>" type="text"  placeholder="<?php echo \Joomla\CMS\Language\Text::_('IMAGETITLE');?>" name="jform[fields][<?php echo $this->id;?>][image_title]"
                   value="<?php echo (isset($this->value['image_title']) ? stripslashes($this->value['image_title']) : '');?>" class="form-control border-0"/>
        </div>
	<?php endif; ?>
</div>
<?php endif; ?>


