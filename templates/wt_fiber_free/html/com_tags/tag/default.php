<?php
/**
* @package   Joomla Templates
* @author    WarpTheme http://www.warptheme.com
* @copyright Copyright (C) WarpTheme
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// include config and layout
$base = dirname(dirname(dirname(__FILE__)));
include($base.'/config.php');
include($warp['path']->path('layouts:'.preg_replace('/'.preg_quote($base, '/').'/', '', __FILE__, 1)));
