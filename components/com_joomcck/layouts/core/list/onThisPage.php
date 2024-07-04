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

<?php if($params->get('tmpl_core.show_title_index')):?>

	<div id="jcck-onthispage" class="card mb-4">
		<div class="card-header">
			<h3><?php echo \Joomla\CMS\Language\Text::_('CONTHISPAGE')?></h3>
		</div>
		<ul class="list-group list-group-flush">
			<?php foreach ($items AS $item):?>
				<li class="list-group-item"><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
			<?php endforeach;?>
		</ul>
	</div>
<?php endif;?>