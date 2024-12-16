<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

HTMLHelper::_('bootstrap.collapse');

?>


<button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#email_form<?php echo $current->key;?>" aria-expanded="false" aria-controls="collapseExample">
	<?php echo Text::_($current->params->get('params.popup_label', $current->label));?>
</button>
<div class="collapse my-3" id="email_form<?php echo $current->key;?>">
	<div class="row h-100">
		<div class="col-md-6">
			<div class="card card-body h-100">
				<iframe onload="resizeIframe(this)" id="email_frame<?php echo $current->key;?>" loading="lazy" src="<?php echo $current->url_form ?>" width="100%" height="99%" frameborder="0"></iframe>
			</div>
		</div>
	</div>
</div>
