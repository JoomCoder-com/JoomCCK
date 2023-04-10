<?php
/**
 * @package     Joomla.site
 * @subpackage  Joomrecipe
 *
 * @copyright   Copyright (C) 2013 - 2018 joomcoder. All rights reserved.
 * @license     GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author      joomcoder
 */

defined('_JEXEC') or die;


/**
 * @package        Joomrecipe
 * @copyright    2013-2017 joomcoder, https://www.joomcoder.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

\defined('_JEXEC') or die;

JLoader::registerNamespace("Joomla\\Component\\Joomcck\\Site\\Service", JPATH_SITE . '/components/com_joomcck/src/Services');

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Component\Joomcck\Site\Service\RouterRules;
use Joomla\Database\DatabaseInterface;


/**
 * Routing class of com_joomfaqs
 *
 * @since  3.3
 */
class JoomcckRouter extends RouterView
{
	/**
	 * Flag to remove IDs
	 *
	 * @var    boolean
	 */
	protected $noIDs = false;

	/**
	 * The category factory
	 *
	 * @var CategoryFactoryInterface
	 *
	 * @since  4.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var  array
	 *
	 * @since  4.0.0
	 */
	private $categoryCache = [];

	/**
	 * The db
	 *
	 * @var DatabaseInterface
	 *
	 * @since  4.0.0
	 */
	private $db;

	/**
	 * Content Component router constructor
	 *
	 * @param SiteApplication $app The application object
	 * @param AbstractMenu $menu The menu object to work with
	 * @param CategoryFactoryInterface $categoryFactory The category object
	 * @param DatabaseInterface $db The database object
	 */
	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		parent::__construct($app, $menu);
		$this->attachRule(new RouterRules($this));
	}

}
