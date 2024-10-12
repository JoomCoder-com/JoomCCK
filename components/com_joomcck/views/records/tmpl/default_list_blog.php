<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

if(!class_exists('CarticleHelper'))
{
	class CarticleHelper
	{

		var $k = 0;
        public $exclude;

		public function isnext($obj)
		{
			return (isset($obj->items[$this->k]));
		}
		public function display(&$obj)
		{
			if(empty($obj->items[$this->k]))
			{
				return;
			}
			$params = $obj->tmpl_params['list'];
			$item = $obj->items[$this->k];

			unset($obj->items[$this->k++]);
			?>
			<?php echo Layout::render('core.apps.blog.list.article',['params' => $params,'item' => $item,'obj' => $obj,'exclude' => $this->exclude]) ?>
		<?php
		}
	}
}

$k = 0;
$params = $this->tmpl_params['list'];
$leading = $params->get('tmpl_params.leading', 1);
$cols = $params->get('tmpl_params.blog_cols', 2);
$intro = $params->get('tmpl_params.blog_intro', 6);
$links = $params->get('tmpl_params.blog_links', 5);
$l = 0;
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
$rows = $cols ? ceil($intro / $cols) : 0;
if($rows <= 0) $rows = 0;

$helper = new CarticleHelper();
$helper->k = 0;

$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}

// add image field automatically to exclude list
if($params->get('tmpl_params.field_image', 0)){
	$exclude[] = $this->fields_keys_by_id[$params->get('tmpl_params.field_image', 0)];
}

$helper->exclude = $exclude;

?>

<?php echo Layout::render('core.list.onThisPage',['params' => $params,'items' => $this->items]) ?>

<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}
	.input-field-full {
		margin-left: 0px !important;
	}
</style>

<div class="jcck-blog-articles mb-5">

	<?php if($leading && $helper->isnext($this)):?>
        <div class="items-leading mb-3">
			<?php for($i = 0; $i < $leading; $i++): ?>
                <div class="leading-<?php echo $i;?>">
					<?php echo $helper->display($this);?>
                </div>
			<?php endfor;?>
        </div>
	<?php endif;?>

    <div class="clearfix"></div>

	<?php if($intro && $helper->isnext($this)):?>
		<?php for($r = 0; $r < $rows; $r++):?>
            <div class="row">
				<?php for($c = 0; $c < $cols; $c++):?>
                    <div class="col-md-<?php echo round((12 / $cols));?>">
						<?php echo $helper->display($this); ?>
                    </div>
				<?php endfor;?>
            </div>
		<?php endfor;?>
	<?php endif;?>

	<?php if($links && $helper->isnext($this)):?>
        <div class="items-more">
            <h3><?php echo Text::_('CMORERECORDS')?></h3>
            <ul class="nav nav-tabs nav-stacked">
				<?php foreach ($this->items AS $item):?>
                    <li class="has-context">
                        <div class="float-end controls">
                            <div class="btn-group" style="display: none;">
								<?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
								<?php echo HTMLFormatHelper::follow($item, $this->section);?>
								<?php echo HTMLFormatHelper::repost($item, $this->section);?>
								<?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
								<?php if($item->controls):?>
                                    <a href="#" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm">
										<?php echo HTMLFormatHelper::icon('gear.png');  ?>
                                    </a>
                                    <ul class="dropdown-menu">
										<?php echo list_controls($item->controls);?>
                                    </ul>
								<?php endif;?>
                            </div>
                        </div>

                        <a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo \Joomla\CMS\Router\Route::_($item->url);?>">
							<?php echo $item->title;?>
							<?php echo CEventsHelper::showNum('record', $item->id);?>
                        </a>

                    </li>
				<?php endforeach;?>
            </ul>
        </div>
	<?php endif;?>

</div>


