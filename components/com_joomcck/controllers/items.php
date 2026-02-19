<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') || die();

require_once __DIR__ . '/records.php';
class JoomcckControllerItems extends JoomcckControllerRecords
{
    public function change_core()
    {
        $this->setRedirect(
            \Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=items&layout=change_core&' . $this->_getCIDs(), false)
        );
    }
    public function change_field()
    {
        $this->setRedirect(
            \Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=items&layout=change_field&' . $this->_getCIDs(), false)
        );
    }
    public function change_category()
    {
        $this->setRedirect(
            \Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=items&layout=change_category&' . $this->_getCIDs(), false)
        );
    }
    public function cancel()
    {
        $this->setRedirect(
            \Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=items', false)
        );
    }
    public function applychco()
    {
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$db = \Joomla\CMS\Factory::getDbo();
		$app = \Joomla\CMS\Factory::getApplication();

        $form = $this->input->get('jform', [], 'array');
        $cid = $this->input->get('cid', [], 'array');
        $cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (empty($cid)) {
            $app->enqueueMessage(Mint::_('CNOCHANGE'));
            $this->setRedirect(
                \Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=items', false)
            );
            return;
        }

        $int = ['published', 'access', 'meta_index', 'langs', 'featured', 'user_id'];
        $str = ['meta_descr', 'meta_key', 'ftime', 'ctime', 'extime', 'mtime'];

		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__js_res_record'));
		$hasUpdates = false;

		foreach ($int as $value) {
			if($value == 'user_id' && (int)$form[$value] == 0) {
				continue;
			}
			if((int)$form[$value] < 0) {
				continue;
			}
			if($value == 'featured') {
				$form['ftime'] = NULL;
			}
			$query->set($db->quoteName($value) . ' = ' . (int)$form[$value]);
			$hasUpdates = true;
		}
		foreach ($str as $value) {
			if(empty($form[$value])) {
				continue;
			}
			$query->set($db->quoteName($value) . ' = ' . $db->quote($form[$value]));
			$hasUpdates = true;
		}

		if($hasUpdates) {
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');
			$db->setQuery($query);
			$db->execute();
			$app->enqueueMessage(\Joomla\CMS\Language\Text::printf('COM_JOOMCCK_N_ITEMS_UPDATED', count($cid)));
		} else {
			$app->enqueueMessage(Mint::_('CNOCHANGE'));
		}

        $this->setRedirect(
            \Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=items', false)
        );
    }

    public function delete()
    {
        $ids = $this->input->get('cid', [], '', 'array');
	    $ids = \Joomla\Utilities\ArrayHelper::toInteger($ids);

        if (empty($ids)) {
            $this->redirect(Url::view('items', false));
        }

        $app = \Joomla\CMS\Factory::getApplication();
        foreach ($ids as $id) {
            $app->input->set('id', $id);
            parent::delete();
        }

        $this->setRedirect(Url::view('items', false));
    }

    public function unpublish()
    {
        $this->publish();
    }

    public function publish()
    {
        $ids  = $this->input->get('cid', [], '', 'array');
        $app  = \Joomla\CMS\Factory::getApplication();
        $task = $this->getTask();
        foreach ($ids as $id) {
            $app->input->set('id', $id);
            $task == 'publish' ? $this->spub() : $this->sunpub();
        }

        $this->setRedirect(Url::view('items', false));
    }
    private function _getCIDs()
    {
        $query = [];
        $cid   = $this->input->get('cid', []);
        foreach ($cid as $c) {
            $query[] = 'cid[]=' . $c;
        }

        return implode('&', $query);
    }
}
