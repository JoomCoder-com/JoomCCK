<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

extract($displayData);



?>
<div class="accordion" id="sectionFormFields">
<?php foreach($types as $i => $type_id): ?>

	<?php

		if(!$type_id)	continue; // skip empty types

		$def  = !empty($default['types'][$type_id]) ? $default['types'][$type_id] : array();
		$type = ItemsStore::getType($type_id);

		$form = new \Joomla\CMS\Form\Form('params', array(
		'control' => 'params[types][' . $type_id . ']'
		));

		$form->loadFile($file, TRUE, 'config');

		$f = new SimpleXMLElement('<field name="categoryselect_ss" type="radio" class="btn-group"  default="1" label="XML_LABEL_SP_CATEGORYSELECT"><option value="0">CNO</option><option value="1">CYES</option></field>');

		$form->setField($f, 'list_tmpl');


	?>

	<div class="accordion-item">
		<h2 class="accordion-header" id="Headertype<?php echo $type->id ?>">
			<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#type<?php echo $type->id ?>" aria-expanded="true" aria-controls="type<?php echo $type->id ?>">
				<?php echo $type->name ?>
			</button>
		</h2>
		<div id="type<?php echo $type->id ?>" class="accordion-collapse collapse <?php echo $i == 0 ? 'show' : '' ?>" aria-labelledby="Headertype<?php echo $type->id ?>" data-bs-parent="#sectionFormFields">
			<div class="accordion-body">
				<?php

				echo MFormHelper::renderFieldset($form, 'sp_type_templates', $def, NULL);
				echo MFormHelper::renderFieldset($form, 'sp_type_content', $def, NULL);
				echo MFormHelper::renderFieldset($form, 'sp_type_fields', $def, NULL);

				?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>





