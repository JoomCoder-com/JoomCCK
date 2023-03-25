<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
 defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addScript(JURI::root(TRUE).'/media/system/js/mootree.js');
$document->addStyleSheet(JURI::root(TRUE).'/media/system/css/mootree.css');
?>
<script type="text/javascript"><!--
	var parents<?php echo $module->id;?>= [<?php echo implode(',', $parents);?>];
	var record<?php echo $module->id;?>= <?php echo $rid;?>;
	var i = 0;

	function modJsc_initTree<?php echo $module->id;?>(id, title)
	{
		tree = new MooTreeControl({
			div: 'modcat-'+id,
			mode: 'files',
			grid: true,
			theme: '<?php echo JURI::root(TRUE);?>/media/system/images/mootree.gif',
			loader: {icon: '<?php echo JURI::root(TRUE);?>/media/system/images/mootree_loader.gif', text: '<?php echo JText::_('Loading...')?>'},
			onClick: function (node) {
				if(node.data.url)
				window.location = node.data.url;
			},
			onExpand: function(node, state) {
				if(state && node.id)
				{
					node.clear();
					if(node.id) id = node.id
					modJsc_getChilds<?php echo $module->id;?>(id,node);
					if(parents<?php echo $module->id;?>.contains(node.id)) node.selected = true;
				}
			}
		},{
			text: title,
		<?php if($params->get( 'show_section', 1)) :?>
				open: true,
			<?php if($params->get( 'show_section', 1) == 2) :?>
				data: {url: '<?php echo JURI::root(TRUE);?>/index.php?option=com_joomcck&view=records&section_id=<?php echo $section->id;?>&Itemid=<?php echo $Itemid;?>',
						id: id},
			<?php endif;?>
		<?php endif;?>
		});
		if(parents<?php echo $module->id;?>.contains(id))
		{
			tree.root.select(true);
		}
		tree.disable(); // this stops visual updates while we're building the tree...

		<?php if($params->get( 'show_section', 1)) :
			$i = 0;
			foreach ( $categories as $i => $cat) :
				if(!$cat_id && !$params->get('cat_empty', 1) && !$cat->records_num)
					continue;
				if($params->get('cat_nums', 0))
					$cat->title .= ' ('.$cat->records_num.')';?>

				var node<?php echo $i;?> = tree.insert({
					text:'<?php echo $cat->title;?>',
					id:'<?php echo $cat->id;?>',
					open: false,
					data: {url: '<?php echo JURI::root(TRUE);?>/index.php?option=com_joomcck&view=records&section_id=<?php echo $section->id;?>&cat_id=<?php echo $cat->id;?>&Itemid=<?php echo $Itemid;?>',
						id: id}
				});
				node<?php echo $i;?>.insert({text:'loading', id:'0'});
				if(parents<?php echo $module->id;?>.contains(<?php echo $cat->id;?>))
				{
					node<?php echo $i;?>.select(true);
					node<?php echo $i;?>.open= true;
					node<?php echo $i;?>.clear();
					modJsc_getChilds<?php echo $module->id;?>(<?php echo $cat->id;?>, node<?php echo $i;?>);

				}
			<?php endforeach;
			if($params->get('records') && $section->records) :
				foreach($section->records as $k => $rec):
					if($params->get('records_limit') && $k == $params->get('records_limit') )
					{
						$rec->title = JText::_('CMORERECORDS');
						$rec->id = 0;
						$rec->url = '';
					}
					$i ++; ?>
					var node<?php echo $i;?> = tree.insert({
						text:'<?php echo $rec->title;?>',
						id:'<?php echo $rec->id;?>',
						open: false,
						data: {url: '<?php echo $rec->url;?>', id: id}
					});

				<?php
				endforeach;
			endif;
		else :?>
			tree.insert({text:'loading', id:'0'});
		<?php endif;?>

		tree.enable(); // this turns visual updates on again.
	}

	function modJsc_initTreeRec<?php echo $module->id;?>(id, title, url)
	{
		tree = new MooTreeControl({
			div: 'modrec-'+id,
			mode: 'files',
			grid: true,
			theme: '<?php echo JURI::root(TRUE);?>/media/system/images/mootree.gif',
			loader: {icon: '<?php echo JURI::root(TRUE);?>/media/system/images/mootree_loader.gif', text: '<?php echo JText::_('Loading...')?>'},
			onClick: function (node) {
				if(node.data.url)
				window.location = node.data.url;
			},
			onExpand: function(node, state) {}
		},{
			text: title,
			data: {url: url, id: id}

		});
		if(record<?php echo $module->id;?> == id)
		{
			tree.root.select(true);
		}
		//tree.disable(); // this stops visual updates while we're building the tree...

		//tree.enable(); // this turns visual updates on again.
	}


	function modJsc_getChilds<?php echo $module->id;?>( id, mytree)
	{
		jQuery.ajax({
			url: '<?php echo JURI::root(TRUE);?>/index.php?option=com_joomcck&task=ajax.category_childs&tmpl=component',
			dataType: 'json',
			type: 'POST',
			data:{cat_id: id}
		}).done(function(json) {
			if(!json)
			{
				return;
			}
			if(!json.success)
			{
				alert(json.error);
				return;
			}

			if(json.result.length)
			{
				jQuery.each(json.result, function(index, item){
					item.id = item.id.toInt();
					if( <?php echo $params->get('cat_empty', 1);?> == 1 || (<?php echo $params->get('cat_empty', 1);?> == 0 && item.records_num > 0))
					{
						if(<?php echo ($params->get('cat_nums', 0) ? 1 : 0);?> != 0)
						{
							item.title += ' (' + item.records_num + ')';
						}
						var node = mytree.insert({
							text: item.title,
							id: item.id,
							open: parents<?php echo $module->id;?>.contains(item.id),
							data: {url: item.link + '&Itemid=<?php echo $Itemid;?>', id: 'cat-'+item.id}
						});
						if(item.childs_num > 0)
							node.insert({text:'loading', id:'0'});

						if(parents<?php echo $module->id;?>.contains(item.id))
						{
							node.select(true);
							node.clear();
							modJsc_getChilds<?php echo $module->id;?>(item.id, node);
						}
					}
				});

			}
			<?php if($params->get('records')):?>
				modJsc_getCatRecords<?php echo $module->id;?>( id, mytree);
			<?php endif;?>
		});


	}
	function modJsc_getCatRecords<?php echo $module->id;?>( id, mytree)
	{
		jQuery.ajax({
			url: '<?php echo JURI::root(TRUE);?>/index.php?option=com_joomcck&task=ajax.category_records&tmpl=component',
			dataType: 'json',
			type: 'POST',
			data:{cat_id: id, rec_limit: <?php echo $params->get('records_limit');?>}
		}).done(function(json) {
			if(!json)
			{
				return;
			}
			if(!json.success)
			{
				alert(json.error);
				return;
			}

			if(json.result.length)
			{
				jQuery.each(json.result, function(index, item){
					item.id = item.id.toInt();

					var node = mytree.insert({
						text: item.title,
						id: item.id,
						open: parents<?php echo $module->id;?>.contains(item.id),
						data: {url: item.url + '&Itemid=<?php echo $Itemid;?>', id: 'cat-'+item.id}
					});
				});
			}
			else
			{
				return;
			}
		});

	}
