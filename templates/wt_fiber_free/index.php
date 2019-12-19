<?php
/**
* @package   Joomla Templates
* @author    WarpTheme http://www.warptheme.com
* @copyright Copyright (C) WarpTheme
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get warp
$warp = require(__DIR__.'/warp.php');

// load main theme file, located in /layouts/theme.php
echo $warp['template']->render('theme');
