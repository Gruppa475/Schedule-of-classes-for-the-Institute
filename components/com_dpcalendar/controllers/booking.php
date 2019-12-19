<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_dpcalendar/models');

class DPCalendarControllerBooking extends JControllerLegacy
{

	public function invite()
	{
		$model = $this->getModel();

		$booking = $model->getItem(array('uid' => $this->input->get('uid')));

		if ($this->input->getInt('accept')) {
			$booking->state = $booking->price > 0 ? 3 : 1;
			$model->save((array)json_decode(json_encode($booking)));
			$this->setRedirect(DPCalendarHelperRoute::getBookingRoute($booking));
		} else {
			$model->delete($booking->id);
			$this->setRedirect(JUri::base());
		}
	}

	public function calculateprice()
	{
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		$formData = $this->input->get('jform', [], 'array');
		$id       = $this->input->getInt('b_id');
		$data     = ['total' => '', 'events' => []];
		if ($id) {
			$data['total'] = $this->getModel()->getItem($id)->price;
			foreach ($this->getModel()->getTickets($id) as $ticket) {
				$data['tickets'][$ticket->event_id] = $ticket->price;
			}
		} else {
			$price = 0.00;
			foreach ($formData['event_id'] as $eid => $types) {
				$data['events'][$eid] = [];
				foreach ($types as $type => $prices) {
					$data['events'][$eid][$type] = [];
					foreach ($prices as $index => $amount) {
						$event                                    = $this->getModel('Event')->getItem($eid);
						$data['events'][$eid][$type][$index] = ['discount' => '0.00', 'original' => '0.00'];
						if ($type == 'tickets') {
							foreach ($event->price->value as $key => $value) {
								if ($key != $index) {
									continue;
								}
								$priceOriginal = $value * $amount;
								$priceDiscount = \DPCalendar\Helper\Booking::getPriceWithDiscount($value, $event) * $amount;

								$price                               += number_format($priceDiscount, 2, '.', '');
								$data['events'][$eid][$type][$index] = [
									'discount' => DPCalendarHelper::renderPrice(number_format($priceDiscount, 2, '.', '')),
									'original' => DPCalendarHelper::renderPrice(number_format($priceOriginal, 2, '.', ''))
								];
							}
						}
						if ($type == 'options') {
							foreach ($event->booking_options as $key => $option) {
								if (preg_replace('/\D/', '', $key) != $index) {
									continue;
								}
								$priceOriginal = $option->price * $amount;
								$priceDiscount = $priceOriginal;

								$price                               += number_format($priceDiscount, 2, '.', '');
								$data['events'][$eid][$type][$index] = [
									'discount' => DPCalendarHelper::renderPrice(number_format($priceDiscount, 2, '.', '')),
									'original' => DPCalendarHelper::renderPrice(number_format($priceOriginal, 2, '.', ''))
								];
							}
						}
					}
				}
			}
			$data['total'] = $price;
		}
		$data['total']    = DPCalendarHelper::renderPrice(number_format($data['total'], 2, '.', ''));
		$data['currency'] = DPCalendarHelper::getComponentParameter('currency_symbol', '$');
		DPCalendarHelper::sendMessage(null, false, $data);
	}

	public function invoice()
	{
		$model   = $this->getModel('Booking', 'DPCalendarModel', array('ignore_request' => false));
		$state   = $model->getState();
		$booking = $model->getItem();

		if ($booking == null) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		$fileName = \DPCalendar\Helper\Booking::createInvoice($booking, $model->getTickets($booking->id), $state->params);
		if ($fileName) {
			JFactory::getApplication()->close();
		} else {
			JFactory::getApplication()->redirect(DPCalendarHelperRoute::getBookingRoute($booking));
		}
	}

	public function pay()
	{
		$app = JFactory::getApplication();

		$rawDataPost = $this->input->post->getArray();
		$rawDataGet  = $this->input->get->getArray();
		$data        = array_merge($rawDataGet, $rawDataPost);

		/*
		 * Some plugins result in an empty Itemid being added to the request
		 * data, screwing up the payment callback validation in some cases (e.g.
		 * PayPal).
		 */
		if (array_key_exists('Itemid', $data)) {
			if (empty($data['Itemid'])) {
				unset($data['Itemid']);
			}
		}

		JLoader::import('joomla.plugin.helper');
		JPluginHelper::importPlugin('dpcalendarpay');

		$app->triggerEvent(
			'onDPPaymentCallBack',
			array($this->input->getCmd('paymentmethod', 'none'), $data)
		);

		$tmpl = '';
		if ($t = $this->input->get('tmpl')) {
			$tmpl = '&tmpl=' . $t;
		}

		$app->redirect(\JRoute::_('index.php?option=com_dpcalendar&view=booking&layout=order&b_id=' . $data['b_id'] . $tmpl, false));
	}

	public function paycancel()
	{
		$this->getModel()->delete($this->input->getInt('b_id'));
		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_dpcalendar&view=booking&layout=cancel&b_id=' . $this->input->getInt('b_id') . '&ptype=' .
				$this->input->getInt('ptype')
			)
		);
	}

	public function getModel($name = 'Booking', $prefix = 'DPCalendarModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
