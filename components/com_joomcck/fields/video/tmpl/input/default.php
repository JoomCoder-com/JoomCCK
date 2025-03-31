<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

?>
<style>
<!--
.link_delete {
	cursor: pointer;
	<?php if($this->only_one):?>
	display:none;
	<?php endif;?>
	position:absolute;
	top: 4px;
	right: 4px;
}
.element-box {
}
.element-box textarea, .element-box input {
	margin-bottom: 15px;
	width: 98%;
}
.video-title {
	cursor: pointer;
	margin-bottom:10px;
	font-size: 16px;
}
-->
</style>
<div id="video-field" class="accordion">
	<?php if($this->only_one):?>
		<p class="small"><?php echo \Joomla\CMS\Language\Text::_('CONLYONE')?></p>
	<?php endif;?>
	<?php if($this->upload):?>
		<div class="accordion-item">
            <h2 class="accordion-header">
                <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#upload-pan<?php echo $this->id ?>">
                    <i class="fas fa-upload pe-2"></i> <?php echo \Joomla\CMS\Language\Text::_('CUPLOAD')?>
                </button>
            </h2>

            <div id="upload-pan<?php echo $this->id ?>" class="accordion-collapse collapse show video-pan-<?php echo $this->id; ?>">
                <div class="accordion-body">
					<?php echo $this->upload;?>
                </div>
            </div>
        </div>
	<?php endif;?>

	<?php if(in_array($this->params->get('params.embed', 1), $this->user->getAuthorisedViewLevels())): ?>
		<div class="accordion-item">
            <h2 class="accordion-header">
                <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#embed-pan<?php echo $this->id ?>">
                    <i class="fas fa-code pe-2"></i> <?php echo \Joomla\CMS\Language\Text::_('CEMBED')?>
                </button>
            </h2>
            <div id="embed-pan<?php echo $this->id ?>" class="accordion-collapse collapse fade video-pan-<?php echo $this->id; ?>">
                <div class="accordion-body">
                    <div id="input_embeds">
						<?php foreach ($this->embed AS $embed):?>
                            <div class="element-box">
							<textarea class="form-control" style="" name="jform[fields][<?php echo $this->id; ?>][embed][]" cols="50" rows="5"
                                      id="<?php echo $this->formControl.$this->name;?>" ><?php echo $embed;?></textarea>
                                <img align="absmiddle" src="<?php echo Uri::root(TRUE)?>/media/com_joomcck/icons/16/cross-button.png"
                                     class="link_delete" onclick="Joomcck.deleteFormElement<?php echo $this->id; ?>('embed', this);">
                            </div>
						<?php endforeach;?>
                    </div>

					<?php if(!$this->only_one):?>
                        <div id="embed-button">
                            <button class="btn btn-light border" type="button" onclick="Joomcck.addFormElement<?php echo $this->id; ?>('embed', <?php echo $this->id; ?>);">
                                <img src="<?php echo Uri::root(TRUE); ?>/media/com_joomcck/icons/16/plus-button.png" align="absmiddle">
								<?php echo \Joomla\CMS\Language\Text::_('F_ADDEMBEDE'); ?>
                            </button>
                        </div>
					<?php endif;?>
                </div>
            </div>
        </div>
	<?php endif;?>

	<?php if(in_array($this->params->get('params.link', 1), $this->user->getAuthorisedViewLevels())): ?>
		<div class="accordion-item">

            <h2 class="accordion-header">
                <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#link-pan<?php echo $this->id ?>">
                    <i class="fas fa-link pe-2"></i> <?php echo \Joomla\CMS\Language\Text::_('CLINK')?>
                </button>
            </h2>

            <div id="link-pan<?php echo $this->id ?>" class="accordion-collapse video-pan-<?php echo $this->id; ?> collapse fade">
                <div class="accordion-body">
                    <p><?php echo \Joomla\CMS\Language\Text::_('WEUNDERSTAND');?>:
						<?php foreach ($this->params->get('params.adapters', array()) as $adapter):?>
                            <img align="absmiddle" src="<?php echo Uri::root(TRUE); ?>/components/com_joomcck/fields/video/adapters/icons/<?php echo $adapter;?>.png"
                                 alt="<?php echo ucfirst($adapter); ?>" title="<?php echo ucfirst($adapter); ?>" />
						<?php endforeach;?>
						<?php echo \Joomla\CMS\Language\Text::_('WEUNDERSTAND2');?>
                    </p>

                    <div id="input_links" class="mb-3">
						<?php foreach ($this->link AS $link):?>
                            <div class="element-box input-group mb-2">
                                <input
                                        class="form-control m-0"
                                        name="jform[fields][<?php echo $this->id;?>][link][]"
                                        type="text"
                                        value="<?php echo $link; ?>"
                                        id="<?php echo $this->formControl.$this->name;?>"
                                />
                                <?php if(!$this->only_one): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="Joomcck.deleteFormElement<?php echo $this->id; ?>('link', this);">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                            </div>
						<?php endforeach;?>
                    </div>

					<?php if(!$this->only_one):?>
                        <div id="link-button">
                            <button class="btn btn-light border" type="button" onclick="Joomcck.addFormElement<?php echo $this->id; ?>('link', <?php echo $this->id; ?>);">
                                <i class="fas fa-plus"></i> <?php echo \Joomla\CMS\Language\Text::_('F_ONEMOREVIDEO'); ?>
                            </button>
                        </div>
					<?php endif;?>
                </div>
            </div>
        </div>
	<?php endif;?>
