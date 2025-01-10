<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if($limit > 0):?>
	<small><?php echo \Joomla\CMS\Language\Text::sprintf('CSELECTLIMIT', $limit)?></small>
	<br />
	<br />
<?php endif;?>
<div id="error-msg"></div>
<div id="categories_list" class="clearfix">
	<?php if (!empty($defaults)): ?>
		<?php	foreach ( $defaults as $item ):?>
            <div class="float-start alert alert-success alert-dismissible fade show" role="alert">
                <strong><?php echo $item->title;?></strong> <?php echo $item->path;?>
                <button type="button" class="btn-close" onclick="CatSelector.remove(<?php echo $item->id;?>)" data-bs-dismiss="alert" aria-label="Close"></button>
                <input type="hidden" name="<?php echo $name?>" value="<?php echo $item->id;?>" />
            </div>
		<?php endforeach;?>
	<?php endif; ?>
</div>
<div class="clearfix"></div>
<div>
	<?php if(!empty($sections)):?>
		<div id="main-selector-list">
			<div class="select-list w-100" id="select_list1">
				<ul id="level1" class="nav nav-list">
					<?php foreach ($sections AS $s):?>
						<li id="category-<?php echo $s->id; ?>-1">
							<a class="btn btn-outline-dark" href="javascript:void(0);" onclick="CatSelector.get_children(1, <?php echo $s->id?>, 1)"><?php echo $s->name?></a>
						</li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	<?php elseif(!empty($categories)):?>
		<div id="main-selector-list">
			<div class="select-list w-100" id="select_list1">
				<ul id="level1" class="nav nav-list">
					<?php foreach ($categories AS $c):?>
						<?php if(in_array($c->id, $ignore)) { continue;} ?>
						<li class="w-50 mb-1 p-1" id="category-<?php echo $c->section_id; ?>-<?php echo $c->id; ?>">
							<a class="btn btn-sm btn-outline-success d-inline-block w-100" href="javascript:void(0);" <?php if($c->children){ echo 'onclick="CatSelector.get_children('.$c->id.', '.$c->section_id.', 1)"';}else{echo ' class="disabled"';}?>>
								<?php if($c->id != \Joomla\CMS\Factory::getApplication()->input->getInt('id') && $c->params->get('submission', 1)):?>
									<img class="float-end" id="s_icon<?php echo $c->id;?>"
										src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/plus-button.png"
										onclick="CatSelector.add(<?php echo $c->id;?>, '<?php echo $c->title;?>', '<?php echo $c->path;?>')" />
								<?php endif;?>
								<?php echo $c->title?>
							</a>
						</li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	<?php else:?>
		<h2><?php echo \Joomla\CMS\Language\Text::_('CNOSECTIONS')?></h2>
	<?php endif;?>

	<div class="clearfix"></div>
	<div id="bro-ba" style="width:200px;" class="progress progress-striped">
		<div class="bar" style="width: 100%;"></div>
	</div>
	<small>
		<?php echo \Joomla\CMS\Language\Text::sprintf('CCLICKTOASIGNCATEGORY',
			\Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root().'media/com_joomcck/icons/16/plus-button.png', \Joomla\CMS\Language\Text::_('CADDCATEGORY'), array('align'=>'absmiddle', 'width' => 12, 'style' => 'float: none')),
			\Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root().'media/com_joomcck/icons/16/cross-button.png', \Joomla\CMS\Language\Text::_('CDELETECATEGORY'), array('align'=>'absmiddle', 'width' => 12, 'style' => 'float: none')));?>
	</small>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
