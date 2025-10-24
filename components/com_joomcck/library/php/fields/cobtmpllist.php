<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') || die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.path');

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of files
 *
 * @since  1.7.0
 */
class JFormFieldCobTmplList extends \Joomla\CMS\Form\Field\ListField
{
    /**
     * The form field type.
     *
     * @var string
     * @since  1.7.0
     */
    protected $type = 'CobTmplList';

    /**
     * The filter.
     *
     * @var string
     * @since  3.2
     */
    protected $filter;

    /**
     * The exclude.
     *
     * @var string
     * @since  3.2
     */
    protected $exclude;

    /**
     * The hideNone.
     *
     * @var boolean
     * @since  3.2
     */
    protected $hideNone = false;

    /**
     * The hideDefault.
     *
     * @var boolean
     * @since  3.2
     */
    protected $hideDefault = false;

    /**
     * The stripExt.
     *
     * @var boolean
     * @since  3.2
     */
    protected $stripExt = false;

    /**
     * The directory.
     *
     * @var string
     * @since  3.2
     */
    protected $directory;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @since   3.2
     *
     * @param  string $name The property name for which to get the value.
     * @return mixed  The property value or null.
     */

    protected function getInput()
    {
        $html = [];
        $attr = '';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
        $attr .= $this->multiple ? ' multiple' : '';
        $attr .= $this->required ? ' required aria-required="true"' : '';
        $attr .= $this->autofocus ? ' autofocus' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1' || (string) $this->disabled == 'true') {
            $attr .= ' disabled="disabled"';
        }

        // Initialize JavaScript field attributes.
        $attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();

        // Create a read-only list (no name) with hidden input(s) to store the value(s).
        if ((string) $this->readonly == '1' || (string) $this->readonly == 'true') {
            $html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);

            // E.g. form field type tag sends $this->value as array
            if ($this->multiple && is_array($this->value)) {
                if (!count($this->value)) {
                    $this->value[] = '';
                }

                foreach ($this->value as $value) {
                    $html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
                }
            } else {
                $html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
            }
        } else
        // Create a regular list passing the arguments in an array.
        {
            $listoptions                   = [];
            $listoptions['option.key']     = 'value';
            $listoptions['option.text']    = 'text';
            $listoptions['list.select']    = $this->value;
            $listoptions['id']             = $this->id;
            $listoptions['list.translate'] = false;
            $listoptions['option.attr']    = 'optionattr';
            $listoptions['list.attr']      = trim($attr);

            $html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, $this->name, $listoptions);
		}
		
		//JHtmlSelect::genericlist()
		$path = $this->directory;

        if (!is_dir($path)) {
            $path = JPATH_ROOT . '/' . $path;
        }

		$path =\Joomla\Filesystem\Path::clean($path);
		$xml = $path.DIRECTORY_SEPARATOR.str_replace('.php', '.xml', $this->value ?: $this->default);


		if(is_file($xml)) {
			$icon = HTMLFormatHelper::icon('gear.png');
			$parts = explode('/', $this->directory);


			$title = \Joomla\CMS\Language\Text::sprintf('COB_FIEL_PARAMS', ucfirst($parts[5]));


			HTMLHelper::_('bootstrap.modal');

			$form = MFormHelper::getFieldParams($xml, \Joomla\CMS\Factory::getApplication()->input->get('id'), $this->value ?: $this->default);

			$html[] = <<<EOT
			<button data-bs-target="#config_$this->id" type="button" role="button" class="btn btn-sm btn-light border" data-bs-toggle="modal">$icon</button>
			
			<div id="config_$this->id" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="mtitle_$this->id" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">							  
							  <h3 class="modal-title fs-5" id="mtitle_$this->id">$title</h3>
							  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							
											<div class="modal-body" style="overflow-Y: scroll;">
							  $form
							</div>
							
							<div class="modal-footer">
							  <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							</div>
					</div>
				
				</div>
			
		  </div>
EOT;
		}

        return '<div style="white-space: nowrap; display: inline-block;">'.str_replace('<select ', '<select style="width: auto;" ', implode($html)).'</div>';
    }

    public function __get($name)
    {
        switch ($name) {
            case 'filter':
            case 'exclude':
            case 'hideNone':
            case 'hideDefault':
            case 'stripExt':
            case 'directory':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @since   3.2
     *
     * @param  string $name  The property name for which to set the value.
     * @param  mixed  $value The value of the property.
     * @return void
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'filter':
            case 'directory':
            case 'exclude':
                $this->$name = (string) $value;
                break;

            case 'hideNone':
            case 'hideDefault':
            case 'stripExt':
                $value       = (string) $value;
                $this->$name = ($value === 'true' || $value === $name || $value === '1');
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a \Joomla\CMS\Form\Form object to the field.
     *
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     * @see     JFormField::setup()
     * @since   3.2
     *
     * @param  SimpleXMLElement $element The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param  mixed            $value   The form field value to validate.
     * @param  string           $group   The field name group control value. This acts as an array container for the field.
     * @return boolean          True on success.
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return) {
            $this->filter  = (string) $this->element['filter'];
            $this->exclude = (string) $this->element['exclude'];

            $hideNone       = (string) $this->element['hide_none'];
            $this->hideNone = ($hideNone == 'true' || $hideNone == 'hideNone' || $hideNone == '1');

            $hideDefault       = (string) $this->element['hide_default'];
            $this->hideDefault = ($hideDefault == 'true' || $hideDefault == 'hideDefault' || $hideDefault == '1');

            $stripExt       = (string) $this->element['stripext'];
            $this->stripExt = ($stripExt == 'true' || $stripExt == 'stripExt' || $stripExt == '1');

            // Get the path in which to search for file options.
            $this->directory = (string) $this->element['directory'];
        }

        return $return;
    }

    /**
     * Method to get the list of files for the field options.
     * Specify the target directory with a directory attribute
     * Attributes allow an exclude mask and stripping of extensions from file name.
     * Default attribute may optionally be set to null (no file) or -1 (use a default).
     *
     * @since   1.7.0
     *
     * @return array The field option objects.
     */
    protected function getOptions()
    {
        $options = [];

        $path = $this->directory;

        if (!is_dir($path)) {
            $path = JPATH_ROOT . '/' . $path;
        }

        $path =\Joomla\Filesystem\Path::clean($path);

        // Prepend some default options based on field attributes.
        if (!$this->hideNone) {
            $options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '-1', \Joomla\CMS\Language\Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        if (!$this->hideDefault) {
            $options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        // Get a list of files in the search path with the given filter.
        $files = \Joomla\Filesystem\Folder::files($path, $this->filter);

        // Build the options list from the list of files.
        if (is_array($files)) {
            foreach ($files as $file) {
                // Check to see if the file is in the exclude mask.
                if ($this->exclude) {
                    if (preg_match(chr(1) . $this->exclude . chr(1), $file)) {
                        continue;
                    }
                }

                // If the extension is to be stripped, do it.
                if ($this->stripExt) {
                    $file = \Joomla\Filesystem\File::stripExt($file);
                }

                $options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $file, $file);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
