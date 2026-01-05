<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Actionlog.Joomcck
 *
 * @copyright   (C) 2025 JoomCoder
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// Manually load the class until namespace is registered in autoloader
require_once \dirname(__DIR__) . '/src/Extension/Joomcck.php';

use Joomla\Plugin\Actionlog\Joomcck\Extension\Joomcck;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin = new Joomcck(
                    $container->get(\Joomla\Event\DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('actionlog', 'joomcck')
                );

                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get(DatabaseInterface::class));

                return $plugin;
            }
        );
    }
};
