<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JLoader::import('controllers.ticket', JPATH_COMPONENT_ADMINISTRATOR);

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_dpcalendar/models');

class DPCalendarControllerTicketForm extends DPCalendarControllerTicket
{

	protected $view_item = 'ticketform';
	protected $view_list = 'calendar';
	protected $option = 'com_dpcalendar';

	private $ignoreCaptcha = false;

	protected function allowAdd($data = array())
	{
		return false;
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = isset($data[$key]) ? $data[$key] : 0;
		$ticket   = $this->getModel()->getItem($recordId);

		if (empty($ticket)) {
			return false;
		}

		return $ticket->params->get('access-edit');
	}

	protected function allowDelete($data = array(), $key = 'id')
	{
		return false;
	}

	public function edit($key = 'id', $urlVar = 't_id')
	{
		$this->input->set('layout', 'edit');

		return parent::edit($key, $urlVar);
	}

	public function cancel($key = 't_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function saveall($key = null, $urlVar = 't_id')
	{
		$result = true;

		$ticketData = [];
		foreach ($this->input->getArray() as $name => $value) {
			if (strpos($name, 'ticket-') !== 0) {
				continue;
			}
			$ticketData[] = $value;
		}

		//List of tickets to save
		foreach ($ticketData as $index => $value) {
			$this->input->set($urlVar, $value['id']);

			$this->input->post->set('jform', $value);

			// We can have only one captcha so it is in the last ticket form
			$this->ignoreCaptcha = $index < count($ticketData) - 1;

			$result = parent::save($key, $urlVar);

			if (!$result) {
				break;
			}

			$this->setMessage('');
		}

		if ($result) {
			$this->setMessage(JText::plural('COM_DPCALENDAR_TICKETS_SAVE_SUCCESS', count($ticketData)));
		}

		if ($return = $this->input->get('return', null, 'base64')) {
			$this->setRedirect(base64_decode($return));
		}

		return $result;
	}

	public function save($key = null, $urlVar = 't_id')
	{
		$result = parent::save($key, $urlVar);

		if ($return = $this->input->get('return', null, 'base64')) {
			$this->setRedirect(base64_decode($return));
		}

		return $result;
	}

	public function getModel($name = 'Ticket', $prefix = 'DPCalendarModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		if ($this->getTask() == 'saveall') {
			$model->setState('captcha.disabled', $this->ignoreCaptcha);
		}

		return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();

		if ($itemId) {
			$append .= '&Itemid=' . $itemId;
		}

		if ($return) {
			$append .= '&return=' . base64_encode($return);
		}

		$append .= '&t_id=' . $this->input->getInt('t_id');
		if ($this->input->getCmd('tmpl')) {
			$append .= '&tmpl=' . $this->input->getCmd('tmpl');
		}

		return $append;
	}

	protected function getReturnPage()
	{
		$return = $this->input->getBase64('return');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		} else {
			return base64_decode($return);
		}
	}
}
