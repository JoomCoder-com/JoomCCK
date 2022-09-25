<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();


$out = $contacts = $links = $address = array();
$client = ($client == 'list') ? 1 : 2;
$links = JFormFieldCgeo::getAditionalinks();
$contacts = JFormFieldCgeo::getAditionalFields();
$address = JFormFieldCgeo::getAddressFields();
$app = JFactory::getApplication();
$sho_map = in_array($this->params->get('params.map_client'), array($client, 3))	&& in_array($this->params->get('params.map_view'), $this->user->getAuthorisedViewLevels());
$sho_sv = in_array($this->params->get('params.sv_client'), array($client, 3))	&& in_array($this->params->get('params.sv_view'), $this->user->getAuthorisedViewLevels());
$height = 100;
if($sho_map && $sho_sv && $this->params->get('params.sv_layout') == 0)
{
	$height = 50;
}

if($this->request->get('func') == 'onInfoWindow')
{
	$this->params->set('params.map_view',0);
}
$a = $c = $l = array();
if(in_array($this->params->get('params.adr_view'), $this->user->getAuthorisedViewLevels())):?>
	<?php if(! empty($this->value['address'])):
		ArrayHelper::clean_r($this->value['address']);
		foreach($this->value['address'] as $name => $link)
		{
			if($name == 'country')
			{
				$this->value['address'][$name] = $this->_getcountryname($link);
			}
			if(! in_array($this->params->get("params.address.{$name}.show", 3), array($client, 3)))
				unset($this->value['address'][$name]);
		}

		if(! empty($this->value['address']['company']))
		{
			$a[] = '<strong>' . $this->value['address']['company'] . '</strong><br>';
			$mecard['n'] = 'N:' . $this->value['address']['company'];
			unset($this->value['address']['company']);
		}
		elseif(! empty($this->value['address']['person']))
		{
			$a[] = '<strong>' . $this->value['address']['person'] . '</strong><br>';
			$mecard['n'] = 'N:' . $this->value['address']['person'];
			unset($this->value['address']['person']);
		}

		if(! empty($this->value['address']))
		{
			$ordered = array();
			foreach(array('address1', 'address2', 'city', 'state', 'zip', 'country') as $key)
			{
				if(array_key_exists($key, $this->value['address']))
				{
					$ordered[$key] = $this->value['address'][$key];
					unset($this->value['address'][$key]);
				}
			}

			$ordered = $ordered + $this->value['address'];
			$a[] = implode(", ", $ordered);
			$mecard[] = 'ADR:' . implode(", ", $ordered);
			$this->value['address'] = $ordered;
		}
	endif;

	if(! empty($this->value['contacts'])):
		ArrayHelper::clean_r($this->value['contacts']);
		foreach($this->value['contacts'] as $name => $contact)
		{
			if(empty($contacts[$name])) continue;
			if(! in_array($this->params->get("params.contacts.{$name}.show", 3), array($client, 3)))
				continue;
			$text = $contact;
			if(! empty($contacts[$name]['patern']))
			{
				$text = str_replace(array('[VALUE]', '[NAME]'), array($contact,	JText::_($contacts[$name]['label'])), $contacts[$name]['patern']);
			}
			$c[] = '<abbr title="'.JText::_($contacts[$name]['label']).'" rel="tooltip" data-original-title="'.JText::_($contacts[$name]['label']).'">
				<img src="'.str_replace(array('[VALUE]', '[NAME]'), array($contact, JText::_($contacts[$name]['label'])), $contacts[$name]['icon']).'">
				 '.\Joomla\String\StringHelper::substr(JText::_($contacts[$name]['label']), 0, 1).':</abbr> '.$text;
		}
	endif;

	if(! empty($this->value['links'])) :
		ArrayHelper::clean_r($this->value['links']);
		foreach($this->value['links'] as $name => $link)
		{
			if(! in_array($this->params->get("params.links.{$name}.show", 3), array($client,  3)))
				continue;
			if(in_array(trim($link), array('http://', 'http:')))
				continue;
			$l[] = '<abbr title="'.JText::_($links[$name]['label']).'" rel="tooltip" data-original-title="'.JText::_($links[$name]['label']).'">
				<img src="'.$links[$name]['icon'].'">'.\Joomla\String\StringHelper::substr(JText::_($links[$name]['label']), 0, 1).':</abbr>
				<a rel="nofollow" target="_blank" href="'.$link.'">'.($this->params->get('params.links_labels') ?  $link : JText::_($links[$name]['label'])).'</a>';
		}
		?>
	<?php endif; ?>
	<?php if($a || $c || $l):  ?>
		<address>
			<?php if($a): ?>
				<?php echo implode("\n", $a);?>
				<br><br>
			<?php endif;?>
			<?php if($c): ?>
				<?php echo implode("<br>", $c);?>
				<br>
			<?php endif;?>
			<?php if($l): ?>
				<?php echo implode("<br>", $l);?>
			<?php endif;?>
		</address>
	<?php endif;?>
