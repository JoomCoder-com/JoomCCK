<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

/*JHtml::_('formbehavior.chosen', '.select-geo');
$('.select-geo').chosen({
	disable_search_threshold : 10,
	allow_single_deselect : true
});*/

$default = new JRegistry($this->value);
$distance = false;
$radius = ($default->get('radius') ? $default->get('radius') : $this->params->get('params.filter_radius', 20));
if (in_array($this->params->get('params.filter_distance', 0), $this->user->getAuthorisedViewLevels())):

	MapHelper::loadGoogleMapAPI();

	$distance = true;

	$list = array();
	$list[] = JHTML::_('select.option', 'km', JText::_("G_KM"), 'value', 'text');
	$list[] = JHTML::_('select.option', 'm', JText::_('G_M'), 'value', 'text');
	$list = JHtml::_('select.genericlist', $list, "filters[{$this->key}][dist_type]", '', 'value', 'text', $default->get('dist_type', 'km'));

	$clist = $default_country_code = null;
	$post = array('index' => 'country', 'section_id' => $section->id, 'donotgolower' => 1);
	$countries = $this->_getList($post, 'GO_CONLIM');
	if($countries)
	{
		if(count($countries) > 2)
		{
			$clist = JHtml::_('select.genericlist', $countries, "filters[{$this->key}][complete_limit]", '', 'value', 'text', $default->get('complete_limit', NULL));
		}
		elseif (count($countries) == 2)
		{
			array_shift($countries);
			$countries = array_values($countries);
			$default_country_code = strtolower($countries[0]->value);
			$clist = $countries[0]->text;
		}
	}
	if(!$default_country_code && $this->params->get('params.country_limit') && (count($this->params->get('params.country_limit')) == 1))
	{
		$country_array = $this->params->get('params.country_limit');
		$default_country_code = array_shift($country_array);
	}
	?>
	<style>
	#map_canvas_<?php echo $this->id;?> label { width: auto; display:inline; }
	#map_canvas_<?php echo $this->id;?> img { max-width: none; }
	#locationField {
		display: inline;
		position: absolute;
		z-index: 5;
	}
	#locationField input {
		width: 180px;
	}
	#filters<?php echo $this->key; ?>complete_limit {
		width: 120px;
	}
	</style>
	<div class="form-inline">
		<label><?php echo JText::_('G_DISTANCERADIUS');?></label>
		<input type="text" style="width: 50px" name="filters[<?php echo $this->key;?>][radius]" onkeyup="setCircleRadius<?php echo $this->id  ?>()" value="<?php echo (int)$radius;?>" size="10" id="filters<?php echo $this->key;?>radius">
		<select  onchange="setCircleRadius<?php echo $this->id  ?>()"  style="width: 60px" name="filters[<?php echo $this->key;?>][dist_type]" id="filters<?php echo $this->key;?>dist_type">
			<option value="km" <?php echo ($default->get('dist_type', 'km') == 'km' ? 'selected' : null);?>><?php echo JText::_('G_KM');?></option>
			<option value="m" <?php echo ($default->get('dist_type', 'km') == 'm' ? 'selected' : null);?>><?php echo JText::_('G_M');?></option>
		</select>
		<input type="button" class="btn" value="<?php echo JText::_('G_SET');?>" onclick="setCircleRadius<?php echo $this->id  ?>()">
		<br/>
		<div style="margin-top: 15px; position: relative;">
			<div id="locationField">
				<input id="filter_ac" placeholder="Enter a city" type="text" />
			</div>
			<span style="margin-left: 205px">
				<?php echo $clist;?>
				<?php if($default_country_code): ?>
					<input type="hidden" id="filters<?php echo $this->key; ?>complete_limit" value="<?php echo $default_country_code; ?>">
				<?php endif; ?>
				<b>OR</b>
				<button class="btn" type="button" id="cur_loc"
						onclick="currpos<?php echo $this->id  ?>()"><?php echo JText::_('G_MARKERCURRENTLOCATION');?></button>
			</span>
		</div>
	</div>
	<br>
	<div id="map_canvas_<?php echo $this->id;?>" style="width:<?php echo $this->params->get('params.map_width', '100%');?>; height:<?php echo $this->params->get('params.map_height', '200px');?>;"></div>
	<small><?php echo JText::_('G_DRAGMARKER');?></small>
	<?php echo $this->_input_f('position', 'lat', 'hidden');?>
	<?php echo $this->_input_f('position', 'lng', 'hidden');?>
	<?php echo $this->_input_f('position', 'zoom', 'hidden');?>

	<?php echo $this->_input_f('bounds', 'sw_lat', 'hidden');?>
	<?php echo $this->_input_f('bounds', 'sw_lng', 'hidden');?>
	<?php echo $this->_input_f('bounds', 'ne_lat', 'hidden');?>
	<?php echo $this->_input_f('bounds', 'ne_lng', 'hidden');?>

	<?php echo $this->_input_f('position', 'address', 'hidden');?>
