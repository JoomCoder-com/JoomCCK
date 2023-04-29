<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;


$to = null;




switch ($params->get('params.to'))
{
	case 1 :
		if ($show_emailto && $this->value)
			$to = JHtml::_('content.prepare', $this->value);
		break;
	case 2 :
		$to = JText::_('E_ADMIN') . ($show_emailto ? '(' . $app->getCfg('mailfrom') . ')' : '');
		break;
	case 3 :
		$to = $author->get('name') . ($show_emailto ? ' (' . $author->get('email') . ')' : '');
		break;
	case 4 :
		$to = '<input type="text" style="width:99%" required="required" class="form-control" name="email['.$this->id.'][email_to]" value="'.$data->get('email_to').'" size="'.$params->get('params.size', 40).'">';
		break;
	case 5 :
		if ($show_emailto)
			$to = JHtml::_('content.prepare', $params->get('params.custom'));
		break;
}
$key = $record->id.$this->id;
?>
<?php if($this->params->get('params.form_style', 1) == 2 || $this->params->get('params.form_style', 1) == 1): ?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			parent.iframe_loaded(<?php echo $key;?>, jQuery('body').height());
			jQuery('#email_body<?php echo $key;?>').keyup(function(){
				parent.iframe_loaded(<?php echo $key;?>,jQuery('body').height());
			});
		})
	</script>
