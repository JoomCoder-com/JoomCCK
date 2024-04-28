<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$document = \Joomla\CMS\Factory::getDocument();
$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/fields/digits/assets/digits.js');
$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/fields/digits/assets/jquery.ui.slider.js');
$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/fields/digits/assets/css/style.css');

$data = $this->data;

$prep = $this->params->get('params.prepend', NULL);
$app = $this->params->get('params.append', NULL);

?>

<div style="margin: 20px;">
	<div id="digitslider<?php echo $this->key;?>"></div>
</div>

<div class="row">
    <div class="col">
        <label class=""><?php echo $this->params->get('params.label_min') ?> </label>
        <div class="input-group">
			<?php if($prep):?>
                <span class="input-group-text"><?php echo $prep;?></span>
			<?php endif; ?>
            <input type="text" name="filters[<?php echo $this->key;?>][min]" autocomplete="off" type="text" class="form-control" id="input_minmax_min<?php echo $this->id;?>" value="<?php echo $this->value->get('min', $data->min);?>">
			<?php if($app):?>
                <span class="input-group-text"><?php echo $app;?></span>
			<?php endif; ?>
        </div>
    </div>
    <div class="col">
        <label class=""><?php echo $this->params->get('params.label_max') ?></label>
        <div class="input-group">
			<?php if($prep):?>
                <span class="input-group-text"><?php echo $prep;?></span>
			<?php endif; ?>
            <input class="form-control" autocomplete="off" type="text" name="filters[<?php echo $this->key;?>][max]" id="input_minmax_max<?php echo $this->id;?>" value="<?php echo $this->value->get('max', $data->max);?>">
			<?php if($app):?>
                <span class="input-group-text"><?php echo $app;?></span>
			<?php endif; ?>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<br />
<div class="alert alert-danger" id="erralert<?php echo $this->id;?>"></div>

<script>
(function($){
	var sldr = $("#digitslider<?php echo $this->key;?>");
	var errbox = $('#erralert<?php echo $this->id;?>');

	var min = $('#input_minmax_min<?php echo $this->id;?>');
	var max = $('#input_minmax_max<?php echo $this->id;?>');

	errbox.hide();

	sldr.slider({
		range: true,
		min: <?php echo $data->min;?>,
		max: <?php echo $data->max;?>,
		step: <?php echo $this->params->get('params.steps', 1)  ?>,
		values: [ <?php echo $this->value->get('min', $data->min);?>, <?php echo $this->value->get('max', $data->max);?> ],
		slide: function( event, ui ) {
			min.val(ui.values[0]);
			max.val(ui.values[1]);
		}
	});

	min.on('keyup', function(){
		Joomcck.formatFloat(this, <?php echo $this->params->get('params.decimals_num', 0);?>, <?php echo $this->params->get('params.max_num', false);?>)
		var val = this.value;

		if(parseInt(val) > 0)
		{
			max.val(sldr.slider('values', 1));
		}
		else
		{
			max.val('');
		}

		if(parseInt(val) >= <?php echo $data->min;?> && parseInt(val) <= <?php echo $data->max;?>)
		{
			sldr.slider('values', 0, this.value);
		}

		if((parseInt(val) > parseInt(sldr.slider('values', 1))) && (parseInt(sldr.slider('values', 1)) > 0))
		{
			errbox.show().html('<?php echo \Joomla\CMS\Language\Text::sprintf('D_MINBIGEMAX', $data->min, $data->max, array('jsSafe' => true)) ?>');
		}
		else
		{
			if(parseInt(val) > <?php echo $data->max;?>)
			{
				errbox.show().html('<?php echo \Joomla\CMS\Language\Text::sprintf('D_MINTOBIG', $data->min, $data->max, array('jsSafe' => true)) ?>');
			}
			else
			{
				errbox.hide();
			}
		}
	});
	max.on('keyup', function(){
		Joomcck.formatFloat(this, <?php echo $this->params->get('params.decimals_num', 0);?>, <?php echo $this->params->get('params.max_num', false);?>)
		var val = this.value;

		if(parseInt(val) > 0)
		{
			min.val(sldr.slider('values', 0));
		}
		else
		{
			min.val('');
		}

		if(parseInt(val) >= <?php echo $data->min;?> && parseInt(val) <= <?php echo $data->max;?>)
		{
			sldr.slider('values', 1, this.value);
		}
		if(parseInt(val) < sldr.slider('values', 0))
		{
			errbox.show().html('<?php echo \Joomla\CMS\Language\Text::sprintf('D_MAXTOSMALL', $data->min, $data->max, array('jsSafe' => true)) ?>');
		}
		else
		{
			errbox.hide();
		}
	});

}(jQuery));
</script>