<?php endif; ?>

<?php if (in_array($this->params->get('params.filter_address', 0), $this->user->getAuthorisedViewLevels()) && $this->params->get('params.address.country.show')) : ?>
	<?php 	//echo JHtml::_('sliders.panel', JText::_('G_FILTERBYADDRESS'), 'addr-f');?>
	<h4><?php echo JText::_('G_FILTERBYADDRESS'); ?></h4>
	<div id="filter<?php echo $this->key;?>">
		<?php echo $this->_getChilds(array('index' => 'country', 'section_id' => $section->id));?>
		<?php if (!empty($this->value['country'])) :?>
			<?php $list = $this->_getChilds(array('index' => 'state', 'section_id' => $section->id, 'value' => $this->value['country'], 'country' => $this->value['country']));?>
			<?php if ($list != ' '): ?>
				<span id="state<?php echo $this->key;?>"><?php echo $list;?></span>
			<?php endif;?>
		<?php endif;?>
		<?php if (!empty($this->value['state'])):
			$list = $this->_getChilds(array('country' => @$this->value['country'], 'index' => 'city', 'section_id' => $section->id, 'value' => @$this->value['state']));
			if ($list != ' '):?>
				<span id="city<?php echo $this->key;?>"><?php echo $list;?></span>
			<?php endif;?>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
<?php endif; ?>

<?php if(count($this->markers) > 1): ?>
	<?php 	//echo JHtml::_('sliders.panel', JText::_('G_FILTERBYMARKER'), 'mark-f');?>
	<h4><?php echo JText::_('G_FILTERBYMARKER'); ?></h4>
	<style>
	.filter-marker {
		cursor: pointer;
	}
	.filter-marker.active {
		border:2px solid grey;
	}
	</style>
	<div style="margin: 15px 0px">
		<?php echo $this->_input_f('marker', 'name', 'hidden');?>
		<?php foreach ($this->markers AS $marker):?>
			<img id="mrk-<?php echo md5($marker);?>" class="filter-marker<?php if($default->get('marker.name') == $marker){$active = $marker; echo ' active';}?>" onclick="filterMarker<?php echo $this->id  ?>(this, '<?php echo $marker;?>')" class="hasTip" title="<?php echo $this->_getMarkerName($marker)?>"
				 src="<?php echo JURI::root(TRUE)?>/components/com_joomcck/fields/geo/markers/<?php echo $this->params->get('params.map_icon_src.dir', 'custom');?>/<?php echo $marker;?>" >
		<?php endforeach;?>
	</div>
<?php endif;?>

