<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$params = $this->tmpl_params['list'];
if(!$params->get('tmpl_params.field_id_geo', 0))
{

	Factory::getApplication()->enqueueMessage( JText::_('CERRORNOGEOFIELD'),'warning');

	return;
}
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach($exclude as &$value)
{
	$value = @$this->fields_keys_by_id[$value];
}
$app = JFactory::getApplication();
$fieldtable = JTable::getInstance('Field', 'JoomcckTable');
$fieldtable->load($params->get('tmpl_params.field_id_geo', 0));
$fieldparams = new JRegistry($fieldtable->params);
$markerfolder = $fieldparams->get('params.map_icon_src.dir', 'custom');
$w = 32;
$h = 37;
if($fieldparams->get('params.map_icon_src.icon'))
{
	$size = getimagesize(JPATH_ROOT . '/components/com_joomcck/fields/geo/markers/' . $markerfolder . '/' . $fieldparams->get('params.map_icon_src.icon'));
	$size = new JRegistry($size);
	$w    = $size->get('0', $w);
	$h    = $size->get('1', $h);
}

MapHelper::loadGoogleMapAPI();

if($params->get('tmpl_params.map_claster'))
{
	JFactory::getDocument()->addScript('components/com_joomcck/fields/geo/assets/markerclusterer.min.js');
}

?>
<style>
	#map_canvas .info-window H4 {
		margin: 0px;
	}

	#map_canvas {
		margin: 15px 0;
	}

	#map_canvas label {
		width: auto;
		display: inline;
	}

	#map_canvas img {
		max-width: none;
	}
</style>

<div id="message"></div>
<?php if($params->get('tmpl_params.map_load')): ?>
	<div id="load-bar" class="progress progress-striped active">
		<div class="bar" style="width: 100%;"><?php echo JText::_('CLOADARTICLE') ?></div>
	</div>
<?php endif; ?>

<div id="map_canvas" class="" style="width: <?php echo $params->get('tmpl_params.map_width', '100%') ?>; height: <?php echo $params->get('tmpl_params.map_height', '500px') ?>"></div>

<div class="form-horizontal">
	<?php if($params->get('tmpl_params.map_fitbonds', 1)): ?>
		<span class="btn btn-sm btn-primary" onclick="fitBounds()"><?php echo JText::_('CFITMAP') ?></span>
	<?php endif; ?>
	<?php if($params->get('tmpl_params.map_heat')): ?>
		<span class="btn-sm btn-light border" id="heat"><?php echo JText::_('CHEATMAP') ?></span>
	<?php endif; ?>
	<div class="btn-group float-end">
		<?php if($params->get('tmpl_params.map_weather')): ?>
			<span class="btn-sm btn-light border" id="cloud"><?php echo JText::_('CCLOUDS') ?></span>
			<span class="btn-sm btn-light border" id="weather"><?php echo JText::_('CWEATHER') ?></span>
		<?php endif; ?>
		<?php if($params->get('tmpl_params.map_pano')): ?>
			<span class="btn-sm btn-light border" id="pano"><?php echo JText::_('CPNORAMIO') ?></span>
		<?php endif; ?>
		<?php if($params->get('tmpl_params.map_traff')): ?>
			<span class="btn-sm btn-light border" id="traff"><?php echo JText::_('CTRAFF') ?></span>
		<?php endif; ?>
		<?php if($params->get('tmpl_params.map_trans')): ?>
			<span class="btn-sm btn-light border" id="trans"><?php echo JText::_('CTRANS') ?></span>
		<?php endif; ?>
		<?php if($params->get('tmpl_params.map_bike')): ?>
			<span class="btn-sm btn-light border" id="bike"><?php echo JText::_('CBIKE') ?></span>
		<?php endif; ?>
	</div>
</div>
<br>

