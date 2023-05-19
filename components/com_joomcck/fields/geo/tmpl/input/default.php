<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$default = new JRegistry($this->value);
$contacts = $links = $link_list = $contact_list = $adr_list = array();
$defaultmarker = $default->get('position.marker', $this->params->get('params.map_icon_src.icon'));
$lang = explode('-', JFactory::getLanguage()->getTag());
$lang = $lang[0];

?>

<style>
.img-marker {
	margin-right: 8px;
	margin-bottom: 8px;
	cursor: pointer;
}
#markertabs li a {
	padding: 2px 12px;
}
#map_canvas_<?php echo $this->id;?> label { width: auto; display:inline; }
#map_canvas_<?php echo $this->id;?> img { max-width: none; }
</style>
<?php if(in_array($this->params->get('params.adr_enter'), $this->user->getAuthorisedViewLevels())):?>

	<?php $address = JFormFieldCgeo::getAddressFields();?>
	<?php

	$sort = array('country' => $address['country'], 'state' => $address['state'],
	'city' => $address['city'], 'zip' => $address['zip']);
	?>
	<?php if(count($address)): $key = 0;?>
		<h5><?php echo JText::_("G_ADDRESS");?></h5>
		<?php foreach($sort as $name => $adr):?>
			<?php
			if(! $this->params->get('params.address.' . $name . '.show', 1))
			{
				continue;
			}
			$input = $this->_input('address', $name, 'text');
			$label = $this->_label('address', $name, $adr['label']);
			$adr_list[$name] = $input;
			?>
			<?php if($key % 2 == 0):?>
			<div class="row">
			<?php endif;?>

			<div class="col-md-6">
				<small><?php echo $label;?></small><br>

				<?php if($name == 'country'):?>
					<?php echo $this->countries();?>
				<?php else:?>
					<?php echo $input; ?>
				<?php endif;?>
			</div>

			<?php if($key % 2 != 0):?>
			</div>
			<?php endif; $key++;?>

		<?php endforeach; ?>

		<?php if($key % 2 != 0):?>
			</div>
		<?php endif;?>

		<?php if($this->params->get('params.address.address1.show', 1)):?>
			<div class="row">
				<?php if($this->params->get('params.address.address1.show', 1)):?>
					<div class="col-md-12">
						<small><?php echo $this->_label('address', 'address1', $address['address1']['label']);?></small><br>
						<?php echo  $this->_input('address', 'address1'); ?>
					</div>
				<?php endif;?>
			</div>
		<?php endif;?>
		<?php if($this->params->get('params.address.address2.show', 1)):?>
			<div class="row">
				<?php if($this->params->get('params.address.address2.show', 1)):?>
					<div class="col-md-12">
						<small><?php echo $this->_label('address', 'address2', $address['address2']['label']);?></small><br>
						<?php echo  $this->_input('address', 'address2'); ?>
					</div>
				<?php endif;?>
			</div>
		<?php endif;?>
		<?php if($this->params->get('params.address.company.show', 1) || $this->params->get('params.address.person.show', 1)):?>
			<div class="row">
				<?php if($this->params->get('params.address.company.show', 1)):?>
					<div class="col-md-<?php echo ($this->params->get('params.address.person.show', 1) ? 6 : 12) ?>">
						<small><?php echo $this->_label('address', 'company', $address['company']['label']);?></small><br>
						<?php echo  $this->_input('address', 'company'); ?>
					</div>
				<?php endif;?>
				<?php if($this->params->get('params.address.person.show', 1)):?>
					<div class="col-md-<?php echo ($this->params->get('params.address.company.show', 1) ? 6 : 12) ?>">
						<small><?php echo $this->_label('address', 'person', $address['person']['label']);?></small><br>
						<?php echo  $this->_input('address', 'person'); ?>
					</div>
				<?php endif;?>
			</div>
		<?php endif;?>
		<?php if(in_array($this->params->get('params.map_marker'), $this->user->getAuthorisedViewLevels())):?>
			<br/>
			<button class="btn btn- btn-light border" type="button" id="toadr_loc<?php echo $this->id; ?>"><?php echo JText::_('G_ADDRESSFROMMARKER');?></button>

			<button class="btn btn-sm btn-light border" type="button" id="adr_loc<?php echo $this->id; ?>"><?php echo JText::_('G_MARKERFROMADDRESS');?></button>
		<?php endif;?>
		<div class="clearfix"></div>
	<?php endif;?>
