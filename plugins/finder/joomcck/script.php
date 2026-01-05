<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Finder.Joomcck
 *
 * @copyright   (C) 2024 JoomCoder
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;

/**
 * Installation class to cleanup legacy files after update.
 *
 * @since  5.0.0
 */
class plgFinderJoomcckInstallerScript
{
    /**
     * Files to remove during update (legacy files from previous versions)
     *
     * @var    array
     * @since  5.0.0
     */
    protected $deleteFiles = [
        '/plugins/finder/joomcck/joomcck.php',
    ];

    /**
     * Folders to remove during update (legacy folders from previous versions)
     *
     * @var    array
     * @since  5.0.0
     */
    protected $deleteFolders = [];

    /**
     * Function called before extension installation/update/removal.
     *
     * @param   string            $type    The type of change (install, update, uninstall, discover_install)
     * @param   InstallerAdapter  $parent  The parent installer object
     *
     * @return  boolean  True on success
     *
     * @since   5.0.0
     */
    public function preflight(string $type, InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * Function called after extension installation/update/removal.
     *
     * @param   string            $type    The type of change (install, update, uninstall, discover_install)
     * @param   InstallerAdapter  $parent  The parent installer object
     *
     * @return  boolean  True on success
     *
     * @since   5.0.0
     */
    public function postflight(string $type, InstallerAdapter $parent): bool
    {
        // Only run cleanup on update
        if ($type === 'update') {
            $this->removeObsoleteFiles();
            $this->removeObsoleteFolders();
        }

        return true;
    }

    /**
     * Remove obsolete files from previous versions.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function removeObsoleteFiles(): void
    {
        foreach ($this->deleteFiles as $file) {
            $path = JPATH_ROOT . $file;

            if (is_file($path)) {
                File::delete($path);
            }
        }
    }

    /**
     * Remove obsolete folders from previous versions.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function removeObsoleteFolders(): void
    {
        foreach ($this->deleteFolders as $folder) {
            $path = JPATH_ROOT . $folder;

            if (is_dir($path)) {
                Folder::delete($path);
            }
        }
    }
}
