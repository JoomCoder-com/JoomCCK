<?php 
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<script  type="text/javascript" language="javascript">

var callBackFunction = function ( dd, ident )
{
  alert( dd );
}

</script>

<form action="index.php" method="post" id="adminForm" name="adminForm">
  <div class="col width-50">
    <fieldset>
    <legend><?php echo \Joomla\CMS\Language\Text::_('CPRODTMPL'); ?></legend>
      <TABLE class="admintable" width="100%">
        <TR>
          <TD class="key"><?php echo \Joomla\CMS\Language\Text::_('CNAME'); ?>:</TD>
          <td><b><?php echo $this->name; ?></b></td>
        </TR>  
        <TR>
          <TD class="key"><?php echo \Joomla\CMS\Language\Text::_('CTYPE'); ?>:</TD>
          <td><b><?php echo $this->type; ?></b></td>
        </TR>
        <TR>
          <TD class="key"><?php echo \Joomla\CMS\Language\Text::_('CVIEW'); ?>:</TD>
          <td>
          <?php $this->addTemplatePath( $this->tmpl_path ); ?>
          <?php $this->setLayout( 'rating_'.$this->name ); ?>
          <?php $this->assignRef( 'prod_id',          $ident = '0');?>
          <?php $this->assignRef( 'rating_ident',     $ident = 'test');?>
          <?php $this->assignRef( 'rating_current',   $current = '60');?>
          <?php $this->assignRef( 'rating_active',    $rating_active = 'true');?>
          <?php $this->assignRef( 'callbackfunction', $callbackfunction = 'callBackFunction');?>
          <?php echo $this->loadTemplate( ); ?>
          </td>
        </TR>
      </table>
    </fieldset>   
  </div>
  
  <input type="hidden" name="option"       value="<?php echo $option; ?>" />
  <input type="hidden" name="controller"   value="subtmpls" />
  <input type="hidden" name="task"         value="" />
  <input type="hidden" name="name"         value="<?php echo $this->name; ?>" />
  <input type="hidden" name="type"         value="<?php echo $this->type; ?>" />
  <input type="hidden" name="hidemainmenu" value="0" />
</form>
