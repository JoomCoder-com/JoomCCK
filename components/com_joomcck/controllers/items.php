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
            JRoute::_('index.php?option=' . $this->option . '&view=items&layout=change_core&' . $this->_getCIDs(), false)
        );
    }
    public function change_field()
    {
        $this->setRedirect(
            JRoute::_('index.php?option=' . $this->option . '&view=items&layout=change_field&' . $this->_getCIDs(), false)
        );
    }
    public function change_category()
    {
        $this->setRedirect(
            JRoute::_('index.php?option=' . $this->option . '&view=items&layout=change_category&' . $this->_getCIDs(), false)
        );
    }
    public function cancel()
    {
        $this->setRedirect(
            JRoute::_('index.php?option=' . $this->option . '&view=items', false)
        );
    }
    public function applychco()
    {
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		
        $form = $this->input->get('jform', [], 'array');
        $cid = $this->input->get('cid', [], 'array');

        $int = ['published', 'access', 'meta_index', 'langs', 'featured', 'user_id'];
        $str = ['meta_descr', 'meta_key', 'ftime', 'ctime', 'extime', 'mtime'];

		$sql = [];
		
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
			$sql[] = sprintf('`%s` = %d', $value, $form[$value]);
		}
		foreach ($str as $value) {
			if(empty($form[$value])) {
				continue;
			}
			$sql[] = sprintf("`%s` = '%s'", $value, $db->escape($form[$value]));
		}

		if(!empty($sql)) {
			$query = sprintf('UPDATE `#__js_res_record` SET %s WHERE id IN(%s)',implode(', ', $sql), implode(',', $cid));
			$db->setQuery($query);
			$db->execute();
			$app->enqueueMessage(JText::printf('COM_JOOMCCK_N_ITEMS_UPDATED', count($cid)));
		} else {
			$app->enqueueMessage(Mint::_('CNOCHANGE'));
		}

        $this->setRedirect(
            JRoute::_('index.php?option=' . $this->option . '&view=items', false)
        );
    }

    public function delete()
    {
        $ids = $this->input->get('cid', [], '', 'array');
        \Joomla\Utilities\ArrayHelper::toInteger($ids);

        if (empty($ids)) {
            $this->redirect(Url::view('items', false));
        }

        $app = JFactory::getApplication();
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
        $app  = JFactory::getApplication();
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