<?php endif;?>

<?php if(in_array($this->params->get('params.map_marker'), $this->user->getAuthorisedViewLevels())):?>
	<h5>
		<?php if(!$this->params->get('params.map_require')):?>
		<button class="btn btn-sm btn-danger float-end hide" id="rmp<?php echo $this->id?>" type="button"><?php echo JText::_('G_REMOVEPOSITION');?></button>
		<?php endif; ?>

		<?php if($this->params->get('params.map_require')):?>
			<?php echo JHtml::image(JURI::root() . 'media/com_joomcck/icons/16/asterisk-small.png', 'Required', array('align'=>'absmiddle', 'rel' => 'tooltip', 'data-bs-title' => JText::_('CREQUIRED')));?>
		<?php endif; ?>

		<?php echo JText::_('G_MAP');?>
	</h5>
	<style type="text/css">
		#locationField {
			display: inline;
			position: absolute;
			z-index: 5;
		}
		#locationField input {
			width: 280px;
		}
	</style>
	<div style="margin-bottom: 15px; position: relative;">
		<div id="locationField">
			<input id="autocomplete<?php echo $this->id; ?>" placeholder="<?php echo JText::_('G_ENTERCITY'); ?>" type="text" />
		</div>
		<span style="margin-left: 298px">
			<b>OR</b>
			<button class="btn" type="button" id="cur_loc<?php echo $this->id; ?>""><?php echo JText::_('G_MARKERCURRENTLOCATION');?></button>
		</span>
	</div>
	<div id="map_canvas_<?php echo $this->id;?>" style="width:<?php echo $this->params->get('params.map_width', '100%');?>; height:<?php echo $this->params->get('params.map_height', '200px');?>"></div>
	<small><?php echo JText::_('G_DRAGMARKER');?></small>
	<?php if(in_array($this->params->get('params.map_manual_position'), $this->user->getAuthorisedViewLevels())):?>
	<div class="row">
		<div class="col-md-6">
			<small><?php echo JText::_('G_LAT')?></small>
			<?php echo $this->_input('position', 'lat');?>
		</div>
		<div class="col-md-6">
			<small><?php echo JText::_('G_LNG')?></small>
			<?php echo $this->_input('position', 'lng');?>
		</div>
	</div>
	<?php else:?>
		<?php echo $this->_input('position', 'lat', 'hidden');?>
		<?php echo $this->_input('position', 'lng', 'hidden');?>
	<?php endif;?>
	<?php echo $this->_input('position', 'zoom', 'hidden');?>

	<?php
		$dir = JPATH_ROOT. '/components/com_joomcck/fields/geo/markers'. DIRECTORY_SEPARATOR .$this->params->get('params.map_icon_src.dir', 'custom');
		$path = '/components/com_joomcck/fields/geo/markers/'.$this->params->get('params.map_icon_src.dir', 'custom').'/';
	?>

	<?php if(in_array($this->params->get('params.map_whoicon'), $this->user->getAuthorisedViewLevels())):?>
		<h5><?php echo JText::_('G_MARKER');?></h5>
		<?php echo $this->_input('position', 'marker', 'hidden');?>
		<div style="max-height:220px;width:100%;overflow-x:hidden;overflow-y:scroll">
			<?php $folders = JFolder::folders($dir);?>
			<?php if($folders):?>
				<div class="tabbable tabs-left">
					<ul class="nav nav-tabs" id="markertabs">
						<?php foreach ($folders AS $folder):?>
							<li><a href="#tab-<?php echo $folder?>" data-toggle="tab"><?php echo JText::_($folder);?></a></li>
						<?php endforeach;?>
					</ul>
					<div class="tab-content">
						<?php foreach ($folders AS $folder):?>
							<div class="tab-pane active" id="tab-<?php echo $folder?>">
								<?php echo $this->_listmarkers($dir.DIRECTORY_SEPARATOR.$folder, $defaultmarker, $folder.'/'); ?>
							</div>
						<?php endforeach;?>
					</div>
				</div>
				<script>
					jQuery('#markertabs a:first').tab('show');
				</script>
			<?php else:?>
				<?php echo $this->_listmarkers($dir, $defaultmarker); ?>
			<?php endif;?>
		</div>
	<?php endif;?>
	<script type="text/javascript">
		<?php
		$style = 'null';
		if(substr($this->params->get('params.map_style'), -5) == '.json')
		{
			$style = file_get_contents(JPATH_ROOT . '/components/com_joomcck/library/js/mapstyles/' . $this->params->get('params.map_style'));
		}
		$w = 32;
		$h = 37;
		if(JFile::exists(JPATH_ROOT.'/'.$path.$defaultmarker))
		{
			$msize = getimagesize(JPATH_ROOT.'/'.$path.$defaultmarker);
			$w = $msize[0];
			$h = $msize[1];
		}

		$default_country = @$this->values['address']['country'];
		if(!$default_country && $this->params->get('params.country_limit') && (count($this->params->get('params.country_limit')) == 1))
		{
		    $country_array = $this->params->get('params.country_limit');
		    $default_country = array_shift($country_array);
		}
		?>
		jQuery(function(){jQuery('#map_canvas_<?php echo $this->id;?>').loadmap({
			id: '<?php echo $this->id; ?>',
			style: <?php echo $style; ?>,
			lat: '<?php echo $default->get('position.lat'); ?>',
			lng: '<?php echo $default->get('position.lng'); ?>',
			plat: '<?php echo $this->params->get('params.map_lat'); ?>',
			plng: '<?php echo $this->params->get('params.map_lng'); ?>',
			zoom: '<?php echo $default->get('position.zoom'); ?>',
			pzoom: '<?php echo $this->params->get('params.map_zoom'); ?>',
			marker: '<?php echo $defaultmarker;?>',
			marker_path: '<?php echo JURI::root(TRUE).$path;?>',
			marker_w: '<?php echo $w;?>',
			marker_h: '<?php echo $h;?>',
			root: '<?php echo JURI::root(TRUE);?>',
			lang: '<?php echo substr(JFactory::getLanguage()->getTag() ,0 ,2); ?>',
			initposition: <?php echo (int)$this->params->get('params.map_find_position');?>,
            defaultcountry: '<?php echo $default_country; ?>',
			strings: {
				addrnotfound: '<?php echo JText::_('G_ADDRESSNOTFOUND');  ?>',
				addrnotentered: '<?php echo JText::_('G_ENTERADDRESS'); ?>',
				geocodefail: '<?php echo JText::_('G_GEONOTSUCCESSFUL'); ?>'
			}
		})});
		<?php if(count($adr_list) <= 0): ?>
		    jQuery('#adr_loc<?php echo $this->id; ?>').css('display', 'none');
		<?php endif;?>
	</script>
