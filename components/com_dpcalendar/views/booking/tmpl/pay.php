<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JPluginHelper::importPlugin('dpcalendarpay');

$button = $this->app->triggerEvent('onDPPaymentNew', array($this->plugin, $this->booking));

foreach ($button as $b) {
	echo $b;
}
