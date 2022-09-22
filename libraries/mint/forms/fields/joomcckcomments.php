<?php
defined('_JEXEC') or die();
jimport('joomla.html.html');
jimport('joomla.form.formfield');

JFormHelper::loadFieldClass('melist');

class JFormFieldJoomcckcomments extends JFormMEFieldList
{
	public $type = 'Joomcckcomments';

	protected function getOptions()
	{
		$path = JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_joomcck'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'php'. DIRECTORY_SEPARATOR .'comments';

		$folders = JFolder::folders($path);

		$list[] = JHtml::_('select.option', '', JText::_('CNOCOMMENTS'));
		foreach ($folders as $folder) {
			$provider = $folder;

			$xmlfile = $path. DIRECTORY_SEPARATOR .$folder. DIRECTORY_SEPARATOR .$folder.'.xml';
			if(JFile::exists($xmlfile))
			{
				$xml = simplexml_load_file($xmlfile);
				$provider = $xml->name;
			}

			$list[] = JHtml::_('select.option', $folder, $provider);
		}

		return $list;
	}
}
?>