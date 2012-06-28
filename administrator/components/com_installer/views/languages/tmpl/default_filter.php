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

?>
<fieldset id="filter-bar">
	<div class="filter-search fltlft">
<<<<<<< HEAD
		<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_INSTALLER_LANGUAGES_FILTER_SEARCH_DESC'); ?>" />

		<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
=======
		<label class="filter-search-lbl" for="filter_search">
			<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>:</label>
		<input type="text" name="filter_search" id="filter_search"
		       value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
		       title="<?php echo JText::_('COM_INSTALLER_LANGUAGES_FILTER_SEARCH_DESC'); ?>" />

		<button type="submit" class="btn">
			<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
			<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

>>>>>>> 9402f364d1c404d318e8fc030082738ad4edad6e
	</div>
</fieldset>
<div class="clr"></div>
