<?php 
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>

<h1><?php echo JText::_('CEDITTAG'); ?> </h1>
<br>
<form action="index.php" method="post" id="adminForm" name="adminForm">

	<TABLE class="admintable" width="100%">
    <TR>
      <TD class="key"><?php echo JText::_('CTAG'); ?>:</TD>
      <td><input type="text" name="title" value="<?php echo $this->item->tag; ?>" size="45" /></td>
    </TR>  
  </table>
  <br>
  <br>
  <div class="button_holder" id="button_holder">
    <div class="button1" style="width:30%">
      <div class="next">
        <a onclick="submitbutton('save');"><?php echo JText::_('CSAVECHANGES'); ?></a>
      </div>
    </div>
  </div>
  <div class="clr"></div>
  <br>
  <input type="hidden" name="option"       value="<?php echo $option; ?>" />
  <input type="hidden" name="task"         value="" />
  <input type="hidden" name="controller"   value="category" />
  <input type="hidden" name="id"           value="<?php echo $this->item->id?>" />
  <input type="hidden" name="hidemainmenu" value="0" />
</form>
