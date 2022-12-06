<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Joomcck\Assets\Webassets;

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

Class Webassets{


	/**
	 * The search tools form
	 *
	 * @var   \Joomla\CMS\WebAsset\WebAssetManager     *
	 */
	static $wa;

	public static function init(){

		static::$wa = \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
		static::$wa->getRegistry()->addExtensionRegistryFile('com_joomcck');
		$wr =  static::$wa->getRegistry();
		$wr->addRegistryFile(JPATH_ROOT.'/media/com_joomcck/joomla.asset.json');

	}

}