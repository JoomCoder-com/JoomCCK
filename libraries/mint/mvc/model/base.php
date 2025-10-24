<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Cache\Exception\CacheExceptionInterface;
use Joomla\CMS\Event\Model\AfterCleanCacheEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * Base class for a Joomla Model
 *
 * Acts as a Factory class for application specific objects and
 * provides many supporting API functions.
 *
 * @package     Joomla.Legacy
 * @subpackage  Model
 * @since       12.2
 */
abstract class MModelBase extends BaseDatabaseModel
{

}
