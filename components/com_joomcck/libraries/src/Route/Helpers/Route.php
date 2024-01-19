<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Joomcck\Route\Helpers;


use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die();

class Route
{
	/*
     * find menu item id from a set of rules
     */
	private static function findMenuItemId($rules, $language,$returnActive = true,$returnIdOnly = false)
	{
		foreach ($rules as $rule)
		{
			$itemid = self::executeRule($rule, $language);

			if ($itemid > 0)
			{
				return $returnIdOnly ? $itemid : "&Itemid=" . $itemid;
			}
		}

		return $returnActive ? "&Itemid=" . Factory::getApplication()->getMenu()->getActive()->i : false;
	}

	/*
	 * Find menu item id from a given rule
	 */
	private static function executeRule($rule, $language)
	{


		$menus     = Factory::getApplication()->getMenu()->getMenu();
		$hasId     = isset($rule['id']) ? true : false;
		$hasLayout = isset($rule['layout']) ? true : false;

		$rulesLength = count($rule);

		foreach ($menus as $menu)
		{
			// check if menu item is a component
			if ($menu->component != 'com_joomcck')
			{
				continue;
			}

			// check if menu item has view query
			if (!isset($menu->query['view']))
			{
				continue;
			}

			// check language only if multilanguage enabled
			if ($language && $language !== '*' && Multilanguage::isEnabled())
			{
				$checkLanguage = ($menu->language == $language);
			}
			else
			{
				$checkLanguage = true;
			}


			// rule has view and layout and id
			if ($rulesLength == 3 &&
				$hasLayout &&
				$hasId &&
				$menu->query['view'] == $rule['view'] &&
				$checkLanguage &&
				isset($menu->query['layout']) && $menu->query['layout'] == $rule['layout'] &&
				isset($menu->query['id']) && $menu->query['id'] == $rule['id'])

			{
				return $menu->id;
			}

			// rule has view and id
			if ($rulesLength == 2 &&
				$hasId && $menu->query['view'] == $rule['view'] &&
				$checkLanguage &&
				isset($menu->query['id']) && $menu->query['id'] == $rule['id']
			)
			{

				return $menu->id;
			}

			// rule has view and layout
			if ($rulesLength == 2 &&
				$hasLayout &&
				$menu->query['view'] == $rule['view'] &&
				$checkLanguage &&
				isset($menu->query['layout']) && $menu->query['layout'] == $rule['layout'] //&&
			)
			{
				return $menu->id;
			}

			// rule has view only
			if ($rulesLength == 1 &&
				$menu->query['view'] == $rule['view'] &&
				$checkLanguage &&
				!$hasId &&
				!$hasLayout
			)
			{
				return $menu->id;
			}

		}

		return 0;


	}
}