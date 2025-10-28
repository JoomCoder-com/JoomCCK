<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Joomcck\Html\Helpers;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
defined('_JEXEC') or die();

Class Dropdown extends \Joomla\CMS\HTML\Helpers\Dropdown{

	public static function addCustomItem($label, $link = 'javascript:void(0)', $linkAttributes = '', $className = '', $ajaxLoad = false, $jsCallBackFunc = null)
	{

		$linkAttributes = "class='dropdown-item' ".$linkAttributes;

		// add icon automatically
		switch ($label){
			case Text::_('JACTION_EDIT'):
				$label = "<i class='fas fa-edit'></i> ".$label;
				break;
			case Text::_('JTOOLBAR_PUBLISH'):
				$label = "<i class='fas fa-check'></i> ".$label;
				break;
			case Text::_('JTOOLBAR_UNPUBLISH'):
				$label = "<i class='fas fa-times text-muted'></i> ".$label;
				break;
			case Text::_('JTOOLBAR_CHECKIN'):
				$label = "<i class='fas fa-unlock'></i> ".$label;
				break;
		}


        static::start();

        if ($ajaxLoad) {
            $href = ' href = "javascript:void(0)" onclick="loadAjax(\'' . $link . '\', \'' . $jsCallBackFunc . '\')"';
        } else {
            $href = ' href = "' . $link . '" ';
        }

        $dropDownList = static::$dropDownList;
        $dropDownList .= '<li class="' . $className . '"><a ' . $linkAttributes . $href . ' >';
        $dropDownList .= $label;
        $dropDownList .= '</a></li>';
        static::$dropDownList = $dropDownList;
	}


    /**
     * Append an edit item to the current dropdown menu
     *
     * @param integer $id Record ID
     * @param string $prefix Task prefix
     * @param string $customLink The custom link if dont use default Joomla action format
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function edit($id, $prefix = '', $customLink = '')
    {
        // static::start();

        if (!$customLink) {
            $option = Factory::getApplication()->getInput()->getCmd('option');
            $link = 'index.php?option=' . $option;
        } else {
            $link = $customLink;
        }

        $link .= '&task=' . $prefix . 'edit&id=' . $id;
        $link = Route::_($link);

        static::addCustomItem(Text::_('JACTION_EDIT'), $link);
    }

    /**
     * Append a publish item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function publish($checkboxId, $prefix = '')
    {
        $task = $prefix . 'publish';
        static::addCustomItem(Text::_('JTOOLBAR_PUBLISH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append an unpublish item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function unpublish($checkboxId, $prefix = '')
    {
        $task = $prefix . 'unpublish';
        static::addCustomItem(Text::_('JTOOLBAR_UNPUBLISH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append a featured item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function featured($checkboxId, $prefix = '')
    {
        $task = $prefix . 'featured';
        static::addCustomItem(Text::_('JFEATURED'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append an unfeatured item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function unfeatured($checkboxId, $prefix = '')
    {
        $task = $prefix . 'unfeatured';
        static::addCustomItem(Text::_('JUNFEATURED'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append an archive item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function archive($checkboxId, $prefix = '')
    {
        $task = $prefix . 'archive';
        static::addCustomItem(Text::_('JTOOLBAR_ARCHIVE'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append an unarchive item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function unarchive($checkboxId, $prefix = '')
    {
        $task = $prefix . 'unpublish';
        static::addCustomItem(Text::_('JTOOLBAR_UNARCHIVE'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append a trash item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function trash($checkboxId, $prefix = '')
    {
        $task = $prefix . 'trash';
        static::addCustomItem(Text::_('JTOOLBAR_TRASH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append an untrash item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function untrash($checkboxId, $prefix = '')
    {
        $task = $prefix . 'publish';
        static::addCustomItem(Text::_('JTOOLBAR_UNTRASH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Append a checkin item to the current dropdown menu
     *
     * @param string $checkboxId ID of corresponding checkbox of the record
     * @param string $prefix The task prefix
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function checkin($checkboxId, $prefix = '')
    {
        $task = $prefix . 'checkin';
        static::addCustomItem(Text::_('JTOOLBAR_CHECKIN'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Writes a divider between dropdown items
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function divider()
    {
        static::$dropDownList .= '<li class="divider"></li>';
    }
}