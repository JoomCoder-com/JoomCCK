<?php
defined('_JEXEC') or die();
jimport('joomla.html.html');
jimport('joomla.form.formfield');

\Joomla\CMS\Form\FormHelper::loadFieldClass('melist');

class JFormFieldCckcomments extends JFormMEFieldList
{
	public $type = 'Cckcomments';

	protected function getOptions()
	{
		$path = JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_joomcck'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'php'. DIRECTORY_SEPARATOR .'comments';

		$folders = \Joomla\Filesystem\Folder::folders($path);

		$list[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CNOCOMMENTS'));
		foreach ($folders as $folder) {
			$provider = $folder;

			$xmlfile = $path. DIRECTORY_SEPARATOR .$folder. DIRECTORY_SEPARATOR .$folder.'.xml';
			if(is_file($xmlfile))
			{
				$xml = simplexml_load_file($xmlfile);
				$provider = $xml->name;
			}

			$list[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $folder, $provider);
		}

		return $list;
	}
}
?>