var pass = jQuery('#field_pass_<?php echo $this->id;?>').val();
var pass2 = jQuery('#field_pass_2<?php echo $this->id;?>').val();

var pass3 = jQuery('#field_pass_3<?php echo $this->id;?>').val();

if(pass3) {
 <?php if(!$this->params->get('params.field_id_email')):?>
 if(!jQuery('#field_login_<?php echo $this->id;?>').val()) {
	hfid.push(<?php echo $this->id;?>); 
	isValid = false;
	errorText.push('<?php echo addslashes(JText::_('CR_LOGINREQUIRED'));?>');
 }
 <?php endif;?>
} else if(pass && pass2){
 if(pass != pass2) {
	hfid.push(<?php echo $this->id;?>); 
	isValid = false;
	errorText.push('<?php echo addslashes(JText::_('CR_PASSDOESNOTMATCH'));?>');
 }
 <?php if($this->params->get('params.loginname')):?>
 if(!jQuery('#field_username_<?php echo $this->id;?>').val()) {
	hfid.push(<?php echo $this->id;?>); 
	isValid = false;
	errorText.push('<?php echo addslashes(JText::_('CR_USERNAMEREQUIRED'));?>');
 }
 <?php endif;?>

<?php if(!$this->params->get('params.field_id_email')):?>
 if(!jQuery('#field_email_<?php echo $this->id;?>').val()) {
	hfid.push(<?php echo $this->id;?>); 
	isValid = false;
	errorText.push('<?php echo addslashes(JText::_('CR_EMAILREQUIRED'));?>');
 }
<?php endif;?>
} else {
	hfid.push(<?php echo $this->id;?>); 
	isValid = false;
	errorText.push('<?php echo addslashes(JText::_('CR_REGLOGINREQUIRED'));?>');
}