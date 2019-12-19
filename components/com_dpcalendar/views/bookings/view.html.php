<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();


class DPCalendarViewBookings extends \DPCalendar\View\BaseView
{

	public function display($tpl = null)
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_dpcalendar/models');
		$model = JModelLegacy::getInstance('Bookings', 'DPCalendarModel');
		$this->setModel($model, true);

		return parent::display($tpl);
	}

	public function init()
	{
		if ($this->user->guest) {
			$this->app->redirect($this->router->route('index.php?option=com_users&view=login&return=' . base64_encode(JFactory::getURI())),
				JText::_('COM_DPCALENDAR_NOT_LOGGED_IN'), 'warning');

			return false;
		}

		$this->getModel()->getState();

		// If we don't show the event bookings, show the user bookings
		if (!$this->input->getInt('e_id')) {
			$this->getModel()->setState('filter.my', true);
		} else {
			$this->getModel()->setState('filter.event_id', $this->input->getInt('e_id'));
		}

		$this->bookings = $this->get('Items');

		// Prepare content, eg custom fields
		JPluginHelper::importPlugin('content');
		foreach ($this->bookings as $booking) {
			$booking->text = '';
			$this->app->triggerEvent('onContentPrepare', array('com_dpcalendar.booking', &$booking, &$this->params, 0));
		}

		$this->pagination = $this->get('Pagination');

		return parent::init();
	}
}
