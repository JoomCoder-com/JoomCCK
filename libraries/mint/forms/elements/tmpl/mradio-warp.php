<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 6/2/16
 * Time: 21:54
 */

$class     = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
$required  = $this->required ? ' required aria-required="true"' : '';
$autofocus = $this->autofocus ? ' autofocus' : '';
$disabled  = $this->disabled ? ' disabled' : '';
$readonly  = $this->readonly;
$condition = $this->getAttribute('condition', '[]');

$options = $this->getOptions();
?>

<style>
	.<?php echo $this->id ?> div.uk-button-group input[type=radio] {
		display: block;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
	}
</style>

<div class="<?php echo $this->id ?>">
	<div class="uk-button-group" data-uk-button-radio>
		<?php foreach($options as $i => $option): ?>
			<?php
			$checked = ((string)$option->value == (string)$this->value) ? ' checked="checked"' : '';
			$active  = ((string)$option->value == (string)$this->value) ? ' uk-active uk-primary uk-button-primary' : '';
			$class   = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			$disabled = !empty($option->disable) || ($readonly && !$checked);

			$disabled = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick  = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';
			$value    = htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8');
			?>

			<label for="<?php echo $this->id . $i ?>" id="btn-<?php echo $this->id . $i; ?>" type="button" class="uk-button <?php echo $active ?>">
				<?php echo JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) ?>
                <input style="display: none" type="radio" id="<?php echo $this->id . $i ?>" name="<?php echo $this->name ?>" value="<?php echo $value ?>" <?php echo $checked . $class . $required . $onclick . $onchange . $disabled ?>>
			</label>
		<?php endforeach; ?>
	</div>
</div>

<script>
	(function($) {
		var btns = $('.<?php echo $this->id ?> label');

		btns.bind('click', function() {
			setTimeout(function() {
				btns.removeClass('uk-button-primary');
				$.each(btns, function() {
					if($(this).hasClass('uk-active')) {
						$(this).addClass('uk-button-primary');
					}
				});
			}, 100);
        });
        var btns = $('.<?php echo $this->id ?> label');
		var inputs = $('.<?php echo $this->id ?> label input');
        var conditions = JSON.parse('<?php echo $condition ?: '[]'; ?>');
        $.each(btns, function(key, val){
			var btn = $(val);
            var input = $('input', btn);
            var input_val = input.val();
			if(input.is(':checked')) {
                btn.removeClass();
                btn.addClass('uk-button uk-active ' + (input_val == 0 ? 'uk-button-danger' : 'uk-button-success'));
			} else {
				btn.removeClass();
				btn.addClass('uk-button');
			}
			btn.click(function(){
				btns.removeClass();
				btns.addClass('uk-button');
				btn.addClass('uk-active ' + (input_val == 0 ? 'uk-button-danger' : 'uk-button-success'));
				inputs.removeAttr('checked', 'checked');
                input.attr('checked', 'checked');
                update_view(conditions, input_val);
			});
        });
        
        function update_view(c, v) {
            $.each(c, function(index, element){
                console.log(element, parseInt(v) == 0);
                if(parseInt(v) == 0){
                    $('#tr_' + element).hide() 
                } else { 
                    $('#tr_' + element).show()
                };
            })
        }
	}(jQuery));
</script>