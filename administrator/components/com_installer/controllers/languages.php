<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 */
class InstallerControllerLanguages extends JController {


	/**
	 * Find new updates.
	 */
	function find()
	{
		// TODO
	}

	/**
	 * Purgue the updates list.
	 *
	 * @since	2.5
	 */
	function purge()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Purge updates
		$model = $this->getModel('update');
		$model->purge();
		$model->enableSites();
		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=languages', false), $model->_message);
	}

	/**
	 * Install a language.
	 *
	 * @since	2.5
	 */
	function install()
	{
		$model = $this->getModel('languages');

		// Get array of selected languages
		$lids	= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($lids, array());

		if (!$lids) {
			// No languages have been selected
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_INSTALLER_MSG_DISCOVER_NOEXTENSIONSELECTED'));
		}
		else
		{
			// install selected languages
			$model->install($lids);
		}

		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=languages', false));
	}

}
