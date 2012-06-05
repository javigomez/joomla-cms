<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Language installer view
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.6
 */
class InstallerViewLanguages extends JView
{
	protected $items;
	protected $pagination;

	/**
	 * @since	2.5.5 @TODO: the version may change when will be pulled
 	 */
	function display($tpl=null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 *
	 * @since	2.5.6
	 */
	protected function addToolbar()
	{
		$canDo	= InstallerHelper::getActions();

		JToolBarHelper::custom('languages.install', 'upload', 'upload', 'COM_INSTALLER_TOOLBAR_INSTALL', true, false);
		JToolBarHelper::custom('languages.find', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_FIND_LANGUAGES', false, false);
		JToolBarHelper::custom('languages.purge', 'purge', 'purge', 'JTOOLBAR_PURGE_CACHE', false, false);
		JToolBarHelper::divider();

		JToolBarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_LANGUAGES'); //@TODO: this help screen will need to be created
	}
}
