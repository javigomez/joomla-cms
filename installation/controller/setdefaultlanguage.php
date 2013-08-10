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

		// Check for if user has activated the multilingual site
		$data   = $this->input->post->get('jform', array(), 'array');
		$activeMultilanguage = (int) $data['activateMultilanguage'];
		$menuTitles = $this->input->get('menu', '', 'array');

		if ($activeMultilanguage)
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			// Enable plg_system_languagefilter

			$query
				->update( '#__extensions' )
				->set( 'enabled = 1' )
				->where( 'name = ' . $db->quote('plg_system_languagefilter') )
                                ->where( 'type = ' . $db->quote('plugin') );
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
                        
                        // Add Module Language Switcher
                        JTable::addIncludePath(JPATH_LIBRARIES.'/legacy/table/');
                        $tableModule = JTable::getInstance( 'Module', 'JTable' );
                        $moduleData = array(
                                        'id'            => 0,
                                        'title'         => 'Language Switcher',
                                        'note'          => '',
                                        'content'       => '',
                                        'position'      => 'position-0',
                                        'module'        => 'mod_languages',
                                        'access'        => 1,
                                        'showtitle'     => 0,
                                        'params'        => '{"header_text":"","footer_text":"","dropdown":"0","image":"1","inline":"1","show_active":"1","full_name":"1","layout":"_:default","moduleclass_sfx":"","cache":"0","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}',
                                        'client_id'     => 0,
                                        'language'      => '*',
                                        'published'     => 1
                                    );
                        // Bind the data.
                        if (!$tableModule->bind($moduleData)) {
                                $app->enqueueMessage($tableModule->getError());
                                return false;
                        }

                        // Check the data.
                        if (!$tableModule->check()) {
                                $app->enqueueMessage($tableModule->getError());
                                return false;
                        }

                        // Store the data.
                        if (!$tableModule->store()) {
                                $app->enqueueMessage($tableModule->getError());
                                return false;
                        }
                        // End Module Language Switcher
                        
                        // Add Module in Module menus
                        $query->clear()
				->insert('#__modules_menu')
				->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
				->values((int) $tableModule->id . ', 0');
			$db->setQuery($query);

			try {
                            $db->execute();
			} catch (RuntimeException $e) {
                            $app->enqueueMessage($e->getMessage());
			}
                        // End Module in Module menus
                                
                        // Añadir los menus
                        JLoader::registerPrefix('J', JPATH_PLATFORM . '/legacy');
                        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_menus/tables/');
                        
                        $SiteLanguages = $model->getInstalledlangsFrontend();
                        foreach(@$SiteLanguages as $SiteLang) {
                            // Add Language Manager: Content Languages 
                            $tableLanguage = &JTable::getInstance('Language');
                            
                            // Search if just added
                            $return = $tableLanguage->load(array('lang_code' => $SiteLang->language));
                            if ($return === false) {
                                $langs  = explode('-', $SiteLang->language );
                                $lang   = $langs[0];
                                $region = $langs[1];

                                $langData = array(
                                        'lang_id'       => 0,
                                        'lang_code'     => $SiteLang->language,
                                        'title'         => $SiteLang->name,
                                        'title_native'  => $SiteLang->name,
                                        'sef'           => $lang,
                                        'image'         => $lang,
                                        'published'     => 1
                                    );

                                // Bind the data.
                                if (!$tableLanguage->bind($langData)) {
                                        $app->enqueueMessage($tableLanguage->getError());
                                        return false;
                                }

                                // Check the data.
                                if (!$tableLanguage->check()) {
                                        $app->enqueueMessage($tableLanguage->getError());
                                        return false;
                                }

                                // Store the data.
                                if (!$tableLanguage->store()) {
                                        $app->enqueueMessage($tableLanguage->getError());
                                        return false;
                                }
                            }
                            // End add Language Manager: Content Languages 
//                        }
//                        
//                        $SiteLanguages = $model->getInstalledlangsFrontend();
//                        foreach(@$SiteLanguages as $SiteLang) {
                            
                            // Add Menu Group
                            $tableMenu = &JTable::getInstance('Type', 'JTableMenu');
                               
                            $menuData = array(
                                    'id'            => 0,
                                    'menutype'      => 'mainmenu_'.$SiteLang->language,
                                    'title'         => 'Main Menu ('.$SiteLang->language.')',
                                    'description'   => 'The main menu for the site in language'. $SiteLang->name
                                );
                            
                            // Bind the data.
                            if (!$tableMenu->bind($menuData)) {
                                    $app->enqueueMessage($tableMenu->getError());
                                    return false;
                            }

                            // Check the data.
                            if (!$tableMenu->check())
							{
                                    $app->enqueueMessage($tableMenu->getError());
                                    return false;
                            }

                            // Store the data.
                            if (!$tableMenu->store()) {
                                    $app->enqueueMessage($tableMenu->getError());
                                    return false;
                            }
                            // End Add Menu Group
//                        }
//                        
//                        foreach(@$SiteLanguages as $SiteLang) {
                            // Add Menu Item 
                            $tableItem = &JTable::getInstance('Menu', 'MenusTable');
                            
                            $title = 'Home '.$SiteLang->language;
                            if (isset($menuTitles[$SiteLang->language])) {
                                $title = $menuTitles[$SiteLang->language];
                            }
                            $alias = 'home_'.$SiteLang->language;
                                    // JApplication::stringURLSafe($title);
                            
                            $menuItem = array(
                                    'id'            => 0,
                                    'title'         => $title,
                                    'alias'         => $alias,
                                    'menutype'      => 'mainmenu-'.$SiteLang->language,
                                    'type'          => 'component',
                                    'link'          => 'index.php?option=com_content&view=featured',
                                    'component_id'  => 22,
                                    'published'     => 1,
                                    'parent_id'     => 1,
                                    'level'         => 1,
                                    'home'          => 1,
                                    'params'        => '{"featured_categories":[""],"num_leading_articles":"1","num_intro_articles":"3","num_columns":"3","num_links":"0","orderby_pri":"","orderby_sec":"front","order_date":"","multi_column_order":"1","show_pagination":"2","show_pagination_results":"1","show_noauth":"","article-allow_ratings":"","article-allow_comments":"","show_feed_link":"1","feed_summary":"","show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_readmore":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_hits":"","menu-anchor_title":"","menu-anchor_css":"","menu_image":"","show_page_heading":1,"page_title":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}',
                                    'language'      => $SiteLang->language
                                );
                            
                            // Bind the data.
                            if (!$tableItem->bind($menuItem)) {
                                $app->enqueueMessage($tableItem->getError());
                                return false;
                            }
                            
                            $tableItem->setLocation($menuItem['parent_id'], 'last-child');
                            
                            // Check the data.
                            if (!$tableItem->check()) {
                                $app->enqueueMessage($tableItem->getError());
                                return false;
                            }

                            // Store the data.
                            if (!$tableItem->store()) {
                                $app->enqueueMessage($tableItem->getError());
                                return false;
                            }

                            // Rebuild the tree path.
                            if (!$tableItem->rebuildPath($tableItem->id)) {
                                $app->enqueueMessage($table->getError());
                                return false;
                            }
                            // End Add Menu Item 
                        }
		}

		$r = new stdClass;

		// Redirect to the final page.
		$r->view = 'remove';
		$app->sendJsonResponse($r);
	}
}