<?php endif;

if(in_array($this->params->get('params.qr_code_address'), array($client, 3))):
	$w = $this->params->get('params.qr_width_address', 250);

	if(! empty($this->value['contacts']['mob']))
	{
		$mecard[] = 'TEL:' . $this->value['contacts']['mob'];
	}
	elseif(! empty($this->value['contacts']['tel']))
	{
		$mecard[] = 'TEL:' . $this->value['contacts']['tel'];
	}
	elseif(! empty($this->value['contacts']['fax']))
	{
		$mecard[] = 'TEL:' . $this->value['contacts']['fax'];
	}

	if(! empty($this->value['links']['web']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['web'];
	}
	elseif(! empty($this->value['links']['facebook']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['facebook'];
	}
	elseif(! empty($this->value['links']['twitter']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['twitter'];
	}
	elseif(! empty($this->value['links']['google']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['google'];
	}
	elseif(! empty($this->value['links']['youtube']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['youtube'];
	}

	if(empty($mecard['url']) && ! empty($this->value['links']))
	{
		$mecard['url'] = 'URL:' . array_shift($this->value['links']);
	}
	if(!empty($mecard['url']) && $mecard['url'] == 'URL:http://')
	{
		unset($mecard['url']);
	}

	if(!empty($mecard) && $this->email)
	{
		$mecard[] = 'EMAIL:' . $this->email;
	}

	if(!empty($mecard)) :
		$url = 'http://chart.apis.google.com/chart?cht=qr&chs=' . $w . 'x' . $w . '&chl=MECARD:' . implode(';', $mecard) . ';;';
		?>
		<div class="qr-image qr-image-address"><?php echo JHtml::image($url, JText::_('G_ADDRESSQR'), array('width' => $w, 'height' => $w));?></div>
	<?php endif;?>
<?php endif;


if(
	($sho_map || $sho_sv)
	&& !empty($this->value['position']['lat'])
	&& !empty($this->value['position']['lng'])
	&& $this->request->get('info_window') == 0) :
	$params = new JRegistry($this->value['position']);

	list($icon_w, $icon_h, $icon) = $this->getMarker();
	$icon_m = round($icon_w / 2);
	?>
	<?php echo $this->_title(JText::_('G_MAP'), $client);?>
	<style>
		#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?> label { width: auto; display:inline; }
		#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?> img { max-width: none; }
	</style>

	<div class="img-polaroid" style="width: <?php echo $this->params->get('params.map_dwidth', '100%');?>; height:<?php echo $this->params->get('params.map_dheight', '200px');?>">
		<?php if($sho_map): ?>
			<div <?php echo ($this->params->get('params.sv_layout') == 1 && $sho_sv ? 'style="width:50%;height:'.$height.'%" class="MapDetail pull-left"' : 'class="MapDetail" style="width:100%;height:'.$height.'%"'); ?> id="map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?>"></div>
		<?php endif; ?>
		<?php if($sho_sv): ?>
			<div <?php echo ($this->params->get('params.sv_layout') == 1 && $sho_map ? 'style="width:50%;height:'.$height.'%" class="StreetviewDetail pull-left"' : 'class="StreetviewDetail" style="width:100%;height:'.$height.'%"'); ?> id="map_pano_<?php echo $record->id;?>_<?php echo $this->id;?>"></div>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
	<?php if($this->params->get('params.map_lat_lng')): ?>
		<p>(<?php echo $params->get('lat');?>,<?php echo $params->get('lng');?>)</p>
	<?php endif; ?>

	<script type="text/javascript">
	function initialize_<?php echo $record->id;?>_<?php echo $this->id;?>()
	{
		google.maps.visualRefresh = true;
		var msize = new google.maps.Size(<?php echo $icon_w;?>,<?php echo $icon_h;?>);
		var mpoint = new google.maps.Point(<?php echo $icon_m;?>,<?php echo $icon_h;?>);
		var markerIcon = new google.maps.MarkerImage('<?php echo $icon;?>', msize, new google.maps.Point(0,0), mpoint);
		var myLatlng = new google.maps.LatLng(<?php echo $params->get('lat');?>, <?php echo $params->get('lng');?>);
		var map<?php echo $record->id.$this->id;?> = new google.maps.Map(document.getElementById("map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?>"), {zoom:<?php echo $params->get('zoom', 15);?>,center:myLatlng, mapTypeId: google.maps.MapTypeId.ROADMAP, draggable:0, scrollwheel:0});

	<?php if($this->params->get('params.map_style')):?>
		var mapStyle = <?php echo JFile::read(JPATH_ROOT . '/components/com_joomcck/library/js/mapstyles/' . $this->params->get('params.map_style'));?>;
		map<?php echo $record->id.$this->id;?>.setOptions({styles: mapStyle});
	<?php endif; ?>

		new google.maps.Marker({position: myLatlng, map: map<?php echo $record->id.$this->id;?>, icon: markerIcon});

		var id = jQuery('#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?>').parents('div.tab-pane').attr('id');
		if(id) {
			jQuery('a[href*="' + id + '"]').one('click', function(){
				setTimeout(function(){
					google.maps.event.trigger(map<?php echo $record->id.$this->id;?>, 'resize');
					map<?php echo $record->id.$this->id;?>.setCenter(myLatlng);
				}, 100);
			});
		}

		jQuery('#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?>').one('mouseenter', function(){
			setTimeout(function(){
				google.maps.event.trigger(map<?php echo $record->id.$this->id;?>, 'resize');
				map<?php echo $record->id.$this->id;?>.setCenter(myLatlng);
			}, 100);
		});

		google.maps.event.addListenerOnce(map<?php echo $record->id.$this->id;?>, 'mouseover', function(event) {
			google.maps.event.trigger(map<?php echo $record->id.$this->id;?>, 'resize');
		});

		<?php if($sho_sv): ?>
			var sv = new google.maps.StreetViewService();
			sv.getPanoramaByLocation(myLatlng, 49, function(data, status){
				if(status == 'OK') {
					var panoramaOptions = {
						position: myLatlng,
						pov: {
							heading: 34,
							pitch: 10
						}
					};
					var panorama = new  google.maps.StreetViewPanorama(document.getElementById("map_pano_<?php echo $record->id;?>_<?php echo $this->id;?>"), panoramaOptions);
					map<?php echo $record->id.$this->id;?>.setStreetView(panorama);
				} else {
					jQuery('#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?>').css({height: '100%', width:'100%'});
					jQuery('#map_pano_<?php echo $record->id;?>_<?php echo $this->id;?>').remove();
					google.maps.event.trigger(map<?php echo $record->id.$this->id;?>, 'resize');
				}
			});

		<?php endif; ?>

	}
	initialize_<?php echo $record->id;?>_<?php echo $this->id;?>();
	</script>
<?php endif;

if(in_array($this->params->get('params.qr_code_geo'), array($client, 3)) && isset($this->value['position']['lat']) && isset($this->value['position']['lng']) && $this->value['position']['lat'] && $this->value['position']['lng']):
	$w = $this->params->get('params.qr_width_geo', 120);
	$url = 'http://chart.apis.google.com/chart?cht=qr&chs=' . $w . 'x' . $w . '&chl=geo:' . $this->value['position']['lat'] . ',' . $this->value['position']['lng'];
	?>
	<div class="qr-image qr-image-geo"><?php echo JHtml::image($url, JText::_('G_LOCATIONQR'), array('width' => $w, 'height' => $w));?></div>
<?php endif;
