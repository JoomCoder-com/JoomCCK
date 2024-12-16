<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;


$to = null;


switch ($params->get('params.to'))
{
	case 1 :
		if ($show_emailto && $this->value)
			$to = \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $this->value);
		break;
	case 2 :
		$to = Text::_('E_ADMIN') . ($show_emailto ? '(' . $app->getCfg('mailfrom') . ')' : '');
		break;
	case 3 :
		$to = $author->get('name') . ($show_emailto ? ' (' . $author->get('email') . ')' : '');
		break;
	case 4 :
		$to = '<input id="sendTo" type="text"  required="required" class="form-control" name="email[' . $this->id . '][email_to]" value="' . $data->get('email_to') . '" size="' . $params->get('params.size', 40) . '">';
		break;
	case 5 :
		if ($show_emailto)
			$to = \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $params->get('params.custom'));
		break;
}
$key = $record->id . $this->id;
?>
<?php if ($this->params->get('params.form_style', 1) == 2 || $this->params->get('params.form_style', 1) == 1): ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            parent.iframe_loaded(<?php echo $key;?>, jQuery('body').height());
            jQuery('#email_body<?php echo $key;?>').keyup(function () {
                parent.iframe_loaded(<?php echo $key;?>, jQuery('body').height());
            });
        })
    </script>
<?php endif; ?>
<style>
    html, body {
        width: 100% !important;
        max-width: 640px !important;
        padding: 0px 10px !important;
        margin: 0px !important;
        height: 100% !important;
    }
