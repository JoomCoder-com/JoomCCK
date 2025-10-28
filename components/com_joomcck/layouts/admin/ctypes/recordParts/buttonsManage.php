<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Html\Helpers\Dropdown;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();


extract($displayData);


Dropdown::init();

// Create dropdown items
Dropdown::edit($item->id, 'ctype.');
Dropdown::addCustomItem('<i class="fas fa-trash text-danger"></i> '. Text::_('C_TOOLBAR_DELETE'), 'javascript:void(0)', 'onclick="if(!confirm(\'' . Text::_('C_TOOLBAR_CONFIRMDELET') . '\')){return;}Joomla.listItemTask(\'cb' . $i . '\',\'types.delete\')"');
if ($item->published) :
    Dropdown::unpublish('cb' . $i, 'ctypes.');
else :
    Dropdown::publish('cb' . $i, 'ctypes.');
endif;

if ($item->checked_out) :
    Dropdown::divider();
    Dropdown::checkin('cb' . $i, 'ctypes.');
endif;


Dropdown::divider();
    Dropdown::addCustomItem(Text::_('C_MANAGE_FIELDS') . ' <span class="badge ' . ($item->fieldnum ? ' bg-success' : ' bg-light text-dark border') . '">' . $item->fieldnum . '</span>', \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=tfields&filter_type=' . $item->id));


?>

<?php echo Dropdown::render(); ?>
