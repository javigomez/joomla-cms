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

<<<<<<< HEAD
include_once dirname(__FILE__).'/../default/view.php';
=======
jimport('joomla.application.component.view');
>>>>>>> 9402f364d1c404d318e8fc030082738ad4edad6e

/**
 * Language installer view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 * @since       3.1
 */
<<<<<<< HEAD
class InstallerViewLanguages extends InstallerViewDefault
=======
class InstallerViewLanguages extends JView
>>>>>>> 9402f364d1c404d318e8fc030082738ad4edad6e
{
	/**
	 * @var object item list
	 */
	protected $items;

	/**
	 * @var object pagination information
	 */
	protected $pagination;

	/**
	 * @var object model state
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   null  $tpl  template to display
	 *
	 * @return mixed|void
	 */
	public function display($tpl=null)
	{
		// Get data from the model
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

<<<<<<< HEAD
=======
		$this->addToolbar();
>>>>>>> 9402f364d1c404d318e8fc030082738ad4edad6e
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		$canDo	= InstallerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_INSTALLER_HEADER_' . $this->getName()), 'install.png');

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::custom('languages.install', 'upload', 'upload', 'COM_INSTALLER_TOOLBAR_INSTALL', true, false);
			JToolBarHelper::custom('languages.find', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_FIND_LANGUAGES', false, false);
			JToolBarHelper::custom('languages.purge', 'purge', 'purge', 'JTOOLBAR_PURGE_CACHE', false, false);
			JToolBarHelper::divider();
<<<<<<< HEAD
			parent::addToolbar();
=======
			JToolBarHelper::preferences('com_installer');
			JToolBarHelper::divider();

>>>>>>> 9402f364d1c404d318e8fc030082738ad4edad6e
			// TODO: this help screen will need to be created
			JToolBarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_LANGUAGES');
		}
	}
}
