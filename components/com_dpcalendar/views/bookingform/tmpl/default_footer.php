<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

if ($this->bookingId) {
	return;
}

$text = !$this->bookingId ? 'COM_DPCALENDAR_VIEW_BOOKING_BOOK_BUTTON' : ($this->booking->state == 3 ? 'COM_DPCALENDAR_PAY' : 'JSAVE');
?>
<div class="com-dpcalendar-bookingform__actions dp-button-bar">
	<button type="button" class="dp-button dp-button-save" data-task="save">
		<?php echo $this->layoutHelper->renderLayout('block.icon', ['icon' => \DPCalendar\HTML\Block\Icon::OK]); ?>
		<?php echo $this->translate($text); ?>
	</button>
	<button type="button" class="dp-button dp-button-cancel" data-task="cancel">
		<?php echo $this->layoutHelper->renderLayout('block.icon', ['icon' => \DPCalendar\HTML\Block\Icon::CANCEL]); ?>
		<?php echo $this->translate('JCANCEL'); ?>
	</button>
</div>