(function($)
{
	$('#bro-ba').slideUp();
	CatSelector =
	{
		s_icons_cat: [<?php if(!empty($default)) echo implode(',', $default); ?>],
		ignores:[<?php if(!empty($ignore)) echo implode(',', $ignore); ?>],

		add: function(id, title, path)
		{
			console.log(this.s_icons_cat.length, <?php echo $limit;?>);
			<?php if($limit > 0):?>
				if(this.s_icons_cat.length >= <?php echo $limit;?>)
				{
					var html = '<div class="float-start alert alert-warning alert-dismissible fade show" role="alert">' +
                        '<button type="button" data-bs-dismiss="alert" aria-label="Close" class="btn-close">' +
                        '</button>
                        <?php echo htmlentities(\Joomla\CMS\Language\Text::_('CCATEGORYREACHMAXLIMIT'), ENT_QUOTES, 'UTF-8')?>
                        </div>';
					$('#error-msg').html(html);
					return;
				}
			<?php endif;?>
			this.s_icons_cat.push(id);

			var out = '<strong>' + title + '</strong> ' + path +
                '<button type="button"  data-bs-dismiss="alert" aria-label="Close" class="btn-close" onclick="CatSelector.remove(' + id +
			')" ></button><input type="hidden" name="<?php echo $name?>" value="' + id + '" />';

			var li = $(document.createElement("div"))
				.attr('class', 'float-start alert alert-success alert-dismissible fade show')
				.html(out);
			$('#categories_list').append(li);
			$('#s_icon' + id).css('display', 'none');
		},

		remove: function(id)
		{
			$('#error-msg').html('');
			this.s_icons_cat = this.s_icons_cat.filter(function(e) { return e !== 'seven' });
			try{
                $('#s_icon' + id).css('display', 'block');
                $('#selectedCat'+id).remove();


            }catch(e){

            }
		},

		icons_hide: function()
		{
			$.each(this.s_icons_cat, function(key, id){
				try
				{
					$('#s_icon' + id).css('display', 'none');
				}
				catch (e){}
			});
		},

		clean: function(level)
		{
			for(i=1; i<10; i++)
			{
				try
				{
					$('#select_list' + (i+level)).remove();
				}
				catch (e)
				{}
			}
		},

		get_children: function (id, section, level)
		{
			this.clean(level);
			$('#bro-ba').slideDown('slow', function(){
				$.ajax({
					url: '<?php
							$url = \Joomla\CMS\Router\Route::_("index.php?option=com_joomcck&task=ajax.category_children&tmpl=component", FALSE);
							if(\Joomla\CMS\Factory::getApplication()->isClient('administrator'))
							{
								$url = str_replace('/administrator', '', $url);
							}

					echo  $url;?>',
					context: $("#main-selector-list"),
					dataType: 'json',
					type: 'POST',
					data:{parent: id, section: section}
				}).done(function(data) {
					$('#bro-ba').slideUp('slow');

					if(!data) return;

					var fl = $(document.createElement('div'))
						.attr('id', 'select_list'+ (level+1))
						.attr('class', 'select-list w-100');
					var ul = $(document.createElement('ul'))
						.attr('id', 'level'+ (level+1))
						.attr('class', 'nav nav-list');

					var elements = 0;

					$.each(data, function(id, obj){

						if($.inArray(parseInt(obj.id), CatSelector.ignores) > -1)
						{
							return true;
						}

						var li = $(document.createElement('li'))
							.attr('id', 'category-'+ section + '-' + obj.id);
						var html = '';
						html += '<a class="btn btn-outline-dark d-flex justify-content-between" href="javascript: void(0);"';
						if(obj.children > 0)
						{
							html += 'onclick="CatSelector.get_children(' + obj.id +', ' + obj.section_id +', '+ (level + 1)+')"';
						}
						else
						{
							html += 'class="disabled"';
						}
						html += '>';

						if(obj.params.submission == "1")
						{
							html += '<img id="s_icon' + obj.id +
								'" src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/plus-button.png" ' +
								'onclick="CatSelector.add(' + obj.id +', \'' + obj.title +'\', \'' + obj.path +'\')" />';
						}
						html += obj.title+'</a>';

						li.html(html);
						ul.append(li);

						elements++;
					});

					if(elements) {
						fl.append(ul);
						this.append(fl);
					}

					CatSelector.icons_hide();
				});

				var list = $('#level'+level).children();
				$.each(list, function(item, object){


					$(object).removeClass('active');
				});

			    $('#category-'+ section + '-' + id).addClass('active');

			});
		}
	}

})(jQuery);

CatSelector.icons_hide();

</script>