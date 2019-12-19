<?php
/*
# mod_sp_quickcontact - Ajax based quick contact Module by JoomShaper.com
# -----------------------------------------------------------------------
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2014 JoomShaper.com. All Rights Reserved.
# License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form class="uk-form sp-form">
	<div class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
		<div>
			<input class="uk-width-1-1" placeholder="John" type="text" name="name" onfocus="if (this.value=='<?php echo $name_text ?>') this.value='';" onblur="if (this.value=='') this.value='<?php echo $name_text ?>';" value="<?php echo $name_text ?>" required />
		</div>
		<div>
			<input type="email" placeholder="you@domain" class="uk-width-1-1" name="email" onfocus="if (this.value=='<?php echo $email_text ?>') this.value='';" onblur="if (this.value=='') this.value='<?php echo $email_text ?>';" value="<?php echo $email_text ?>" required />
		</div>
		<div>
			<input type="text" placeholder="Subject" class="uk-width-1-1" name="subject" onfocus="if (this.value=='<?php echo $subject_text ?>') this.value='';" onblur="if (this.value=='') this.value='<?php echo $subject_text ?>';" value="<?php echo $subject_text ?>" />
		</div>
		<div>
			<?php if($formcaptcha) { ?>
				<input type="text" class="uk-width-1-1" name="sccaptcha" placeholder="<?php echo $captcha_question ?>" required />
				<?php } ?>
			</div>
		</div>
		<div class="uk-grid uk-grid-width-1-1 uk-text-center" data-uk-grid-margin>
			<div>
				<textarea name="message" cols="100" rows="12" placeholder="Your message" class="uk-width-1-1" id="" onfocus="if (this.value=='<?php echo $msg_text ?>') this.value='';" onblur="if (this.value=='') this.value='<?php echo $msg_text ?>';" cols="" rows=""><?php echo $msg_text ?></textarea>
			</div>
			<div>
				<input id="sp_qc_submit" class="uk-button uk-button-large uk-button-primary uk-text-contrast" type="submit" value="<?php echo $send_msg ?>" />

			</div>
		</div>
	</form>
