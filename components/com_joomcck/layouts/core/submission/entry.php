<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

// map form display types
$formTypes = ['plain','tabs','accordions','fieldsets','verticalTabs','cards'];

// load asset manager
$wa = Webassets::$wa;

// load submission css file
$wa->useStyle('com_joomcck.submission');

// load bootstrap tooltip
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

// load custom css
if(!empty($current->tmpl_params->get('tmpl_params.css','')))
	$wa->addInlineStyle($current->tmpl_params->get('tmpl_params.css',''));

// load custom js
if(!empty($current->tmpl_params->get('tmpl_params.js','')))
	$wa->addInlineScript($current->tmpl_params->get('tmpl_params.js',''));


?>

<?php echo Layout::render('core.submission.formTypes.'.$formTypes[$current->tmpl_params->get('tmpl_params.form_grouping_type', 0)],['current' => $current]) ?>