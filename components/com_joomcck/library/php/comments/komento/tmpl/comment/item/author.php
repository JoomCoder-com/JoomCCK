<?php
/**
 * @package        Komento
 * @copyright    Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
$notifications = CEventsHelper::showNum('comment', $row->id);
$section = ItemsStore::getSection($row->extension->_item->section_id);
?>
<?php echo $notifications; ?>
<span <?php if($system->konfig->get('enable_schema')) {
	echo ' itemprop="creator" itemscope itemtype="http://schema.org/Person"';
} ?>>
	<?php echo CCommunityHelper::getName($row->created_by, $section); ?>
</span>