<script type="text/javascript">
	(function($) {
		<?php if (in_array($this->params->get('params.filter_distance', 0), $this->user->getAuthorisedViewLevels())): ?>

			var myLatLang = new google.maps.LatLng(<?php echo $default->get('position.lat', $this->params->get('params.map_lat', '42.293564192170095'));?>, <?php echo $default->get('position.lng', $this->params->get('params.map_lng', '-33.33983659744263'));?>);
			var myZoom = <?php echo $default->get('position.zoom', $this->params->get('params.map_zoom', 2));?>;

			<?php if(!$default->get('position.lat') && !$default->get('position.lng') && $this->params->get('params.map_find_position')):?>
				$.ajax('http://ip-api.com/json/', {
					dataType: "json"
				}).done(function(result) {
					if(!result) {
						return;
					}

					if(result.status == 'fail') {
						return;
					}

					if(result.lat && result.lon) {
						myLatLang = new google.maps.LatLng(result.lat, result.lon);
					} else if(result.countryCode) {
						var ipad = [];
						if(result.countryCode) {
							ipad.push(result.countryCode.toLowerCase());
						}
						if(result.city) {
							ipad.push(result.city);
						}

						geocoder.geocode({'address': ipad.join(', ')}, function(results, status) {
							if(status == google.maps.GeocoderStatus.OK) {
								myLatLang = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
							}
						});
					}
				});

				myZoom = 7;
			<?php endif;?>

			var Fgeocoder = new google.maps.Geocoder();
			var Fmap = new google.maps.Map(document.getElementById('map_canvas_<?php echo $this->id;?>'), {
				'mapTypeId': google.maps.MapTypeId.ROADMAP,
				'panControl': false,
				'scaleControl':	false,
				'rotateControl': false,
				'streetViewControl': false,
				'overviewMapControl': false,
				'zoom': myZoom,
				'center': myLatLang
			});

			window.filtermap<?php echo $this->id; ?> = Fmap;
			google.maps.visualRefresh = true;

			var msize = new google.maps.Size(20,34);
			var mpoint = new google.maps.Point(10,34);
			var markerIcon = new google.maps.MarkerImage('<?php echo JUri::root();?>components/com_joomcck/fields/geo/markers/grouped/other/marker-big-blue.png', msize, new google.maps.Point(0,0), mpoint);

			var Fmarker = new google.maps.Marker({
				'map': Fmap,
				'cursor': 'move',
				'position': myLatLang,
				'draggable': true,
				'icon': markerIcon
			});


			//Add a Circle overlay to the map.
			var Fcircle = new google.maps.Circle({
				map: Fmap,
				radius: <?php echo ($radius * ($default->get('dist_type', 'km') == 'km' ? 1000 : 1));?>,
				fillColor: '#000000',
				fillOpacity: 0.2,
				strokeColor: '#000000',
				strokeOpacity: 0.5,
				strokeWeight: 1
			});
			Fcircle.bindTo('center', Fmarker, 'position');
			Fmap.fitBounds(Fcircle.getBounds());

			if($('#<?php echo $this->key ?>').length) {
				$('#<?php echo $this->key ?>').one('click', function (e) {
					setTimeout(function(){
						google.maps.event.trigger(Fmap, 'resize');
						Fmap.setCenter(Fmarker.getPosition());
						Fmap.fitBounds(Fcircle.getBounds());
						onCountryChange($('#filters<?php echo $this->key; ?>complete_limit').val(), resetMap);
					}, 100);
				});
			}

			var autocomplete = new google.maps.places.Autocomplete((document.getElementById('filter_ac')));
			autocomplete.setBounds(new google.maps.LatLngBounds(Fmap.getCenter(), Fmap.getCenter()));
			<?php if($default_country_code): ?>
			autocomplete.setComponentRestrictions({ 'country': '<?php echo $default_country_code; ?>' });
			<?php endif; ?>

			$('#filter_ac').bind('change keydown', function(e){
				if (e.keyCode == 13) {
					e.preventDefault();
				}
			});

			var places = new google.maps.places.PlacesService(Fmap);

			google.maps.event.addListener(autocomplete, 'place_changed', onPlaceChanged);

			google.maps.event.addListener(Fmap, 'click', function(event) {
				$('#cur_loc').removeAttr('disabled').css('background', '');
				Fmarker.setPosition(event.latLng);
				setHiddenPosition(event.latLng);
				setCircleRadius<?php echo $this->id  ?>();
			});

			google.maps.event.addListener(Fmap, 'zoom_changed', function(event) {
				$('#f<?php echo $this->id; ?>_position_zoom').val(Fmap.getZoom());
			});

			google.maps.event.addListener(Fmarker, 'dragend', function(event) {
				$('#cur_loc').removeAttr('disabled').css('background', '');
				setHiddenPosition(event.latLng);
				setCircleRadius<?php echo $this->id  ?>();
			});

			$('#filters<?php echo $this->key; ?>complete_limit').bind('change keyup', function(e){
				onCountryChange($(this).val());
			});

			onCountryChange($('#filters<?php echo $this->key; ?>complete_limit').val(), resetMap);

			function onCountryChange(country, next) {

				autocomplete.setBounds(Fmap.getBounds());

				if(typeof country == 'undefined') {
					return;
				}

				if (country.length == 2) {
					Fgeocoder.geocode({'address': 'country ' + country}, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							Fmarker.setPosition(results[0].geometry.location);
							setHiddenPosition(results[0].geometry.location);
							//setCircleRadius<?php echo $this->id  ?>();

							Fmap.fitBounds(results[0].geometry.bounds);
							autocomplete.setBounds(results[0].geometry.bounds);

							if ($.isFunction(next)) {
								next();
							}
						}
					});

					autocomplete.setComponentRestrictions({ 'country': country.toLowerCase() });
				} else {
					//autocomplete.setComponentRestrictions({'country': ''});
				}
			}

			function onPlaceChanged() {
				var place = autocomplete.getPlace();
				if (place.geometry) {
					Fmarker.setPosition(place.geometry.location);
					setHiddenPosition(place.geometry.location);
					setCircleRadius<?php echo $this->id  ?>();
				} else {
					document.getElementById('autocomplete').placeholder = 'Enter a city';
				}
			}

			$('#autocomplete').keypress(function(e){
				if ( e.which == 13 ) {
					return false;
				}
			});

		<?php endif;  ?>

		window.setCircleRadius<?php echo $this->id  ?> = function()
		{
			Fmarker.setVisible(true);
			Fcircle.setVisible(true);

			var value = parseInt($('#filters<?php echo $this->key;?>radius').val());

			if(!value) return;

			if($('#filters<?php echo $this->key;?>dist_type').val() == 'km') {
				value = value * 1000;
			}

			Fcircle.setOptions({
				fillColor: '#000000',
				fillOpacity: 0.3,
				strokeColor: '#000000',
				strokeOpacity: 0.8,
				strokeWeight: 2
			});
			Fcircle.setRadius(value);
			var bounds = Fcircle.getBounds();
			Fmap.fitBounds(bounds);

	 		$('#f<?php echo $this->id; ?>_bounds_sw_lng').val(bounds.getSouthWest().lng());
	 		$('#f<?php echo $this->id; ?>_bounds_sw_lat').val(bounds.getSouthWest().lat());
	 		$('#f<?php echo $this->id; ?>_bounds_ne_lng').val(bounds.getNorthEast().lng());
	 		$('#f<?php echo $this->id; ?>_bounds_ne_lat').val(bounds.getNorthEast().lat());


			Fgeocoder.geocode({'latLng': Fmap.getCenter()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					$('#f<?php echo $this->id; ?>_position_address').val(
						$('#filters<?php echo $this->key;?>dist_type').val() == 'm' ? results[0].formatted_address : results[0].formatted_address
					);
				}
			});

			//autocomplete.setBounds(Fmap.getBounds());

			resetMarker();
			resetCountry();
		}

		function setHiddenPosition(latlong)
		{
			$('#f<?php echo $this->id; ?>_position_lat').val(latlong.lat());
			$('#f<?php echo $this->id; ?>_position_lng').val(latlong.lng());
			$('#f<?php echo $this->id; ?>_position_zoom').val(Fmap.getZoom());
		}

		window.currpos<?php echo $this->id  ?> = function()
		{
			$('#cur_loc').attr('disabled', 'disabled').css('background', 'url("<?php echo JURI::root(TRUE);?>/media/mint/js/mooupload/imgs/load_bg_blue.gif")');
			Fgl.getCurrentPosition(displayPosition, displayError);
		}

		function displayPosition(position) {
			latlong = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			Fmarker.setPosition(latlong);
			Fmap.panTo(latlong);
			Fmap.setCenter(latlong);
			//Fmap.setZoom(15);
			setHiddenPosition(latlong);
			$('#cur_loc').removeAttr('disabled').css('background', '');
			setCircleRadius<?php echo $this->id  ?>();
		}

		function displayError(positionError) {
		 	alert('Browser not supported');
			$('#cur_loc').removeAttr('disabled').css('background', '');
			$('#cur_loc').css('display', 'none');
		}

		var Fgl;

		try {
			Fgl = navigator.geolocation;
		} catch(e) {}

		if (!Fgl) {
			$('#cur_loc').css('display', 'none');
		}

		// ------------- address filter -------------------
		window.getChilds = function(val, child)
		{
			var fid = '<?php echo $this->key ?>';

			resetMap();
			resetMarker();

			if(!child) return;

			if($('#' + child + fid) && val == 0)
			{
				$(child + fid).remove();
				return;
			}

			if (child == 'state' && $('#city' + fid) && $('#filters' + fid + 'country').val() == 0)
			{
				$('#city' + fid).remove();
				return;
			}

			$.ajax({
				url : '<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.field_call&tmpl=component', FALSE) ;?>',
				dataType: 'json',
				type: 'POST',
				data : {
					field_id : <?php echo $this->id; ?>,
					func : '_getChilds',
					value : val,
					country : ($('#filters' + fid + 'country') == undefined ? 0 : $('#filters' + fid + 'country').val()),
					index : child,
					section_id : <?php echo $section->id; ?>,
					ajax : true
				},
			}).done(function(json){
				if(json == undefined)
				{
					return;
				}

				if (!json.success)
				{
					alert(json.error);
					return;
				}
				if ($('#'+child + fid))
					$('#'+child + fid).remove();
				if (child == 'state' && $('#city' + fid))
					$('#city' + fid).remove();
				var sp = $(document.createElement('span')).attr({ id : child + fid });
				sp.html(json.result);
				$('#filter' + fid).append(sp);
				$('.select-geo').chosen({
					disable_search_threshold : 10,
					allow_single_deselect : true
				});
			});
		};


		window.filterMarker<?php echo $this->id  ?> = function(el, file)
		{
			$('img.active').removeClass('active');
			$(el).addClass('active');
			$('#f<?php echo $this->id; ?>_marker_name').val(file);
			resetMap();
			resetCountry();
		}

		function resetMarker()
		{
			if($('#f<?php echo $this->id; ?>_marker_name'))
			{
				$('#f<?php echo $this->id; ?>_marker_name').val('');
				$('#img.active').removeClass('active');
			}
		}

		function resetMap()
		{
			if($('#filters<?php echo $this->key;?>radius'))
			{
				$('#f<?php echo $this->id; ?>_bounds_sw_lng').val('');
	 			$('#f<?php echo $this->id; ?>_bounds_sw_lat').val('');
	 			$('#f<?php echo $this->id; ?>_bounds_ne_lng').val('');
	 			$('#f<?php echo $this->id; ?>_bounds_ne_lat').val('');
			}

			if(typeof Fcircle != 'undefined')
			{
				Fcircle.setOptions({
					fillColor: '#000000',
					fillOpacity: 0.2,
					strokeColor: '#000000',
					strokeOpacity: 0.5,
					strokeWeight: 1,
					center: myLatLang
				});
			}

			if(Fmap) {
				Fmap.fitBounds(Fcircle.getBounds());
			}
		}
		function resetCountry()
		{

			if($('#filters<?php echo $this->key;?>country'))
			{
				$('#filters<?php echo $this->key;?>country').val(0);
				if ($('#state<?php echo $this->key;?>'))
				{
					$('#state<?php echo $this->key;?>').remove();
				}
				if ($('#city<?php echo $this->key;?>'))
				{
					$('#city<?php echo $this->key;?>').remove();
				}
			}
			if ($('#filters<?php echo $this->key;?>state'))
			{
				$('#filters<?php echo $this->key;?>state').val(0);
				if ($('#city<?php echo $this->key;?>'))
				{
					$('#city<?php echo $this->key;?>').remove();
				}
			}
			if ($('#filters<?php echo $this->key;?>city'))
			{
				$('#city<?php echo $this->key;?>').val(0);
			}
		}
	}(jQuery));
</script>
