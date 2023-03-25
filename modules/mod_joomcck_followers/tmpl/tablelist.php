<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<table class="table table-condensed">
	<?php foreach ($list as $id):?>
	  <tr>
	    <td width="22px"><img class="<?php echo $params->get('ava_style', 'img-polaroid');?>" src="<?php echo CCommunityHelper::getAvatar($id, $params->get('ava_size', 32), $params->get('ava_size', 32));?>" /></td>
	    <td><?php echo CCommunityHelper::getName($id, $section);?></td>
	  </tr>
	<?php endforeach;?>
</table>
