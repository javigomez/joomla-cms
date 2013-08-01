<?php
/**
 * @package     Joomla.Installation
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller class to set the default application languages for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.1
 */
class InstallationControllerSetdefaultlanguage extends JControllerBase
{
	/**
	 * Constructor.
	 *
	 * @since   3.1
	 */
	public function __construct()
	{
		parent::__construct();

		// Overrides application config and set the configuration.php file so tokens and database works
		JFactory::$config = null;
		JFactory::getConfig(JPATH_SITE . '/configuration.php');
		JFactory::$session = null;
	}

	/**
	 * Execute the controller.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public function execute()
	{
		// Get the application
		/* @var InstallationApplicationWeb $app */
		$app = $this->getApplication();

		// Check for request forgeries.
		JSession::checkToken() or $app->sendJsonResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		// Get the languages model.
		$model = new InstallationModelLanguages;

		// Check for request forgeries in the administrator language
		$admin_lang = $this->input->getString('administratorlang', false);

		// Check that the string is an ISO Language Code avoiding any injection.
		if (!preg_match('/^[a-z]{2}(\-[A-Z]{2})?$/', $admin_lang))
		{
			$admin_lang = 'en-GB';
		}

		// Attempt to set the default administrator language
		if (!$model->setDefault($admin_lang, 'administrator'))
		{
			// Create a error response message.
			$app->enqueueMessage(JText::_('INSTL_DEFAULTLANGUAGE_ADMIN_COULDNT_SET_DEFAULT'), 'error');
		}
		else
		{
			// Create a response body.
			$app->enqueueMessage(JText::sprintf('INSTL_DEFAULTLANGUAGE_ADMIN_SET_DEFAULT', $admin_lang));
		}

		// Check for request forgeries in the site language
		$frontend_lang = $this->input->getString('frontendlang', false);

		// Check that the string is an ISO Language Code avoiding any injection.
		if (!preg_match('/^[a-z]{2}(\-[A-Z]{2})?$/', $frontend_lang))
		{
			$frontend_lang = 'en-GB';
		}

		// Attempt to set the default site language
		if (!$model->setDefault($frontend_lang, 'site'))
		{
			// Create a error response message.
			$app->enqueueMessage(JText::_('INSTL_DEFAULTLANGUAGE_FRONTEND_COULDNT_SET_DEFAULT'), 'error');
		}
		else
		{
			// Create a response body.
			$app->enqueueMessage(JText::sprintf('INSTL_DEFAULTLANGUAGE_FRONTEND_SET_DEFAULT', $frontend_lang));
		}

		// Check the form
		$data   = $this->input->post->get('jform', array(), 'array');
		$activeMultilanguage = (int) $data['activateMultilanguage'];

		if ($activeMultilanguage)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Enable plg_system_languagefilter

			$query
				->update('#__extensions')
				->set('enabled = 1')
				->where('name = ' . $db->quote('plg_system_languagefilter'))
				->where('type = ' . $db->quote('plugin'));
			$db->setQuery($query);

			if (!$db->execute())
			{
				$app->enqueueMessage(JText::sprintf('INSTL_DEFAULTLANGUAGE_COULD_NOT_ENABLE_PLG_LANGUAGEFILTER', $frontend_lang));
			}

			// Enable plg_system_languagecode

			$query
				->clear()
				->update('#__extensions')
				->set('enabled = 1')
				->where('name = ' . $db->quote('plg_system_languagecode'))
				->where('type = ' . $db->quote('plugin'));
			$db->setQuery($query);

			if (!$db->execute())
			{
				$app->enqueueMessage(JText::sprintf('INSTL_DEFAULTLANGUAGE_COULD_NOT_ENABLE_PLG_LANGUAGECODE', $frontend_lang));
			}

			// Add menus
			JLoader::registerPrefix('J', JPATH_PLATFORM . '/legacy');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables/');

			$siteLanguages = $model->getInstalledlangsFrontend();

			foreach ($siteLanguages as $SiteLang)
			{
				$menuTable = JTable::getInstance('Type', 'JTableMenu');

				$menuData = array(
					'id'          => 0,
					'menutype'    => 'mainmenu_' . $SiteLang->language,
					'title'       => 'Main Menu (' . $SiteLang->language . ')',
					'description' => 'The main menu for the site in language' . $SiteLang->name
				);

				// Bind the data.
				if (!$menuTable->bind($menuData))
				{
					$app->enqueueMessage(JText::_('INSTL_DEFAULTLANGUAGE_MULTILANGUAGE_COULDNT_CREATE_MENUS') . ': ' . $menuTable->getError(), 'error');
				}

				// Check the data.
				if (!$menuTable->check())
				{
					$app->enqueueMessage(JText::_('INSTL_DEFAULTLANGUAGE_MULTILANGUAGE_COULDNT_CREATE_MENUS') . ': ' . $menuTable->getError(), 'error');
				}

				// Store the data.
				if (!$menuTable->store())
				{
					$app->enqueueMessage(JText::_('INSTL_DEFAULTLANGUAGE_MULTILANGUAGE_COULDNT_CREATE_MENUS') . ': ' . $menuTable->getError(), 'error');
				}
			}
		}

		$r = new stdClass;

		// Redirect to the final page.
		$r->view = 'remove';
		$app->sendJsonResponse($r);
	}
}
