<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$section = ItemsStore::getSection($row->extension->_item->section_id);
if( $row->parent_id != 0 ) { ?>
	<span>
		<?php
			if( $system->config->get( 'enable_threaded' ) )
			{
				$name = '';
				$parent = Komento::getComment( $row->parent_id, true );
				echo JText::sprintf( 'COM_KOMENTO_COMMENT_IN_REPLY_TO_NAME', '', CCommunityHelper::getName($parent->created_by, $section));
			}
			else
			{
				// non threaded no need to show name, because will have parent comment as a popup when hover over comment id
				echo JText::sprintf( 'COM_KOMENTO_COMMENT_IN_REPLY_TO', $row->parentlink, $row->parent_id );
			}


			$parent = '';

			if( $system->konfig->get( 'parent_preload' ) ) {
				$parent = Komento::getComment( $row->parent_id );
			}

			$parentTheme = Komento::getTheme();
			$parentTheme->set( 'parent', $parent );
			echo $parentTheme->fetch( 'comment/item/parent.php' );
			?>
	</span>
<?php }