</div>

<script type='text/javascript'>
!function($)
{
	//$('.video-pan-<?php echo $this->id; ?>').first().collapse('show');

	lnk_count = <?php echo (int)count($this->link);?>;
	emb_count = <?php echo (int)count($this->embed);?>;

	Joomcck.addFormElement<?php echo $this->id; ?> = function (type, id)
	{


		if(type == 'embed')
		{
			<?php if($this->params->get('params.embed_max_count', 0)): ?>
				if(emb_count >= <?php echo $this->params->get('params.embed_max_count', 0);?>)
				{
					alert('<?php echo \Joomla\CMS\Language\Text::sprintf('CMAXCOUNTEMBED', $this->params->get('params.embed_max_count', 0));?>');
					return false;
				}
			<?php endif;?>
			central_div = 'input_embeds';
			// item_div = 'embed_div element-box';
			input = 'textarea';
			// btn_id = 'embed-button';
			emb_count++;
		}
		else if(type == 'link')
		{
			<?php if($this->params->get('params.link_max_count', 0)): ?>
				if(lnk_count >= <?php echo $this->params->get('params.link_max_count', 0);?>)
				{
					alert('<?php echo \Joomla\CMS\Language\Text::sprintf('CMAXCOUNTLINKS', $this->params->get('params.link_max_count', 0));?>');
					return false;
				}
			<?php endif;?>
			central_div = 'input_links';
			// item_div = 'link_div element-box';
			input = 'input';
			// btn_id = 'link-button';
			lnk_count++;
		}
		else
		{
			return;
		}

        // Fixed section for the embed element creation
        if(type == 'link'){
            var input_div = $(document.createElement("div")).attr({
                'class': 'element-box input-group mb-2'
            });

            var input = $(document.createElement(input)).attr({
                type: "text",
                name: 'jform[fields][' + id + '][' + type + '][]',
                id: '<?php echo $this->formControl.$this->name;?>',
                class: 'form-control m-0'
            }).appendTo(input_div);

            var close_link = $(document.createElement("button")).attr({
                'class': 'btn btn-outline-danger',
                'type' : 'button'
            }).html("<i class='fas fa-times'></i>").appendTo(input_div);

            close_link.on('click', function(){
                Joomcck.deleteFormElement<?php echo $this->id; ?>(type, this);
            });
        }
        else{
            var input_div = $(document.createElement("div")).attr({
                'class': 'element-box'
            });

            var input = $(document.createElement(input)).attr({
                name: 'jform[fields][' + id + '][' + type + '][]',
                id: '<?php echo $this->formControl.$this->name;?>',
                cols: 50,
                rows: 5,
                class: 'form-control'
            }).appendTo(input_div);

            var close_link = $(document.createElement("img")).attr({
                'class': 'link_delete',
                'src': '<?php echo Uri::root(TRUE)?>/media/com_joomcck/icons/16/cross-button.png'
            }).appendTo(input_div);

            close_link.on('click', function(){
                Joomcck.deleteFormElement<?php echo $this->id; ?>(type, this);
            });
        }



		$("#"+central_div).append(input_div);

	}

	Joomcck.deleteFormElement<?php echo $this->id; ?> = function (type, second)
	{
		if(type == 'embed')
		{
			emb_count--;
		}
		else if(type == 'link')
		{
			lnk_count--;
		}
		console.log(second);
		$(second).parent('div.element-box').remove();
		//el.remove();
	}
}(jQuery);
</script>