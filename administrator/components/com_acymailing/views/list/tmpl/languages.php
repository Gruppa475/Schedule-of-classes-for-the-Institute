<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.10
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div class="<?php echo acymailing_isAdmin() ? 'acyblockoptions' : 'onelineblockoptions'; ?>">
	<span class="acyblocktitle"><?php echo acymailing_translation('LANGUAGES'); ?></span>
	<?php echo $this->languages->display('languages', $this->list->languages); ?>
</div>
