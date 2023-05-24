<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */


defined('_JEXEC') or die();


extract($displayData);

?>

<ul class="list-group">
	<?php foreach ($items as $item): ?>
		<li class="list-group-item"><?php echo $item['text'] ?></li>
	<?php endforeach; ?>
</ul>


