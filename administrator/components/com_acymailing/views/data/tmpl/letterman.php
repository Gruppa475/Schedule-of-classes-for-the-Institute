<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.10
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php
	$resultUsers = acymailing_loadResult('SELECT count(*) FROM '.acymailing_table('letterman_subscribers',false));
	
	echo acymailing_translation_sprintf('USERS_IN_COMP',$resultUsers,'Letterman');
