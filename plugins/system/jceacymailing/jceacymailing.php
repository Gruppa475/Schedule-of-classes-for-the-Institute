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

class plgSystemJceacymailing extends JPlugin{
	function onBeforeWfEditorRender(&$settings) {
		if(empty($_REQUEST['option']) || $_REQUEST['option'] != 'com_acymailing') return;

		if(!empty($_REQUEST['acycssfile'])) $settings['content_css'] = $_REQUEST['acycssfile'];
	}
}
