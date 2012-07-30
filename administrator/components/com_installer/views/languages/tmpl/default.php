<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<form
	action="<?php echo JRoute::_('index.php?option=com_installer&view=languages');?>"
	method="post" name="adminForm" id="adminForm">

	<?php echo $this->loadTemplate('filter'); ?>

	<div class="width-100 fltlft">
		<fieldset>
			<legend>
				<?php echo JText::_('COM_INSTALLER_LANGUAGES_AVAILABLE_LANGUAGES'); ?>
			</legend>

			<table class="adminlist" cellspacing="1">
				<thead>
					<tr>
						<th width="20">
							<input type="checkbox" name="checkall-toggle"
							value="" title="Check All" onclick="Joomla.checkAll(this)">
						</th>
						<th class="nowrap">
							<a href="#"
							onclick="Joomla.tableOrdering('name','asc','');"
							title="Click to sort by this column">
								Name
							</a>
						</th>
						<th width="10%" class="center">
							Version
						</th>
						<th>
							Type
						</th>
						<th width="35%">
							URL Details
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					foreach ($this->items as $i => $language)
					{
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td>
							<?php echo JHtml::_('grid.id', $i, $language->update_id, false, 'cid'); ?>
						</td>
						<td>
							<span class="editlinktip hasTip" title="">
								<?php echo $language->name; ?>
							</span>
						</td>
						<td class="center">
							<?php echo $language->version; ?>
						</td>
						<td class="center">
							<?php echo $language->type; ?>
						</td>
						<td>
							<?php echo $language->detailsurl; ?>
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="clr"></div>
