<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.application.component.modellist');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
jimport('joomla.updater.update');


/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since	2.5.5 @TODO: the version may change when will be pulled
 */
class InstallerModelLanguages extends JModelList
{
	/**
	 * Method to get the avaliable languages database query
	 *
	 * @return	JDatabaseQuery	The database query
	 * @since	2.5
	 */
	protected function _getListQuery()
	{
		$query = JFactory::getDBO()->getQuery(true);
		$query->select('update_id, name, version, detailsurl, type');
		$query->from('#__updates');

		return $query;

	}



	/**
	 * Install languages in the system.
	 *
	 * @param array $lids array of language ids selected in the list
	 * @TODO falta revisar los mensajes de error y añadir el since
	 */
	public function install($lids)
	{
		$app = JFactory::getApplication();
		$installer = JInstaller::getInstance();
		$failed = false;

		// Loop through every selected language
		foreach($lids as $id)
		{

			// Get the url to the XML manifest file of the selected language
			$remote_manifest 	= $this->_getLanguageManifest($id);
			if (!$remote_manifest)
			{
				// Could not find the url, the information in the update server may be corrupt
				$app->enqueueMessage(JText::_('no consigo el manifest').': '. $id);
				$failed = true;
				continue;
			}


			// Based on the language XML manifest get the url of the package to download
			$package_url 		= $this->_getPackageUrl($remote_manifest);
			if (!$package_url) {
				// Could not find the url , maybe the url is wrong in the update server, or there is not internet access
				$app->enqueueMessage(JText::_('no consigo la url del paquete').': '. $id);
				$failed = true;
				continue;
			}


			// Download the package to the tmp folder
			$package 			= $this->_downloadPackage($package_url);

			// Install the package
			if (!$installer->install($package['dir'])) {
				// There was an error installing the package
				$app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type']))));
				$failed = true;
				continue;
			}

			// Package installed sucessfully
			$app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type']))));

			// Cleanup the install files in tmp folder
			if (!is_file($package['packagefile'])) {
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
			}
			JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		}


	}

	/**
	 * Gets the manifest file of a selected language from a the language list in a update server.
	 * @param int $uid the id of the languaje in the #__updates table
	 * @return string
	 */
	protected function _getLanguageManifest($uid)
	{
		$instance = JTable::getInstance('update');
		$instance->load($uid);

		return $instance->detailsurl;
	}

	/**
	 * Finds the url of the package to download.
	 *
	 * @param string $remote_manifest url to the manifest XML file of the remote package
	 * @return string|false
	 */
	protected function _getPackageUrl( $remote_manifest )
	{
		$update = new JUpdate();
		$update->loadFromXML($remote_manifest);
		$package_url = $update->get('downloadurl', false)->_data;

		return $package_url;
	}


	/**
	 * Download a language package from a URL and unpack it in the tmp folder
	 *
	 * @return	Package details or false on failure
	 * @since	2.5.6
	 */
	protected function _downloadPackage($url)
	{

		// Download the package from the given URL
		$p_file = JInstallerHelper::downloadPackage($url);
		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}

		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path');

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

		return $package;
	}
}
