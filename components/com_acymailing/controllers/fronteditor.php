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

$currentUserid = acymailing_currentUserId();
if(empty($currentUserid)){
    acymailing_askLog();
    return false;
}

include(ACYMAILING_BACK.'controllers'.DS.'editor.php');

class FronteditorController extends EditorController{

}