<?php endif;?>

<?php if(in_array($this->params->get('params.adr_enter'), $this->user->getAuthorisedViewLevels())):?>
	<?php
	$format = '<tr><td nowrap="nowrap" width="1%%">%s %s</td><td><div class="row">%s</div></td></tr>';
	$contacts = JFormFieldCgeo::getAditionalFields();

	foreach($contacts as $name => $contact)
	{
		if(! $this->params->get('params.contacts.' . $name . '.show', 1))
		{
			continue;
		}
		$input = $this->_input('contacts', $name);
		$group = 'contacts';
		if($contact['label'] == JText::_('G_SKYPE'))
		{
			$contact['icon'] = JURI::root() . 'components/com_joomcck/fields/geo/icons/skype.png';
		}
		$contact_list[] = sprintf($format, JHtml::image($contact['icon'], $contact['label']), $this->_label('contacts', $name, $contact['label']), $input);
	}
	?>
	<?php if($contact_list):?>
		<h5><?php echo JText::_("G_INSTANTCONTACTS");?></h5>
		<table class="table table-hover"><?php echo implode(' ', $contact_list);?></table>
	<?php endif;?>

	<?php
	$links = JFormFieldCgeo::getAditionalinks();
	foreach($links as $name => $link)
	{
		if(! $this->params->get('params.links.' . $name . '.show', 1))
		{
			continue;
		}
		$input = $this->_input('links', $name);
		$link_list[] = sprintf($format, JHtml::image($link['icon'], $link['label']), $this->_label('links', $name, $link['label']), $input);
	}
	?>
	<?php if($link_list):?>
		<h5><?php echo JText::_("G_LINKS");?></h5>
		<table class="table table-hover"><?php echo implode(' ', $link_list);?></table>
	<?php endif; ?>

<?php endif;?>