<script type="text/javascript">
(function($) {
	var infowindow = new google.maps.InfoWindow();
	var bounds = new google.maps.LatLngBounds();
	var markers = [];
	var markerCluster = null;
	var heatmapData = [];
	var myLatlng = new google.maps.LatLng(<?php echo $params->get('tmpl_params.map_init_lat', '42.293564192170095') ?>, <?php echo $params->get('tmpl_params.map_init_lng', '-33.33983659744263') ?>);
	var myOptions = {
		zoom: <?php echo $params->get('tmpl_params.map_init_zoom', 10) ?>,
		center:                myLatlng,
		mapTypeId:             google.maps.MapTypeId.<?php echo $params->get('tmpl_params.map_view', 'ROADMAP')?>,
		mapTypeControl: <?php echo ($params->get('tmpl_params.map_type') ? '1' : '0')?>,
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
		},
		overviewMapControl:<?php echo ($params->get('tmpl_params.map_overview', 1) ? '1' : '0')?>,
		draggable: <?php echo ($params->get('tmpl_params.map_drag', 1) ? '1' : '0')?>,
		panControl: <?php echo ($params->get('tmpl_params.map_pan', 1) ? '1' : '0')?>,
		zoomControl: <?php echo ($params->get('tmpl_params.map_zoom', 1) ? '1' : '0')?>,
		scaleControl: <?php echo ($params->get('tmpl_params.map_scale', 1) ? '1' : '0')?>,
		rotateControl:<?php echo ($params->get('tmpl_params.map_rotate', 1) ? '1' : '0')?>,
		streetViewControl:<?php echo ($params->get('tmpl_params.map_street', 1) ? '1' : '0')?>,
		scrollwheel:<?php echo ($params->get('tmpl_params.map_scroll', 1) ? '1' : '0')?>

	}

	google.maps.visualRefresh = true;
	window.map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);

	<?php if($params->get('tmpl_params.map_style')):?>
	var mapStyle =    <?php include JPATH_ROOT. '/components/com_joomcck/library/js/mapstyles/'.$params->get('tmpl_params.map_style'); ?>;
	map.setOptions({styles: mapStyle});
	<?php endif;?>

	$.ajax({
		url:  Joomcck.field_call_url, dataType: 'json', type: 'POST',
		data: {
			field_id: <?php echo $params->get('tmpl_params.field_id_geo', 0); ?>,
			func:      'onGetMarkersList',
			section_id: <?php echo $this->section->id;?>,
			cat_id: <?php echo (int)$this->category->id;?>,
			user_id: <?php echo $app->input->getInt('user_id',0);?>,
			ucat_id: <?php echo $app->input->getInt('ucat_id',0);?>,
			view_what: '<?php echo $app->input->get('view_what');?>',
			markers: <?php echo ($params->get('tmpl_params.map_records', 0))?>,
			_rrid: <?php echo $app->input->getInt('_rrid',0);?>,
			_rfid: <?php echo $app->input->getInt('_rfid',0);?>,
			_rdist: <?php echo $app->input->getInt('_rdist',0);?>
		}
	}).done(function(json) {
		<?php if($params->get('tmpl_params.map_load')):?>
		$('#load-bar').hide();
		<?php endif;?>


		if(json.result == 1) return;

		$.each(json.result, function(k, v) {
			if(!v.lat || !v.lng) return true;
			var ll = new google.maps.LatLng(v.lat, v.lng);
			<?php if($params->get('tmpl_params.map_heat')):?>
			heatmapData.push(ll);
			<?php endif;?>
			drawMarker(ll, k, v.marker);
		});

		<?php if($params->get('tmpl_params.map_claster')):?>
		markerCluster = new MarkerClusterer(map, markers, {
			gridSize: <?php echo $params->get('tmpl_params.map_claster_size', 40); ?>,
			imagePath: '<?php echo JUri::root(TRUE) ?>/components/com_joomcck/fields/geo/assets/m/',
			maxZoom: 15
		});
		//markerCluster.setIgnoreHidden(true);
		<?php endif;?>

		<?php if($params->get('tmpl_params.map_init') == 0): ?>
		map.fitBounds(bounds);
		<?php endif;?>
	});

	function drawMarker(mPosition, record_id, icon) {
		var msize = new google.maps.Size(<?php echo $w; ?>, <?php echo $h; ?>);
		var mpoint = new google.maps.Point(<?php echo round($w / 2);?>, <?php echo $h;?>);
		var markerIcon = new google.maps.MarkerImage(icon, msize, new google.maps.Point(0, 0), mpoint);
		var mOptions = {
			'map': map, 'position': mPosition, icon: markerIcon,
			<?php if($params->get('tmpl_params.map_anim', 1)) "'animation' : google.maps.Animation.DROP," ?>
		};

		var marker = new google.maps.Marker(mOptions);

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"><?php echo JText::_('CLOADARTICLE')?></div></div>');
			infowindow.open(map, marker);
			$.ajax({
				url:  Joomcck.field_call_url, dataType: 'json', type: 'POST',
				data: {
					field_id: <?php echo $params->get('tmpl_params.field_id_geo', 0); ?>,
					func:      'onInfoWindow',
					record_id: record_id,
					section_id: <?php echo $this->section->id;?>,
					cat_id: <?php echo (int)$this->category->id;?>,
					user_id: <?php echo $app->input->getInt('user_id',0);?>,
					ucat_id: <?php echo $app->input->getInt('ucat_id',0);?>,
					view_what: '<?php echo $app->input->get('view_what');?>',
					list_tmpl: '<?php echo $this->list_template  ?>',
					info_window: 1
				}
			}).done(function(json) {
				if(json.result) {
					infowindow.setContent(json.result);
					infowindow.open(map, marker);
				}
			});
		});

		bounds.extend(mPosition);
		markers.push(marker);
	}

	window.fitBounds = function() {
		map.fitBounds(bounds);
	}
	<?php if($params->get('tmpl_params.map_heat')):?>
	var heatDisplayIsOn = false;
	var heatmap = new google.maps.visualization.HeatmapLayer({
		data: heatmapData
	});
	google.maps.event.addDomListener(document.getElementById('heat'),
		'click', function() {
			$(this).toggleClass('active');
			if(heatDisplayIsOn) {
				heatmap.setMap(null);
				$.each(markers, function(k, v) {
					v.setVisible(true);
				});
				heatDisplayIsOn = false;
				<?php if($params->get('tmpl_params.map_claster')):?>
				markerCluster.repaint();
				<?php endif;?>
			}
			else {
				heatmap.setMap(map);
				$.each(markers, function(k, v) {
					v.setVisible(false);
				});
				heatDisplayIsOn = true;
				<?php if($params->get('tmpl_params.map_claster')):?>
				markerCluster.repaint();
				<?php endif;?>
			}
		});
	<?php endif;?>
	<?php if($params->get('tmpl_params.map_bike')):?>
	var bikeDisplayIsOn = false;
	var bikeLayer = new google.maps.BicyclingLayer();
	google.maps.event.addDomListener(document.getElementById('bike'),
		'click', function() {
			$(this).toggleClass('active');
			if(bikeDisplayIsOn) {
				bikeLayer.setMap(null);
				bikeDisplayIsOn = false;
			}
			else {
				bikeLayer.setMap(map);
				bikeDisplayIsOn = true;
			}
		});
	<?php endif;?>
	<?php if($params->get('tmpl_params.map_trans')):?>
	var transDisplayIsOn = false;
	var transitLayer = new google.maps.TransitLayer();
	google.maps.event.addDomListener(document.getElementById('trans'),
		'click', function() {
			$(this).toggleClass('active');
			if(transDisplayIsOn) {
				transitLayer.setMap(null);
				transDisplayIsOn = false;
			}
			else {
				transitLayer.setMap(map);
				transDisplayIsOn = true;
			}
		});
	<?php endif;?>
	<?php if($params->get('tmpl_params.map_traff')):?>
	var traffDisplayIsOn = false;
	var traffLayer = new google.maps.TrafficLayer();
	google.maps.event.addDomListener(document.getElementById('traff'),
		'click', function() {
			$(this).toggleClass('active');
			if(traffDisplayIsOn) {
				traffLayer.setMap(null);
				traffDisplayIsOn = false;
			}
			else {
				traffLayer.setMap(map);
				traffDisplayIsOn = true;
			}
		});
	<?php endif;?>
	<?php if($params->get('tmpl_params.map_pano')):?>
	var panoDisplayIsOn = false;
	var panoramioLayer = new google.maps.panoramio.PanoramioLayer();
	google.maps.event.addDomListener(document.getElementById('pano'),
		'click', function() {
			$(this).toggleClass('active');
			if(panoDisplayIsOn) {
				panoramioLayer.setMap(null);
				panoDisplayIsOn = false;
			}
			else {
				panoramioLayer.setMap(map);
				panoDisplayIsOn = true;
			}
		});
	<?php endif;?>

	<?php if($params->get('tmpl_params.map_weather')):?>
	var cloudDisplayIsOn = false;
	var weatherDisplayIsOn = false;

	var cloudLayer = new google.maps.weather.CloudLayer();
	var weatherLayer = new google.maps.weather.WeatherLayer({temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS});

	google.maps.event.addDomListener(document.getElementById('cloud'),
		'click', function() {
			$(this).toggleClass('active');
			if(cloudDisplayIsOn) {
				cloudLayer.setMap(null);
				cloudDisplayIsOn = false;
			}
			else {
				cloudLayer.setMap(map);
				cloudDisplayIsOn = true;
			}
		});

	google.maps.event.addDomListener(document.getElementById('weather'),
		'click', function() {
			$(this).toggleClass('active');
			if(weatherDisplayIsOn) {
				weatherLayer.setMap(null);
				weatherDisplayIsOn = false;
			}
			else {
				weatherLayer.setMap(map);
				weatherDisplayIsOn = true;
			}
		});
	<?php endif;?>
}(jQuery))

</script>

<?php if($params->get('tmpl_core.tmpl_list')): ?>
	<?php
	$tmpl = explode('.', $params->get('tmpl_core.tmpl_list'));
	$this->section->params->set('general.tmpl_list', $params->get('tmpl_core.tmpl_list'));
	$this->tmpl_params['list'] = CTmpl::prepareTemplate('default_list_', 'general.tmpl_list', $this->section->params);
	include_once(JPATH_ROOT . "/components/com_joomcck/views/records/tmpl/default_list_{$tmpl[0]}.php");
	?>
<?php else: ?>
	<?php $this->tmpl_params['list']->set('tmpl_core.item_pagination', 0) ?>
<?php endif; ?>
