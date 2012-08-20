<?php
/**
 * @package    Joomla.Installation
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
include_once __DIR__ . '/setup.json.php';


/**
 * Setup controller for the Joomla Core Installer.
 * - JSON Protocol -
 *
 * @package  Joomla.Installation
 * @since    3.0
 */
class InstallationControllerLanguages extends InstallationControllerSetup
{
	public function __construct($config = array())
	{
		JFactory::$config = null;
		JFactory::getConfig(JPATH_SITE . '/configuration.php');
		JFactory::$session = null;
		parent::__construct();
		// Overrides application config and set the configuration.php file so tokens and database works
	}


	/**
	 * Method to install languages to Joomla application.
	 *
	 * @return  void
	 * @since   3.x.x
	 */
	public function installLanguages()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JSession::checkToken() or $this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		// Get the application object.
		$app = JFactory::getApplication();


		// Get array of selected languages
		$lids	= $this->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($lids, array());

		// Get the languages model.
		$model = $this->getModel('Languages', 'InstallationModel');

		$return = false;
		if (!$lids)
		{
			// No languages have been selected
			$app->enqueueMessage(JText::_('INSTL_LANGUAGES_NO_LANGUAGE_SELECTED'));
		}
		else
		{
			// Install selected languages
			$return	= $model->install($lids);
		}

		$r = new stdClass;
		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the language selection screen.
			$r->view = 'languages';
			$this->sendResponse($r);
		}

		// Create a response body.
		$r->view = 'defaultlanguage';

		// Send the response.
		$this->sendResponse($r);
	}

	/**
	 * Set the selected language as the main language to the Joomla! administrator
	 *
	 * @since	X.x.x
	 */
	function setDefaultLanguage()
	{
		// Check for a valid token. If invalid, send a 403 with the error message.
		JSession::checkToken() or $this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		// Check for request forgeries
		$lang = JRequest::getString('lang', false);

		// check that is an Lang ISO Code avoiding any injection.
		if (!preg_match('/^[a-z]{2}(\-[A-Z]{2})?$/', $lang))
		{
			$lang = 'en-GB';
		}

		// Get the languages model.
		$model = $this->getModel('Languages', 'InstallationModel');

		$r = new stdClass;

		if (!$model->setDefault($lang))
		{
			// Create a response body.
			$r->text = JText::_('INSTL_DEFAULTLANGUAGE_COULDNT_SET_DEFAULT');
			$r->view = 'complete';

			// Send the response.
			$this->sendResponse($r);
		}

		// Create a response body.
		$r->view = 'complete';

		// Send the response.
		$this->sendResponse($r);

	}
}