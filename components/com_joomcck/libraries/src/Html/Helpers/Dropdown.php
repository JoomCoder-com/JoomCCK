<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Joomcck\Html\Helpers;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

/**
 * Dropdown helper class - fully overridden for Joomla 5.4+/6 compatibility
 * Uses vanilla JavaScript and Bootstrap 5 click-based dropdowns
 */
class Dropdown
{
    /**
     * @var array Track loaded state
     */
    protected static $loaded = [];

    /**
     * @var string HTML markup for the dropdown list
     */
    protected static $dropDownList = null;

    /**
     * Initialize dropdown - load Bootstrap dropdown and vanilla JS contextAction
     *
     * @return void
     */
    public static function init()
    {
        if (isset(static::$loaded[__METHOD__])) {
            return;
        }

        // Load Bootstrap 5 dropdown
        HTMLHelper::_('bootstrap.dropdown', '.dropdown-toggle');

        // Add vanilla JS contextAction function for publish/unpublish/checkin actions
        Factory::getDocument()->addScriptDeclaration("
            window.contextAction = function(cbId, task) {
                document.querySelectorAll('input[name=\"cid[]\"]').forEach(function(el) {
                    el.checked = false;
                });
                var cb = document.getElementById(cbId);
                if (cb) {
                    cb.checked = true;
                }
                Joomla.submitbutton(task);
            };
        ");

        static::$loaded[__METHOD__] = true;
    }

    /**
     * Start a new dropdown menu
     *
     * @return void
     */
    public static function start()
    {
        if (isset(static::$loaded[__METHOD__]) && static::$loaded[__METHOD__]) {
            return;
        }

        static::$dropDownList = '<div class="btn-group ms-2">
            <a href="#" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-secondary btn-sm">
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">';

        static::$loaded[__METHOD__] = true;
    }

    /**
     * Render the dropdown menu
     *
     * @return string HTML markup
     */
    public static function render()
    {
        $html = static::$dropDownList . '</ul></div>';

        static::$dropDownList = null;
        static::$loaded[__CLASS__ . '::start'] = false;

        return $html;
    }

    /**
     * Add edit item to dropdown
     *
     * @param integer $id Record ID
     * @param string $prefix Task prefix
     * @param string $customLink Custom link URL
     *
     * @return void
     */
    public static function edit($id, $prefix = '', $customLink = '')
    {
        static::start();

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
     * Add publish item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function publish($checkboxId, $prefix = '')
    {
        $task = $prefix . 'publish';
        static::addCustomItem(Text::_('JTOOLBAR_PUBLISH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add unpublish item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function unpublish($checkboxId, $prefix = '')
    {
        $task = $prefix . 'unpublish';
        static::addCustomItem(Text::_('JTOOLBAR_UNPUBLISH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add featured item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function featured($checkboxId, $prefix = '')
    {
        $task = $prefix . 'featured';
        static::addCustomItem(Text::_('JFEATURED'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add unfeatured item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function unfeatured($checkboxId, $prefix = '')
    {
        $task = $prefix . 'unfeatured';
        static::addCustomItem(Text::_('JUNFEATURED'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add archive item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function archive($checkboxId, $prefix = '')
    {
        $task = $prefix . 'archive';
        static::addCustomItem(Text::_('JTOOLBAR_ARCHIVE'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add unarchive item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function unarchive($checkboxId, $prefix = '')
    {
        $task = $prefix . 'unpublish';
        static::addCustomItem(Text::_('JTOOLBAR_UNARCHIVE'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add trash item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function trash($checkboxId, $prefix = '')
    {
        $task = $prefix . 'trash';
        static::addCustomItem(Text::_('JTOOLBAR_TRASH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add untrash item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function untrash($checkboxId, $prefix = '')
    {
        $task = $prefix . 'publish';
        static::addCustomItem(Text::_('JTOOLBAR_UNTRASH'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add checkin item to dropdown
     *
     * @param string $checkboxId Checkbox ID
     * @param string $prefix Task prefix
     *
     * @return void
     */
    public static function checkin($checkboxId, $prefix = '')
    {
        $task = $prefix . 'checkin';
        static::addCustomItem(Text::_('JTOOLBAR_CHECKIN'), 'javascript:void(0)', 'onclick="contextAction(\'' . $checkboxId . '\', \'' . $task . '\')"');
    }

    /**
     * Add divider to dropdown (Bootstrap 5 compatible)
     *
     * @return void
     */
    public static function divider()
    {
        static::$dropDownList .= '<li><hr class="dropdown-divider"></li>';
    }

    /**
     * Add custom item to dropdown
     *
     * @param string $label Item label
     * @param string $link Item link
     * @param string $linkAttributes Additional link attributes
     * @param string $className Item class name
     * @param boolean $ajaxLoad Use AJAX loading
     * @param string $jsCallBackFunc JS callback function
     *
     * @return void
     */
    public static function addCustomItem(
        $label,
        $link = 'javascript:void(0)',
        $linkAttributes = '',
        $className = '',
        $ajaxLoad = false,
        $jsCallBackFunc = null
    ) {
        static::start();

        // Add dropdown-item class
        $linkAttributes = 'class="dropdown-item" ' . $linkAttributes;

        // Add icons automatically based on label
        switch ($label) {
            case Text::_('JACTION_EDIT'):
                $label = '<i class="fas fa-edit"></i> ' . $label;
                break;
            case Text::_('JTOOLBAR_PUBLISH'):
                $label = '<i class="fas fa-check"></i> ' . $label;
                break;
            case Text::_('JTOOLBAR_UNPUBLISH'):
                $label = '<i class="fas fa-times text-muted"></i> ' . $label;
                break;
            case Text::_('JTOOLBAR_CHECKIN'):
                $label = '<i class="fas fa-unlock"></i> ' . $label;
                break;
        }

        if ($ajaxLoad) {
            $href = ' href="javascript:void(0)" onclick="loadAjax(\'' . $link . '\', \'' . $jsCallBackFunc . '\')"';
        } else {
            $href = ' href="' . $link . '"';
        }

        static::$dropDownList .= '<li class="' . $className . '"><a ' . $linkAttributes . $href . '>' . $label . '</a></li>';
    }
}
