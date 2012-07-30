<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       2.5.2
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * HTML Languages View class for the Languages component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 * @since       1.6
 */
class LanguagesViewLanguages extends JViewLegacy
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
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/languages.php';
		$canDo	= LanguagesHelper::getActions();

		JToolBarHelper::title(JText::_('COM_LANGUAGES_VIEW_LANGUAGES_TITLE'), 'langmanager.png');

		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('language.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('language.edit');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.published') != 2)
			{
				JToolBarHelper::publishList('languages.publish');
				JToolBarHelper::unpublishList('languages.unpublish');
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'languages.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('languages.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{

			// Add an upload button.
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Link', 'extension', 'INSTALL_LANGUAGES', 'index.php?option=com_installer&view=languages');
			JToolBarHelper::divider();

			JToolBarHelper::preferences('com_languages');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_CONTENT');
	}
}
