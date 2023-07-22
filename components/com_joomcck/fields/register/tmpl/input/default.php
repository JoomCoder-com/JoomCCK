<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$default = new JRegistry($this->value);
$user_params = JComponentHelper::getParams('com_users');



?>


<div class="row g-4" id="regfiled">
	<div class="col-md-<?php echo $this->params->get('params.loginform') ? 6 : 12 ?>">
		<h4><?php echo JText::_('COM_USERS_REGISTRATION')?></h4>
		<div class="border rounded p-3">
			<?php if($this->params->get('params.name')):?>
			<label><?php echo JText::_('COM_USERS_REGISTER_NAME_LABEL')?></label>
			<input class="form-control" type="text" name="jform[fields][<?php echo $this->id; ?>][name]" id="field_name_<?php echo $this->id; ?>" value="<?php echo $default->get('name')?>">
			<?php endif;?>

			<?php if($this->params->get('params.loginname')):?>
			<label><?php echo JText::_('COM_USERS_REGISTER_USERNAME_LABEL')?></label>
			<input class="form-control" type="text" name="jform[fields][<?php echo $this->id; ?>][username]" id="field_username_<?php echo $this->id; ?>" value="<?php echo $default->get('username')?>">
			<?php endif;?>

			<?php if(!$this->params->get('params.field_id_email')):?>
			<label><?php echo JText::_('COM_USERS_REGISTER_EMAIL1_LABEL')?></label>
			<input class="form-control" type="text" name="jform[fields][<?php echo $this->id; ?>][email]" id="field_email_<?php echo $this->id; ?>" value="<?php echo $default->get('email')?>">
			<?php endif;?>

			<?php if($this->group):?>
			<label><?php echo JText::_('CR_GROUP')?></label>
			<?php echo $this->group;?>
			<?php endif;?>

			<label><?php echo JText::_('COM_USERS_PROFILE_PASSWORD1_LABEL')?></label>
			<input class="form-control" type="password" name="jform[fields][<?php echo $this->id; ?>][pass]" id="field_pass_<?php echo $this->id; ?>" value="<?php echo $default->get('pass')?>">
			<div class="text-muted">
				<?php if($user_params->get('minimum_integers')): ?>
					<?php echo JText::plural('CR_PASS_INT_N', $user_params->get('minimum_integers')); ?>
				<?php endif;?>
				<?php if($user_params->get('minimum_symbols')): ?>
					<?php echo JText::plural('CR_PASS_SYN_N', $user_params->get('minimum_symbols')); ?>
				<?php endif;?>
				<?php if($user_params->get('minimum_uppercase')): ?>
					<?php echo JText::plural('CR_PASS_UC_N', $user_params->get('minimum_uppercase')); ?>
				<?php endif;?>
				<?php if($user_params->get('minimum_length')): ?>
					<?php echo JText::plural('CR_PASS_LEN_N', $user_params->get('minimum_length')); ?>
				<?php endif;?>
			</div>

			<label><?php echo JText::_('COM_USERS_PROFILE_PASSWORD2_LABEL')?></label>
			<input class="form-control" type="password" name="jform[fields][<?php echo $this->id; ?>][pass2]" id="field_pass_2<?php echo $this->id; ?>" value="<?php echo $default->get('pass2')?>">
		</div>
	</div>
	<?php if($this->params->get('params.loginform')):?>
		<div class="col-md-6">
			<h4><?php echo JText::_('JLOGIN')?></h4>
			<div class="border rounded p-3">
				<?php if(!$this->params->get('params.field_id_email')):?>
				<label><?php echo JText::_('CR_LOGIN')?></label>
				<input class="form-control" type="text" name="jform[fields][<?php echo $this->id; ?>][login]" id="field_login_<?php echo $this->id; ?>" value="<?php echo $default->get('login')?>">
				<?php else:?>
					<p><?php echo JText::sprintf('CR_EMAILASLOGIN', '<span class="label" id="regloginemail'.$this->id.'"></span>')?></p>
				<?php endif;?>

				<label><?php echo JText::_('COM_USERS_PROFILE_PASSWORD1_LABEL')?></label>
				<input class="form-control" type="password" name="jform[fields][<?php echo $this->id; ?>][pass3]" id="field_pass_3<?php echo $this->id; ?>" value="<?php echo $default->get('pass3')?>">
			</div>
		</div>
	<?php endif;?>
</div>
<?php if($this->params->get('params.field_id_email')):?>
<script>
(function($){
	$('#field_<?php echo $this->params->get('params.field_id_email')?>').bind('blur', function(){
		$('#regloginemail<?php echo $this->id?>').html($(this).val());
	});
}(jQuery))
</script>
<?php endif;?>