<?php
/**
 * @version 1.9.5
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_jem.category');
$saveOrder	= $listOrder=='ordering';

$params		= (isset($this->state->params)) ? $this->state->params : new JObject();
$settings	= $this->settings;
?>

<script>
window.addEvent('domready', function() {
	var h = <?php echo $settings->get('highlight','0'); ?>;

	switch(h)
	{
	case 0:
		break;
	case 1:
		highlightvenues();
		break;
	}
});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=venues'); ?>" method="post" name="adminForm" id="adminForm">

<fieldset id="filter-bar">
	<div class="filter-search fltlft">
		<?php echo $this->lists['filter']; ?>
		<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_JEM_SEARCH');?>" value="<?php echo $this->escape($this->state->get('filter_search')); ?>" class="text_area" onChange="document.adminForm.submit();" />
		<button type="submit"><?php echo JText::_('COM_JEM_GO'); ?></button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</div>
	<div class="filter-select fltrt">
		<select name="filter_state" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',array('all' => 0, 'archived' => 0, 'trash' => 0)), 'value', 'text', $this->state->get('filter_state'), true);?>
		</select>
	</div>
</fieldset>
<div class="clr"> </div>

<table class="table table-striped" id="articleList">
	<thead>
		<tr>
			<th width="1%" class="center"><?php echo JText::_('COM_JEM_NUM'); ?></th>
			<th width="1%" class="center"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th class="title"><?php echo JHtml::_('grid.sort', 'COM_JEM_VENUE', 'a.venue', $listDirn, $listOrder ); ?></th>
			<th width="20%"><?php echo JHtml::_('grid.sort', 'COM_JEM_ALIAS', 'a.alias', $listDirn, $listOrder ); ?></th>
			<th><?php echo JText::_( 'COM_JEM_WEBSITE' ); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_JEM_CITY', 'a.city', $listDirn, $listOrder ); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_JEM_STATE', 'a.state', $listDirn, $listOrder ); ?></th>
			<th width="1%"><?php echo JHtml::_('grid.sort', 'COM_JEM_COUNTRY', 'a.country', $listDirn, $listOrder ); ?></th>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JText::_('JSTATUS'); ?></th>
			<th><?php echo JText::_( 'COM_JEM_CREATION' ); ?></th>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_EVENTS', 'assignedevents', $listDirn, $listOrder ); ?></th>
			<th width="8%" colspan="2"><?php echo JHtml::_('grid.sort', 'COM_JEM_REORDER', 'a.ordering', $listDirn, $listOrder ); ?></th>
			<th width="1%" class="center" nowrap="nowrap"><?php echo JHtml::_('grid.sort', 'COM_JEM_ID', 'a.id', $listDirn, $listOrder ); ?></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="20">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

	<tbody id="seach_in_here">
		<?php foreach ($this->items as $i => $row) : ?>
			<?php
			$ordering	= ($listOrder == 'ordering');
			$canCreate	= $user->authorise('core.create');
			$canEdit	= $user->authorise('core.edit');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state') && $canCheckin;

			$link 		= 'index.php?option=com_jem&amp;task=venue.edit&amp;id='. $row->id;
			$published 	= JHtml::_('jgrid.published', $row->published, $i, 'venues.', $canChange, 'cb', $row->publish_up, $row->publish_down);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td align="left" class="venue">
					<?php if ($row->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'venues.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jem&task=venue.edit&id='.(int) $row->id); ?>">
							<?php echo $this->escape($row->venue); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->venue); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if (JString::strlen($row->alias) > 25) : ?>
						<?php echo $this->escape(JString::substr($row->alias, 0 , 25)).'...'; ?>
					<?php else : ?>
						<?php echo $this->escape($row->alias); ?>
					<?php endif; ?>
				</td>
				<td align="left">
					<?php if ($row->url) : ?>
						<a href="<?php echo $this->escape($row->url); ?>" target="_blank">
							<?php if (JString::strlen($row->url) > 25) : ?>
								<?php echo $this->escape(JString::substr($row->url, 0 , 25)).'...'; ?>
							<?php else : ?>
								<?php echo $this->escape($row->url); ?>
							<?php endif; ?>
						</a>
					<?php else : ?>
						-
					<?php endif; ?>
				</td>
				<td align="left" class="city"><?php echo $row->city ? $this->escape($row->city) : '-'; ?></td>
				<td align="left" class="state"><?php echo $row->state ? $this->escape($row->state) : '-'; ?></td>
				<td class="country"><?php echo $row->country ? $this->escape($row->country) : '-'; ?></td>
				<td class="center"><?php echo $published; ?></td>
				<td>
					<?php echo JText::_( 'COM_JEM_AUTHOR' ).': '; ?>
					<a href="<?php echo 'index.php?option=com_users&amp;task=edit&amp;hidemainmenu=1&amp;cid[]='.$row->created_by; ?>">
						<?php echo $row->author; ?>
					</a><br />
					<?php echo JText::_('COM_JEM_EMAIL').': '; ?><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a><br />
					<?php
					$delivertime 	= JHtml::_('date',$row->created,JText::_('DATE_FORMAT_LC2'));
					$edittime 		= JHtml::_('date',$row->modified,JText::_('DATE_FORMAT_LC2'));
					$ip				= $row->author_ip == 'COM_JEM_DISABLED' ? JText::_('COM_JEM_DISABLED') : $row->author_ip;
					$image 			= JHtml::_('image','com_jem/icon-16-info.png', NULL,NULL,true);
					$overlib 		= JText::_('COM_JEM_CREATED_AT').': '.$delivertime.'<br />';
					$overlib		.= JText::_('COM_JEM_WITH_IP').': '.$ip.'<br />';
					if ($row->modified != '0000-00-00 00:00:00') {
						$overlib 	.= JText::_('COM_JEM_EDITED_AT').': '.$edittime.'<br />';
						$overlib 	.= JText::_('COM_JEM_EDITED_FROM').': '.$row->editor.'<br />';
					}
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JEM_VENUE_STATS'); ?>::<?php echo $overlib; ?>">
						<?php echo $image; ?>
					</span>
				</td>
				<td class="center"><?php echo $row->assignedevents; ?></td>
				<td align="right">
					<?php
						/* @todo fix ordering  */
						echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'JLIB_HTML_MOVE_UP', $ordering );
					?>
				</td>
				<td align="left">
					<?php echo $this->pagination->orderDownIcon( $i,$this->pagination->total, true, 'orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering ); ?>
				</td>
				<td class="center"><?php echo $row->id; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>