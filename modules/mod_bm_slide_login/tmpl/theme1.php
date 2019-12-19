<?php
/**
 * @package     Brainymore.com
 * @subpackage  mod_bm_slide_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form"  class="form-1">
	<p class="field">
		<input id="modlgn-username" type="text" name="username" placeholder="<?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?>" />
		<i class="icon-user icon-large"></i>
	</p>
		<p class="field">
			<input id="modlgn-passwd" type="password" name="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
			<i class="icon-lock icon-large"></i>
	</p>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<div id="form-login-remember" class="control-group checkbox">
		<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_BM_SLIDE_LOGIN_REMEMBER_ME') ?></label>
	</div>
	<?php endif; ?>
	<div id="form-login-submit" class="control-group">
		<div class="controls">
			<button type="submit" tabindex="0" name="Submit" class="btn-submit"><?php echo JText::_('JLOGIN') ?></button>
		</div>
	</div>
	<?php
		$usersConfig = JComponentHelper::getParams('com_users'); ?>
		<ul class="unstyled">
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid='.UsersHelperRoute::getRegistrationRoute()); ?>">
				<?php echo JText::_('MOD_BM_SLIDE_LOGIN_REGISTER'); ?> <span class="icon-arrow-right"></span></a>
			</li>
		<?php endif; ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind&Itemid='.UsersHelperRoute::getRemindRoute()); ?>">
				<?php echo JText::_('MOD_BM_SLIDE_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
			</li>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid='.UsersHelperRoute::getResetRoute()); ?>">
				<?php echo JText::_('MOD_BM_SLIDE_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
			</li>
		</ul>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>		
