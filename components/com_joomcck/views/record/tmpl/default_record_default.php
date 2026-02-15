<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die();

$item = $this->item;
$params = $this->tmpl_params['record'];

?>
<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}

.line-brk {
	margin-left: 0px !important;
}
<?php echo $params->get('tmpl_params.css');?>
</style>
<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">

    <?php echo Layout::render('core.single.recordParts.buttonsManage',['current' => $this]) ?>
	<?php echo Layout::render('core.single.recordParts.title',['current' => $this]) ?>

	<?php echo Layout::render('core.single.recordParts.fieldsUngrouped', ['current' => $this]) ?>

	<?php echo Layout::render('core.single.recordParts.fieldsGrouped', ['current' => $this]) ?>

	<?php echo Layout::render('core.single.recordParts.tags',['current' => $this]) ?>

	<?php echo Layout::render('core.single.recordParts.articleInfo', ['current' => $this]) ?>
</article>
