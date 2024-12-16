<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$modalData = [
	'url' => $current->url_form,
	'id' => 'emailmodal'.$current->id,
	'title' => strip_tags(Text::_($current->params->get('params.popup_label', $current->label)))
];

// onclick="getEmailIframe('<?php echo $current->key; echo $current->url_form;

?>

<button
        class="btn btn-primary btn-sm"
        data-bs-target="#emailmodal<?php echo $current->id;?>"
        data-bs-toggle="modal"
        role="button"
>
	<?php echo Text::_($current->params->get('params.popup_label', $current->label)) ?>
</button>

<?php echo Layout::render('core.bootstrap.modalIframe',$modalData,null,['client' => 'site','component' => 'com_joomcck']) ?>


