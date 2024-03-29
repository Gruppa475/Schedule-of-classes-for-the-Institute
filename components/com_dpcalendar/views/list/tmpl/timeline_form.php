<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

if ($this->params->get('list_autocomplete', 1)) {
	$this->dpdocument->loadLibrary(\DPCalendar\HTML\Document\HtmlDocument::LIBRARY_AUTOCOMPLETE);
}

$loc     = $this->state->get('filter.location');
$tmpl    = $this->input->getCmd('tmpl') ? '&tmpl=' . $this->input->getCmd('tmpl') : '';
$action  = $this->router->route('index.php?option=com_dpcalendar&view=list&Itemid=' . $this->input->getInt('Itemid') . $tmpl);
$visible = $this->state->get('filter.search') || $this->overrideStartDate || $this->overrideEndDate || $loc;
?>
<form action="<?php echo $action; ?>" method="post"
	  class="com-dpcalendar-timeline__form com-dpcalendar-timeline__form_<?php echo $visible ? '' : 'hidden'; ?> dp-form form-validate dp-print-hide dp-print-hide">
	<div class="com-dpcalendar-timeline__form-container">
		<div class="com-dpcalendar-timeline__text-search">
			<input type="text" name="filter-search" value="<?php echo $this->state->get('filter.search'); ?>"
				   class="dp-input dp-input-text"
				   placeholder="<?php echo $this->translate('JGLOBAL_FILTER_LABEL'); ?>">
		</div>
		<div class="com-dpcalendar-timeline__date-search">
			<?php $this->displayData['title'] = $this->translate('COM_DPCALENDAR_FIELD_START_DATE_LABEL'); ?>
			<?php $this->displayData['name'] = 'start-date'; ?>
			<?php $this->displayData['date'] = $this->overrideStartDate ? $this->startDate : null; ?>
			<?php echo $this->layoutHelper->renderLayout('block.datepicker', $this->displayData); ?>
			<?php $this->displayData['title'] = $this->translate('COM_DPCALENDAR_FIELD_END_DATE_LABEL'); ?>
			<?php $this->displayData['name'] = 'end-date'; ?>
			<?php $this->displayData['date'] = $this->overrideEndDate ? $this->endDate : null; ?>
			<?php echo $this->layoutHelper->renderLayout('block.datepicker', $this->displayData); ?>
		</div>
	</div>
	<div class="com-dpcalendar-timeline__form-container">
		<div class="com-dpcalendar-timeline__location-search">
			<input type="text" name="location" class="dp-input dp-input-text dp-input_location" autocomplete="off"
				   value="<?php echo $loc ? $loc->title : ''; ?>"
				   placeholder="<?php echo $this->translate('COM_DPCALENDAR_LOCATION'); ?>"
				   data-latitude="<?php echo $loc ? $loc->latitude : ''; ?>"
				   data-longitude="<?php echo $loc ? $loc->longitude : ''; ?>">
		</div>
		<div class="com-dpcalendar-timeline__radius-search">
			<?php $radius = $this->state->get('filter.radius', 50); ?>
			<select name="radius" class="dp-input dp-input-select">
				<option value="5"<?php echo $radius == 5 ? ' selected' : ''; ?>>5</option>
				<option value="10"<?php echo $radius == 10 ? ' selected' : ''; ?>>10</option>
				<option value="20"<?php echo $radius == 20 ? ' selected' : ''; ?>>20</option>
				<option value="50"<?php echo $radius == 50 ? ' selected' : ''; ?>>50</option>
				<option value="100"<?php echo $radius == 100 ? ' selected' : ''; ?>>100</option>
				<option value="500"<?php echo $radius == 500 ? ' selected' : ''; ?>>500</option>
				<option value="1000"<?php echo $radius == 1000 ? ' selected' : ''; ?>>1000</option>
				<option value="-1"<?php echo $radius == '-1' ? ' selected' : ''; ?>><?php echo $this->translate('JALL'); ?></option>
			</select>
			<?php $length = $this->state->get('filter.length-type', 'm'); ?>
			<select name="length-type" class="dp-input dp-input-select">
				<option value="m"<?php echo $length == 'm' ? ' selected' : ''; ?>>
					<?php echo $this->translate('COM_DPCALENDAR_FIELD_CONFIG_MAP_LENGTH_TYPE_METER'); ?>
				</option>
				<option value="mile"<?php echo $length == 'mile' ? ' selected' : ''; ?>>
					<?php echo $this->translate('COM_DPCALENDAR_FIELD_CONFIG_MAP_LENGTH_TYPE_MILE'); ?>
				</option>
			</select>
		</div>
	</div>
	<div class="com-dpcalendar-timeline__button-bar dp-button-bar">
		<button class="dp-button dp-button-current-location" type="button">
			<?php echo $this->layoutHelper->renderLayout('block.icon', ['icon' => \DPCalendar\HTML\Block\Icon::LOCATION]); ?>
			<?php echo $this->translate('COM_DPCALENDAR_VIEW_MAP_LABEL_CURRENT_LOCATION'); ?>
		</button>
		<button class="dp-button dp-button-search" type="button">
			<?php echo $this->layoutHelper->renderLayout('block.icon', ['icon' => \DPCalendar\HTML\Block\Icon::OK]); ?>
			<?php echo $this->translate('JSEARCH_FILTER'); ?>
		</button>
		<button class="dp-button dp-button-clear" type="button">
			<?php echo $this->layoutHelper->renderLayout('block.icon', ['icon' => \DPCalendar\HTML\Block\Icon::CANCEL]); ?>
			<?php echo $this->translate('JCLEAR'); ?>
		</button>
	</div>
	<input type="hidden" name="Itemid" value="<?php echo $this->input->getInt('Itemid'); ?>" class="dp-input dp-input-hidden">
</form>
