<?php
/**
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The HTML Joomla Core License View
 *
 * @package		Joomla.Installation
 * @since		3.0
 */
class InstallationViewDefaultlanguage extends JViewLegacy
{
	/**
	 * @var object item list of languages installed in the administrator
	 */
	public $items;

	/**
	 * Display the view
	 *
	 */
	public function display($tpl = null)
	{
		$items = $this->get('Installedlangs');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->items = $items;

		parent::display($tpl);
	}
}
