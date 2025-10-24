<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Provides a list of access levels. Access levels control what users in specific
 * groups can see.
 *
 * @see    JAccess
 * @since  1.7.0
 */
class JFormFieldMaccessLevel extends \Joomla\CMS\Form\Field\ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'MaccessLevel';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.7.0
	 */
	protected function getInput()
	{
		$condition = $this->getAttribute('condition', '{}');
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="form-select ' . $this->class . '"' : 'class="form-select"';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
			
		// Get the field options.
		$options = $this->getOptions();

		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('access.level', $this->name, $this->value, $attr, $options, $this->id);
		if($condition != '{}') {

			$out[] = "
			<script>
			(function($){
				var options = [];
				$('#{$this->id} option').each(function(i, e){
					options.push(parseInt($(this).val()));	
				});
				var conditions = JSON.parse('{$condition}');
				var inputs = [];
				var ids = {};
				$.each(conditions, function(i, list){
					var neg = (i.substr(0, 3) == 'not');
					if(neg) {
						i = i.substr(3, i.length - 3);
					}
					i = parseInt(i);

					inputs[i] = [];
					$.each(list, function(k, id){
						var show = neg ? false : true;
						inputs[i][id] = show;
						ids[id] = neg;
					});
				});

				$('#{$this->id}').change(function(){
					var v = parseInt($(this).val());
					$.each(ids, function(id, neg){
						if(inputs[v] == undefined ) {
							(neg ? show(id) : hide(id));
						} else if(inputs[v][id] == true){
							show(id);
						} else if (inputs[v][id] == undefined) {
							(neg ? show(id) : hide(id));
						} else { 
							hide(id);
						}
					});
				});
				
				function show(l) {
					if(l.substr(0, 9) == 'fieldset-') {
						$('#' + l).show() 
					} else {
						$('#tr_' + l).show() 
					}
				}
				function hide(l) {
					if(l.substr(0, 9) == 'fieldset-') {
						$('#' + l).hide() 
					} else {
						$('#tr_' + l).hide() 
					}
				}
			}(jQuery));
			</script>
			";
		}
		return implode('', $out); 
	}
}
