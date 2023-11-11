    <?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$app = \Joomla\CMS\Factory::getApplication();
$document = \Joomla\CMS\Factory::getDocument();
$document->addStyleDeclaration('
.catselect{
	width:' . $params->get('select_width') . 'px;
}
');
$parents = array();
if($app->input->getInt('cat_id', false))
{
	$parents = modJoomcckCategoriesHelper::getParentsList($app->input->getInt('cat_id'));
}
$levels = explode("\r\n", $params->get('levels'));
ArrayHelper::clean_r($levels);
$max_level = count($levels);
?>

<script type="text/javascript">
	(function($) {
		var max_level = <?php echo $max_level; ?>;
		var categories_parents = [<?php echo implode(',', $parents)?>];

		window.redirectToCategory = function(id) {
			if(!id) return;
			window.location = '<?php echo \Joomla\CMS\Uri\Uri::base(); ?>index.php?option=com_joomcck&view=records&section_id=<?php echo $section->id; ?>&cat_id='+id;
		}

		window.modJsc_getChilds_select = function(el, selectid, i, populate) {
			if(!el.value) {
				return;
			}
			$.ajax({
				url:      '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=ajax.category_childs&tmpl=component', FALSE);?>',
				dataType: 'json',
				type:     'POST',
				data:     {cat_id: el.value, section_id: <?php echo $section->id; ?>}
			}).done(function(json) {
					if(!json) {
						return;
					}
					if(!json.success) {
						alert(json.error);
						return;
					}

					select = $("#" + selectid);
					select.empty();
					selected = false;
					if(json.result.length) {

						var opt = $(document.createElement("option")).attr({
							value: ''
						});
						opt.text('<?php echo \Joomla\CMS\Language\Text::_('CSELECTMODULE');?>');
						opt.appendTo(select);
						$.each(json.result, function(index, item) {
							if(<?php echo $params->get('cat_empty', 1);?> == 1 || (<?php echo $params->get('cat_empty', 1);?> == 0 && (item.num_current > 0 || item.num_all > 0 )))
							{
								if(<?php echo ($params->get('cat_nums', 0) ? 1 : 0);?> != 0)
								{
									item.title += ' (' + ( ('<?php echo $params->get('cat_nums');?>' == 'current') ? item.num_current : item.num_all) + ')';
								}


								var opt = $(document.createElement("option")).attr({
									value: parseInt(item.id)
								});
								opt.text(item.title);
								opt.appendTo(select);
								if($.inArray(parseInt(item.id), categories_parents) > -1)
								{
									selected = item.id;
								}
							}
						});

						console.log(selected);

						if(selected)
						{
							select.val(selected);
						}
					}
					else {
						var opt = $(document.createElement("option")).attr({
							value: ''
						});
						opt.text('<?php echo \Joomla\CMS\Language\Text::_('CEMPTYMODULE');?>');
						opt.appendTo(select);
						if(!populate)
							window.redirectToCategory(el.value);
					}
				});
		}
	}(jQuery));
</script>

<div class="js_cc<?php echo $params->get('moduleclass_sfx') ?>">

	<?php if($headerText) : ?>
		<div class="js_cc"><?php echo $headerText ?></div>
	<?php endif; ?>

	<?php if($params->get('show_section', 1)) : ?>
		<div <?php echo $params->get('section_class') ? 'class="' . $params->get('section_class') . '"' : ''; ?>>
			<?php if($params->get('show_section', 1) == 2) : ?>
				<a href="<?php echo \Joomla\CMS\Router\Route::_($section->link); ?>"><?php echo $section->name; ?></a>
			<?php
			else :
				echo $section->name;
			endif;?>
		</div>
	<?php endif; ?>


	<?php if(count($categories)): ?>
		<div class="contentpane">
			<?php
			$i = 1;

			foreach($levels as $level):

				if($i == $max_level)
				{
					$function = 'window.redirectToCategory(this.value);';
				}
				else
				{
					$function = 'window.modJsc_getChilds_select(this, \'category_select' . ($i + 1) . '\', ' . $i . ', false);';
				}

				$options   = array();
				$selected = null;
				$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CSELECTMODULE'));
				if($i == 1):

					foreach($categories as $cat)
					{
						if(!$params->get('cat_empty', 1) && !$cat->records_num)
						{
							continue;
						}
						if($params->get('cat_nums', 0))
						{
							$cat->title .= ' (' . ($params->get('cat_nums', 'current') == 'current' ? modJoomcckCategoriesHelper::getRecordsNum($section, $cat->id) : $cat->records_num) . ')';
						}
						$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $cat->id, $cat->title);
						if(in_array($cat->id, $parents))
						{
							$selected = $cat->id;
							$document->addScriptDeclaration("
								jQuery(document).ready(function(){
									window.modJsc_getChilds_select(jQuery('#category_select1').get(0), 'category_select2', 1, true);
								});
							");
						}
					}
					?>
					<div>
						<?php echo \Joomla\CMS\Language\Text::_($level); ?><br/>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, 'category_select1', 'class="catselect" onchange="' . $function . '"', 'value', 'text', $selected); ?>
					</div>

				<?php else: ?>
					<div>
						<?php echo \Joomla\CMS\Language\Text::_($level); ?><br/>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, 'category_select' . $i, ' class="catselect" onchange="' . $function . '"'); ?>
					</div>
				<?php endif; ?>
				<?php $i++; ?>
			<?php endforeach; ?>

		</div>
	<?php endif; ?>


	<?php if($footerText) : ?>
		<div class="js_cc<?php echo $params->get('moduleclass_sfx') ?>"><?php echo $footerText; ?></div>
	<?php endif; ?>

</div>


<div class="clearfix"></div>