--></script>

<div class="js_cc<?php echo $params->get('moduleclass_sfx') ?>">

<?php if ( $headerText ) : ?>
	<div class="js_cc"><?php echo $headerText ?></div>
<?php endif; ?>

<?php if( $params->get( 'show_section', 1 ) ) : ?>
		<div id="modcat-<?php echo $section->id;?>"></div>
			<script type="text/javascript">
				modJsc_initTree<?php echo $module->id;?>(<?php echo $section->id;?>, '<?php echo $section->title ? $section->title : $section->name;?>' );
			</script>

<?php else : ?>
	<?php if (count($categories)):?>
		<div class="contentpane">
			<?php
				foreach ($categories as $cat) :
					if(!$params->get('cat_empty', 1) && !$cat->records_num) continue;
					if($params->get('cat_nums', 0))
					{
						$cat->title .= ' ('.$cat->records_num.')';
					}
					?>
					<div id="modcat-<?php echo $cat->id;?>"></div>
					<script type="text/javascript">
						modJsc_initTree<?php echo $module->id;?>(<?php echo $cat->id;?>, '<?php echo $cat->title;?>' );
					</script>
			<?php endforeach;?>
		</div>
	<?php endif;?>
	<?php if (count($section->records)):?>
		<div class="contentpane">
			<?php
				foreach ($section->records as $k => $rec) :
					if($params->get('records_limit') && $k == $params->get('records_limit') )
					{
						$rec->title = JText::_('CMORERECORDS');
						$rec->id = -1;
						$rec->url = $section->link;
					}
					?>
					<div id="modrec-<?php echo $rec->id;?>"></div>
					<script type="text/javascript">
						modJsc_initTreeRec<?php echo $module->id;?>(<?php echo $rec->id;?>, '<?php echo $rec->title;?>', '<?php echo $rec->url;?>' );
					</script>
			<?php endforeach;?>
		</div>
	<?php endif;?>
<?php endif; ?>

	<?php if ( $footerText ) : ?>
		<div class="js_cc<?php echo $params->get( 'moduleclass_sfx' ) ?>"><?php echo $footerText; ?></div>
	<?php endif; ?>

</div>

<div class="clearfix"> </div>