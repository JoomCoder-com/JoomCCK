<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$doc = \Joomla\CMS\Factory::getDocument();
$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/jquery.inputmask/dist/min/inputmask/inputmask.min.js');
$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/jquery.inputmask/dist/min/inputmask/inputmask.date.extensions.min.js');
$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/jquery.inputmask/dist/min/inputmask/inputmask.numeric.extensions.min.js');

$class    = ' class="' . $this->params->get('core.field_class', 'form-control') . ($this->required ? ' required' : NULL) . '" ';
$required = $this->required ? 'required="true" ' : NULL;

$mask = $this->params->get('tmpl_text_input.mask', 'd/m/y');
if((int)$mask == 100) {
	$mask = $this->params->get('tmpl_text_input.custom', 'd/m/y');
}

$moment_format = str_replace(["d","m","y","h","s"], ["DD","MM","YYYY","HH","mm"], $mask);
$comment_format = str_replace(["d","m","y","h","s"], ["31","12","2000","23","59"], $mask);
$php_format = str_replace(["d","m","y","h","s"], ["d","m","Y","H","i"], $mask);
$default = $this->default ? date($php_format, strtotime($this->default)) : '';
?>

<input type="text" value="<?php echo $default; ?>" name="dp_text_<?php echo $this->id; ?>" <?php echo $class . $required; ?> id="dp_text_field_<?php echo $this->id; ?>" data-inputmask="'mask': '<?php echo $mask ?>'"/>
<p><small><?php echo \Joomla\CMS\Language\Text::_('F_FORMAT') ?>: <?php echo $comment_format ?></small></p>

<input type="hidden" id="picker<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $this->default ?>" />

<script type='text/javascript'>
	(function($){
		Inputmask().mask(document.querySelector("#dp_text_field_<?php echo $this->id; ?>"));

		$('#dp_text_field_<?php echo $this->id; ?>')
			.change(function(){
				$('#picker<?php echo $this->id; ?>').val(
					moment(this.value, "<?php echo $moment_format ?>").format('<?php echo $this->db_format ?>')
				);
			});
	}(jQuery))
</script>
			
