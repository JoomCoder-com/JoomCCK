<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$params = $this->params;
$class = ' class="' . $this->params->get('core.field_class', 'form-control') . ($this->required ? ' required' : NULL) .'"';
$required = $this->required ? 'required="true" ' : NULL;

$value = $this->value ? $this->value : null;
?>

<div class="input text">
	<table cellpadding="2" cellspacing="2" class="tel_table">
		<tr>
			<td>
				<span class="flag_icon" id="flag<?php echo $this->id;?>">
					<?php if ($this->flag): ?>
						<img src="<?php echo JURI::root(TRUE);?>/media/mint/<?php echo ($this->flag ? 'icons/flag/16/'.\Joomla\String\StringHelper::strtolower($this->flag->code2) : 'blank');?>.png" border="0" 
						align="absmiddle" alt="<?php echo JText::_($this->flag->name);?>" title="<?php echo JText::_($this->flag->name);?>">
					<?php endif;?>
				</span> 
				+<br>&nbsp;
			</td>
			<td>
				<input autocomplete="off" class="input-mini" id="field_<?php echo $this->id;?>_cnt" type="text" name="jform[fields][<?php echo $this->id;?>][country]" 
					size="3" data-autocompleter-default="<?php echo (isset($value['country']) ? $value['country'] : '');?>"
					value="<?php echo (isset($value['country']) ? $value['country'] : '');?>"/>
				<br><small id="field_<?php echo $this->id;?>_cntname"><?php echo JText::_('T_COUNTRY');?></small>
		       	<script type="text/javascript">
            	   InitAutocomplete("<?php echo $this->id;?>");
               	</script>
           	</td>
        	<td>
        	   	<input class="input-mini" id="field_<?php echo $this->id;?>_reg" type="text" name="jform[fields][<?php echo $this->id;?>][region]" onkeyup="Joomcck.formatInt(this)"
        	   		size="3"  value="<?php echo (isset($value['region']) ? $value['region'] : '');?>"/>      	    
        	   		<br><small id="field_<?php echo $this->id;?>_cntname"><?php echo JText::_('T_REGION');?></small>
        	</td>
        	<td>
        	  	<input class="input-small" id="field_<?php echo $this->id;?>_tel" type="text" name="jform[fields][<?php echo $this->id;?>][tel]"  onkeyup="Joomcck.formatInt(this)"
        	   		size="7" maxlength="7" value="<?php echo (isset($value['tel']) ? $value['tel'] : '');?>" />
        	   	<br><small id="field_<?php echo $this->id;?>_cntname"><?php echo JText::_('T_TEL');?></small>      	    
        	</td>
		<?php if ($params->get('params.extension')) : ?>
			<td>#<br>&nbsp;</td>
			<td>
        	    <input class="input-mini" id="field_<?php echo $this->id;?>_ext" type="text" name="jform[fields][<?php echo $this->id;?>][ext]"  onkeyup="Joomcck.formatInt(this)"
        	    	size="3"  value="<?php echo (isset($value['ext']) ? $value['ext'] : '');?>" />  
        	    <br><small id="field_<?php echo $this->id;?>_cntname"><?php echo JText::_('T_EXTENSION');?></small>    	    
        	</td>
        <?php endif; ?>
		</tr>
	</table>
</div>