</style>
<br>
<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post"
      enctype="multipart/form-data">

	<?php if ($to): ?>
        <div class="mb-3">
            <label for="sendTo" class="form-label"><?php echo Text::_('E_SENDTO'); ?>:</label>
			<?php echo $to; ?>
        </div>
	<?php endif; ?>
	<?php if ($params->get('params.change_name_from', 1) || !$user->id): ?>
        <div class="mb-3">
            <label for="name" class="form-label"><?php echo Text::_('E_YOURNAME'); ?></label>
            <input id="name" required="required" class="form-control" type="text"
                   name="email[<?php echo $this->id; ?>][name]"
                   value="<?php echo $data->get('name', $user->get('name')); ?>"
                   size="<?php echo $params->get('params.size', 40); ?>"/>
        </div>
	<?php endif; ?>

	<?php if ($params->get('params.change_email_from', 1) || !$user->id): ?>
        <div class="mb-3">
            <label class="form-label" for="emailFrom"><?php echo Text::_('E_YOURMAIL'); ?></label>
            <input required="required" class="form-control" type="text"
                   name="email[<?php echo $this->id; ?>][email_from]"
                   id="emailFrom"
                   value="<?php echo $data->get('email_from', $user->get('email')); ?>"
                   size="<?php echo $params->get('params.size', 40); ?>"/>
        </div>
	<?php endif; ?>

	<?php if ($this->params->get('params.acemail')): ?>
        <div class="mb-3">

            <div class="form-check">
                <input name="email[<?php echo $this->id; ?>][subscr]" class="form-check-input" type="checkbox" value="1"
                       id="acym" checked>
                <label class="form-check-label" for="acym">
					<?php echo Text::_($this->params->get('params.acemail_text')); ?>
                </label>
            </div>

        </div>
	<?php endif; ?>

	<?php if ($params->get('params.cc')): ?>
        <div class="mb-3">
            <label for="emailCC" class="form-label"><?php echo Text::_('E_COPY'); ?></label>
            <input
                    class="form-control"
                    type="text"
                    id="emailCC"
                    name="email[<?php echo $this->id; ?>][cc]"
                    value="<?php echo $data->get('cc'); ?>"
                    size="<?php echo $params->get('params.size', 40); ?>"
            />
        </div>
	<?php endif; ?>

	<?php if ($params->get('params.subject_style', 0)): ?>
        <div class="mb-3">
            <label for="subject" class="form-label"><?php echo Text::_('E_SUBJ'); ?></label>
			<?php if ($params->get('params.subject_style', 0) == 2) :
				$pre_subject_values = explode("\n", trim($params->get('params.pre_subject_val')));
				if (count($pre_subject_values)):?>
                    <select id="subject" class="form-select" name="email[<?php echo $this->id; ?>][subject]">
						<?php foreach ($pre_subject_values as $value): ?>
                            <option value="<?php echo trim($value); ?>" <?php echo($value == $data->get('subject') ? 'selected="selected"' : ""); ?>><?php echo $value; ?></option>
						<?php endforeach; ?>
                    </select>
				<?php endif; ?>
			<?php else: ?>
                <input id="subject" required="required" class="form-control" type="text"
                       name="email[<?php echo $this->id; ?>][subject]"
                       value="<?php echo $data->get('subject', $params->get('params.subject')); ?>"
                       size="<?php echo $params->get('params.size', 40); ?>"/>
			<?php endif; ?>
        </div>

		<?php if ($params->get('params.show_body', 1)):

			\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(true) . '/components/com_joomcck/fields/textarea/assets/grow.js');
			$style = 'box-sizing: border-box;';
			$style .= 'max-height:' . $params->get('params.grow_max_height', 350) . 'px;';
			$style .= 'height:' . $params->get('params.grow_min_height', 50) . 'px;';
			?>
            <div class="mb-3">
                <label for="body" class="form-label"><?php echo Text::_('E_MSG'); ?></label>
                <textarea id="email_body<?php echo $this->id; ?>" style="<?php echo $style ?>"
                          name="email[<?php echo $this->id; ?>][body]"
                          class="form-control w-100"><?php echo $data->get('body', $params->get('params.body')); ?></textarea>

                <script type="text/javascript">
                    jQuery("#email_body<?php echo $this->id;?>").expanding();
                </script>
            </div>
		<?php endif; ?>

	<?php endif; ?>


	<?php

	// additional fields feature

	$schema = explode("\n", str_replace("\r", '', rtrim(trim($params->get('params.additional_fields', '')), ';')));
	ArrayHelper::clean_r($schema);

	if ($schema):
		foreach ($schema as $f):
			$field_info = explode('::', $f);

			if (count($field_info) > 2) :
				?>
                <div class="mb-3">
                <label class="form-label"><?php echo Text::_($field_info[2]); ?>:</label>
                <div>
				<?php
				switch (trim($field_info[0])) :
					case 'text' : ?>
                        <input type="<?php echo trim($field_info[0]); ?>"
                               name="email[<?php echo $this->id; ?>][add_field][<?php echo trim($field_info[1]); ?>]"
                               value="<?php echo trim($data->get('add_field.' . $field_info[1])); ?>"
                               style="width:99%" <?php echo @$field_info[4] ?> />
						<?php
						break;
					case 'radio' :
						if (isset($field_info[3])):
							$values = explode('|', $field_info[3]);
							foreach ($values as $val): ?>
                                <label for="" class="checkbox">
                                    <input type="<?php echo trim($field_info[0]); ?>"
                                           name="email[<?php echo $this->id; ?>][add_field][<?php echo trim($field_info[1]); ?>]" <?php echo trim($field_info[4]); ?>
                                           value="<?php echo trim(htmlentities($val, ENT_QUOTES, 'UTF-8')); ?>" <?php echo(($data->get('add_field.' . $field_info[1]) == $val) ? 'checked ' : ''); ?>> <?php echo $val; ?>
                                </label>
							<?php endforeach;
						else:?>
                            <label for="" class="checkbox">
                                <input type="<?php echo trim($field_info[0]); ?>"
                                       name="email[<?php echo $this->id; ?>][add_field][<?php echo trim($field_info[1]); ?>]" <?php echo trim($field_info[4]); ?>
                                       value="<?php echo trim(htmlentities($field_info[1], ENT_QUOTES, 'UTF-8')); ?>" <?php echo($data->get('add_field.' . $field_info[1]) ? ';?>ked' : ''); ?>> <?php echo $field_info[1]; ?>
                            </label>
						<?php endif;

						break;
					case 'checkbox' :
						if (isset($field_info[3])):
							$values = explode('|', $field_info[3]);
							foreach ($values as $val):?>

                                <label for="" class="checkbox">
                                    <input type="<?php echo trim($field_info[0]); ?>"
                                           name="email[<?php echo $this->id; ?>][add_field][<?php echo trim($field_info[1]); ?>][]" <?php echo @$field_info[4]; ?>
                                           value="<?php echo trim(htmlentities($val, ENT_QUOTES, 'UTF-8')); ?>" <?php echo(in_array($val, $data->get('add_field.' . $field_info[1], array())) ? 'checked ' : ''); ?>> <?php echo $val; ?>
                                </label>
							<?php endforeach;
						else: ?>
                            <label for="" class="checkbox">
                                <input type="<?php echo trim($field_info[0]); ?>"
                                       name="email[<?php echo $this->id; ?>][add_field][<?php echo trim($field_info[1]); ?>][]" <?php echo @$field_info[4]; ?>
                                       value="<?php echo trim(htmlentities($field_info[1], ENT_QUOTES, 'UTF-8')); ?>" <?php echo($data->get('add_field.' . $field_info[1]) ? 'checked' : ''); ?>> <?php echo $field_info[1]; ?>
                            </label>
						<?php endif;
						break;
					case 'select' :
						if (isset($field_info[3])):
							$values = explode('|', $field_info[3]); ?>
                            <select name="email[<?php echo $this->id; ?>][add_field][<?php echo $field_info[1]; ?>][]" <?php echo @$field_info[4]; ?>>
								<?php foreach ($values as $val): ?>
                                    <option <?php echo((in_array($val, $data->get('add_field.' . $field_info[1], array()))) ? 'selected=selected ' : ''); ?>><?php echo $val; ?></option>
								<?php endforeach; ?>
                            </select>
						<?php else: ?>
                            <input class="form-control" type="<?php echo trim($field_info[0]); ?>"
                                   name="email[<?php echo $this->id; ?>][add_field][<?php echo trim($field_info[1]); ?>][]" <?php echo @$field_info[4]; ?>
                                   value="<?php echo trim(htmlentities($field_info[1], ENT_QUOTES, 'UTF-8')); ?>" <?php echo($data->get('add_field.' . $field_info[1]) ? 'checked' : ''); ?>><?php echo $field_info[1]; ?>
						<?php endif;
						break;
					case 'textarea' :
						?>
                        <textarea class="form-control"
                                  name="email[<?php echo $this->id; ?>][add_field][<?php echo $field_info[1]; ?>]"><?php echo $data->get('add_field.' . $field_info[1]); ?></textarea>
						<?php
						break;
				endswitch;
			endif;
			?>
            </div>
            </div>
		<?php endforeach;
	endif; ?>


	<?php if ($params->get('params.attachment')): ?>
        <div class="mb-3">
            <label class="form-label" for="attachment"><?php echo Text::_('E_ATTACH'); ?>:</label>
            <div>
				<?php $num = $params->get('params.attach_num', 1);
				for ($i = 0; $i < $num; $i++):?>
                    <input id="attachment" type="file" name="email_<?php echo $this->id; ?>[]" class="form-control"/><br>
				<?php endfor; ?>
                <small class="text-muted"><?php echo Text::_('E_ALLOWEDEXT'); ?>: <?php echo $params->get('params.formats') ?></small>
            </div>
        </div>
	<?php endif; ?>

	<?php if ($params->get('params.copy_to_sender', 1)): ?>
        <div class="mb-3">
            <label class="form-label"><?php echo Text::_('E_SENDCOPY'); ?></label>
            <div>
				<?php
				$form = Form::getInstance("emailFormdefault", __DIR__ . "/default.xml", ['control' => 'email[' . $this->id . ']']);
				echo $form->getInput("copy_to_sender");
				?>

            </div>
        </div>
	<?php endif; ?>

	<?php if ($params->get('params.show_captcha', 1) && !$user->id): ?>
		<?php
		$joomcck_params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		$captcha        = Captcha::getInstance($joomcck_params->get('captcha', 'recaptcha'), array('namespace' => 'email'));
		?>
        <div class="mb-3">
            <label class="control-label"><?php echo Text::_('E_CAPTCHA'); ?></label>
            <div><?php echo $captcha->display('captcha', 'captcha'); ?></div>
        </div>
	<?php endif; ?>

    <div class="mb-3">
        <input type="submit" id="mailSubmit<?php echo $this->id; ?>" name="submit_<?php echo $this->id; ?>"
               value="<?php echo $params->get('params.button', Text::_('E_SEND')); ?>"
               class="btn btn-primary w-100"/>
    </div>


</form>