<?php endif; ?>
<br>
<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString();?>" method="post" enctype="multipart/form-data">
	<table class="table table-hover">
		<?php if ($to):?>
		<tr>
			<td width="1%" nowrap="nowrap" ><?php echo JText::_('E_SENDTO');?>:</td>
			<td><?php echo $to;?></td>
		</tr>
		<?php endif; ?>
		<?php if ($params->get('params.change_name_from', 1) || !$user->id):?>
		<tr>
			<td width="1%" nowrap="nowrap"><?php echo JText::_('E_YOURNAME');?></td>
			<td><input required="required" class="form-control" type="text" name="email[<?php echo $this->id;?>][name]"  value="<?php echo $data->get('name', $user->get('name'));?>"
				size="<?php echo $params->get('params.size', 40);?>" /></td>
		</tr>
		<?php endif; ?>

		<?php if ($params->get('params.change_email_from', 1) || !$user->id):?>
		<tr>
			<td width="1%" nowrap="nowrap"><?php echo JText::_('E_YOURMAIL');?></td>
			<td><input required="required" class="form-control" type="text" name="email[<?php echo $this->id;?>][email_from]"  value="<?php echo $data->get('email_from', $user->get('email'));?>"
				size="<?php echo $params->get('params.size', 40);?>" /></td>
		</tr>
		<?php endif; ?>

		<?php if($this->params->get('params.acemail')):	?>
			<tr>
				<td width="1%" nowrap="nowrap"></td>
				<td>
					<label class="checkbox">
						<input required="required" class="form-control" type="checkbox" name="email[<?php echo $this->id;?>][subscr]" value="1" checked>
						<?php echo JText::_($this->params->get('params.acemail_text')); ?>
					</label>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($params->get('params.cc')):?>
		<tr>
			<td width="1%" nowrap="nowrap"><?php echo JText::_('E_COPY');?></td>
			<td><input class="form-control" type="text" name="email[<?php echo $this->id;?>][cc]"  value="<?php echo $data->get('cc');?>"
				size="<?php echo $params->get('params.size', 40);?>" /></td>
		</tr>
		<?php endif; ?>

		<?php if ($params->get('params.subject_style', 0)):?>
			<tr>
				<td width="1%" nowrap="nowrap"><?php echo JText::_('E_SUBJ');?></td>
				<td>
					<?php if ($params->get('params.subject_style', 0) == 2) :
						$pre_subject_values = explode("\n", trim($params->get('params.pre_subject_val')));
						if (count($pre_subject_values)):?>
							<select class="form-control" name="email[<?php echo $this->id;?>][subject]" >
							<?php foreach($pre_subject_values as $value): ?>
								<option value="<?php echo trim($value);?>" <?php echo ($value == $data->get('subject') ? 'selected="selected"' : "");?>><?php echo $value;?></option>
							<?php endforeach; ?>
							</select>
						<?php endif;?>
					<?php else: ?>
						<input required="required" class="form-control" type="text" name="email[<?php echo $this->id;?>][subject]"  value="<?php echo $data->get('subject', $params->get('params.subject'));?>"
						size="<?php echo $params->get('params.size', 40);?>" />
					<?php endif;?>
				</td>
			</tr>
		<?php endif;?>

		<?php if ($params->get('params.show_body', 1)):

			JFactory::getDocument()->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/textarea/assets/grow.js');
			$style = 'box-sizing: border-box;';
			$style .= 'max-height:' . $params->get('params.grow_max_height', 350) . 'px;';
			$style .= 'height:' . $params->get('params.grow_min_height', 50) . 'px;';
		?>
		<tr>
			<td width="1%" nowrap="nowrap"><?php echo JText::_('E_MSG');?></td>
			<td>
				<textarea id="email_body<?php echo $this->id;?>" style="<?php echo $style ?>" name="email[<?php echo $this->id;?>][body]" class="form-control"><?php echo $data->get('body', $params->get('params.body'));?></textarea>

                    <script type="text/javascript">
                        jQuery("#field_<?php echo $this->id;?>").expanding();
                    </script>
			</td>
		</tr>
		<?php endif; ?>

		<?php
		$schema = explode("\n", str_replace("\r", '', rtrim(trim($params->get('params.additional_fields', '')), ';')));
		ArrayHelper::clean_r($schema);

		if ($schema):
			foreach($schema as $f):
				$field_info = explode('::', $f);

				if (count($field_info) > 2) :
					?>
					<tr>
						<td><?php echo JText::_($field_info[2]);?>:</td>
						<td>
						<?php
						switch (trim($field_info[0])) :
							case 'text' : ?>
							<input type="<?php echo trim($field_info[0]);?>"
										name="email[<?php echo $this->id;?>][add_field][<?php echo trim($field_info[1]);?>]"
										value="<?php echo trim($data->get('add_field.' . $field_info[1]));?>"
										style="width:99%" <?php echo @$field_info[4] ?> />
							 <?php
								break;
							case 'radio' :
								if (isset($field_info[3])):
									$values = explode('|', $field_info[3]);
									foreach($values as $val): ?>
									<label for="" class="checkbox">
										<input type="<?php echo trim($field_info[0]);?>"
											name="email[<?php echo $this->id;?>][add_field][<?php echo trim($field_info[1]);?>]" <?php echo trim($field_info[4]);?>
											value="<?php echo trim(htmlentities($val, ENT_QUOTES, 'UTF-8'));?>" <?php echo (($data->get('add_field.' . $field_info[1]) == $val) ? 'checked ' : '');?>> <?php echo $val;?>
									</label>
									<?php endforeach;
								else:?>
									<label for="" class="checkbox">
									<input type="<?php echo trim($field_info[0]);?>"
											name="email[<?php echo $this->id;?>][add_field][<?php echo  trim($field_info[1]);?>]" <?php echo trim($field_info[4]);?>
											value="<?php echo trim(htmlentities($field_info[1], ENT_QUOTES, 'UTF-8'));?>" <?php echo ($data->get('add_field.' . $field_info[1]) ? ';?>ked' : '');?>> <?php echo $field_info[1];?>
									</label>
								<?php endif;

								break;
							case 'checkbox' :
								if (isset($field_info[3])):
									$values = explode('|', $field_info[3]);
									foreach($values as $val):?>

									<label for="" class="checkbox">
										<input type="<?php echo trim($field_info[0]);?>"
											name="email[<?php echo $this->id;?>][add_field][<?php echo trim($field_info[1]);?>][]" <?php echo @$field_info[4];?>
											value="<?php echo trim(htmlentities($val, ENT_QUOTES, 'UTF-8'));?>" <?php echo (in_array($val, $data->get('add_field.'.$field_info[1], array())) ? 'checked ' : '');?>> <?php echo $val;?>
									</label>
									<?php endforeach;
								else: ?>
									<label for="" class="checkbox">
										<input type="<?php echo trim($field_info[0]);?>"
												name="email[<?php echo $this->id;?>][add_field][<?php echo trim($field_info[1]);?>][]" <?php echo @$field_info[4];?>
												value="<?php echo trim(htmlentities($field_info[1], ENT_QUOTES, 'UTF-8'));?>" <?php echo ($data->get('add_field.' . $field_info[1]) ? 'checked' : '');?>> <?php echo $field_info[1];?>
									</label>
								<?php endif;
								break;
							case 'select' :
								if (isset($field_info[3])):
									$values = explode('|', $field_info[3]);?>
									<select name="email[<?php echo $this->id;?>][add_field][<?php echo $field_info[1];?>][]" <?php echo @$field_info[4];?>>
									<?php foreach($values as $val):?>
										<option <?php echo ((in_array($val, $data->get('add_field.' . $field_info[1], array()))) ? 'selected=selected ' : '');?>><?php echo $val;?></option>
									<?php endforeach;?>
									</select>
								<?php else:?>
									<input class="form-control" type="<?php echo trim($field_info[0]);?>"
											name="email[<?php echo $this->id;?>][add_field][<?php echo trim($field_info[1]);?>][]" <?php echo @$field_info[4];?>
											value="<?php echo trim(htmlentities($field_info[1], ENT_QUOTES, 'UTF-8'));?>" <?php echo ($data->get('add_field.' . $field_info[1]) ? 'checked' : '');?>><?php echo $field_info[1];?>
								<?php endif;
								break;
							case 'textarea' :?>
									<textarea class="form-control" name="email[<?php echo $this->id;?>][add_field][<?php echo $field_info[1];?>]"><?php echo $data->get('add_field.' . $field_info[1]);?></textarea>
									<?php
								break;
						endswitch;
					endif;
					?>
					</td>
				</tr>
			<?php endforeach;
		endif;?>

		<?php if ($params->get('params.attachment')): ?>
		<tr>
			<td><?php echo JText::_('E_ATTACH');?>:</td>
			<td>
				<?php $num = $params->get('params.attach_num', 1);
				for ($i = 0;$i < $num; $i++):?>
				<input type="file" name="email_<?php echo $this->id;?>[]" class="btn"/><br>
				<?php endfor;?>
				<span class="small"><?php echo JText::_('E_ALLOWEDEXT');?>: <?php echo $params->get('params.formats')?></span>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($params->get('params.copy_to_sender', 1)): ?>
		<tr valign="top">
			<td><?php echo JText::_('E_SENDCOPY');?></td>
			<td>
				<?php
				    $form = Form::getInstance("emailFormdefault", __DIR__ . "/default.xml",['control' => 'email['.$this->id.']']);
                    echo $form->getInput("copy_to_sender");
				?>
				<script type="text/javascript">
				if (typeof(Joomcck.redrawBS) != "undefined") {
					Joomcck.redrawBS();
				}else{
					emailRedrawBS();
				}
				</script>
			</td>
		</tr>
		<?php endif;?>

		<?php if ($params->get('params.show_captcha', 1) && !$user->id): ?>
			<?php
			$joomcck_params = JComponentHelper::getParams('com_joomcck');
			$captcha = Captcha::getInstance($joomcck_params->get('captcha', 'recaptcha'), array('namespace' => 'email'));
			?>
			<tr valign="top">
				<td><?php echo JText::_('E_CAPTCHA');?></td>
				<td><?php echo $captcha->display('captcha', 'captcha');?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<td></td>
			<td><input type="submit" id="mailSubmit<?php echo $this->id;?>" name="submit_<?php echo $this->id;?>" value="<?php echo $params->get('params.button', JText::_('E_SEND'));?>" class="btn btn-primary" />
			</td>
		</tr>

	</table>
</form>
