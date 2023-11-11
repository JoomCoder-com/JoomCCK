<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<ul class="nav nav-pills">
	<li><a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=1&section_id='.$this->input->get('section_id')); ?>"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD')?></a></li>
	<li><a><?php echo \Joomla\CMS\Language\Text::_('CIMPORTCONFIG')?></a></li>
	<li class="active"><a><?php echo \Joomla\CMS\Language\Text::_('CIMPORTFINISH')?></a></li>
</ul>

<p><?php echo \Joomla\CMS\Language\Text::_('CSUCCESIMPORT');  ?></p>

<table class="table table-bordered table-condensed table-striped">
	<tbody>
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('CMPORTTOTAL')  ?></td>
		<td><?php echo $this->statistic->get('new', 0) +  $this->statistic->get('old', 0) ?></td>
	</tr>
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('CMPORTNEW')  ?></td>
		<td><?php echo $this->statistic->get('new', 0)  ?></td>
	</tr>
	<tr>
		<td><?php echo \Joomla\CMS\Language\Text::_('CMPORTOLD')  ?></td>
		<td><?php echo $this->statistic->get('old', 0)  ?></td>
	</tr>
	</tbody>
</table>