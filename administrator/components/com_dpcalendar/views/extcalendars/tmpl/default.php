<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2018 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$this->dpdocument->loadLibrary(\DPCalendar\HTML\Document\HtmlDocument::LIBRARY_DPCORE);
$this->dpdocument->loadLibrary(\DPCalendar\HTML\Document\HtmlDocument::LIBRARY_IFRAME_CHILD);
$this->dpdocument->loadScriptFile('dpcalendar/views/extcalendars/default.js');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_dpcalendar&task=extcalendars.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'extcalendarsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

if ($this->input->getCmd('tmpl') == 'component') {
	echo JToolbar::getInstance('toolbar')->render();
}

if ($this->pluginParams->get('cache', 1) == '2') {
	$this->app->enqueueMessage(JText::_('COM_DPCALENDAR_VIEW_EXTCALENDARS_SYNC_STARTED'), 'notice');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_dpcalendar&view=extcalendars&dpplugin=' . $this->input->getWord('dpplugin')) . '&tmpl=' . $this->input->getWord('tmpl'); ?>"
	  method="post" name="adminForm" id="adminForm" class="com-dpcalendar-extcalendars"
	  data-sync="<?php echo $this->pluginParams->get('cache', 1); ?>"
	  data-sync-plugin="<?php echo $this->input->getWord('dpplugin'); ?>">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible">
				<?php echo JText::_('COM_DPCALENDAR_SEARCH_IN_TITLE'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_DPCALENDAR_SEARCH_IN_TITLE'); ?>"
				   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				   title="<?php echo JText::_('COM_DPCALENDAR_SEARCH_IN_TITLE'); ?>"/>
		</div>
		<div class="btn-group pull-left">
			<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<i class="icon-search"></i>
			</button>
			<button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
					onclick="document.id('filter_search').value='';this.form.submit();">
				<i class="icon-remove"></i>
			</button>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<table class="table table-striped" id="extcalendarsList">
		<thead>
		<tr>
			<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc',
					'JGRID_HEADING_ORDERING'); ?>
			</th>
			<th width="1%" class="">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
					   onclick="Joomla.checkAll(this)"/>
			</th>
			<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" class="nowrap hidden-phone">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" class="nowrap">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) {
			$ordering  = ($listOrder == 'a.ordering');
			$canCreate = $this->user->authorise('core.create', 'com_dpcalendar');
			$canEdit   = $this->user->authorise('core.edit', 'com_dpcalendar');
			$canChange = $this->user->authorise('core.edit.state', 'com_dpcalendar');
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="">
				<td class="order nowrap center hidden-phone">
					<?php
					$iconClass = '';
					if (!$canChange) {
						$iconClass = ' inactive';
					} else if (!$saveOrder) {
						$title = JText::_('JORDERINGDISABLED');
						$j     = new JVersion();
						if (DPCalendarHelper::isJoomlaVersion('3')) {
							JHtml::tooltipText('JORDERINGDISABLED');
						}
						$iconClass = ' inactive tip-top hasTooltip" title="' . $title;
					}
					?>
					<span class="sortable-handler<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>
					<?php if ($canChange && $saveOrder) { ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>"
							   class="width-20 text-area-order "/>
						<?php
					} ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'extcalendars.', $canChange, 'cb', $item->publish_up,
						$item->publish_down); ?>
				</td>
				<td class="nowrap">
					<?php if ($canEdit) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_dpcalendar&task=extcalendar.edit&id=' . (int)$item->id . '&tmpl=' . $this->input->getWord('tmpl') . '&dpplugin=' . $this->input->getWord('dpplugin')); ?>">
							<?php echo $this->escape($item->title); ?></a>
						<?php
					} else { ?>
						<?php echo $this->escape($item->title); ?>
						<?php
					} ?>
				</td>
				<td class="small hidden-phone">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="small nowrap">
					<?php if ($item->language == '*') { ?>
						<?php echo JText::alt('JALL', 'language'); ?>
					<?php } else { ?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
					<?php } ?>
				</td>
				<td class="center">
					<?php echo (int)$item->id; ?>
				</td>
			</tr>
			<?php
		} ?>
		</tbody>
	</table>

	<input type="hidden" name="action" value="" id="extcalendar-action"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
