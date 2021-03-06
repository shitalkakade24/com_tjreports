<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjreports
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

require_once __DIR__ . '/view.base.php';

/**
 * View class for a list of Tjreports.
 *
 * @since  1.0.0
 */
class TjreportsViewReports extends ReportsViewBase
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$input  = JFactory::getApplication()->input;
		$result = $this->processData();

		if (!$result)
		{
			return false;
		}

		$this->addToolbar();
		$this->addDocumentHeaderData();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  Toolbar instance
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		// Old code
		$extension = JFactory::getApplication()->input->get('client', '', 'word');
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title(JText::_('COM_TJREPORTS_TITLE_REPORT'), 'list');

		$button = "<a class='btn' class='button'
				type='submit' onclick=\"Joomla.submitbutton('reports.csvexport'); jQuery('#task').val('');\" href='#'><span title='Export'
				class='icon-download'></span>" . JText::_('COM_TJREPORTS_CSV_EXPORT') . "</a>";
			$bar->appendButton('Custom', $button);

		// List of plugin
		if ($extension)
		{
			JFactory::getApplication()->input->set('extension', $extension);
			JLoader::import('administrator.components.com_tjreports.helpers.tjreports', JPATH_SITE);
			TjreportsHelper::addSubmenu('reports');
			$this->sidebar = JHtmlSidebar::render();

			$model	= $this->getModel();

			// Get all enable plugins
			$this->enableReportPlugins = $this->model->getenableReportPlugins($extension);

			foreach ($this->enableReportPlugins as $eachPlugin) :
				$this->model->loadLanguage($eachPlugin->element);
				$btnclass = ($this->pluginName == $eachPlugin->element) ? " active btn-primary " : "";
				$button = "<a class='btn button report-btn " . $btnclass . "' id='" . $eachPlugin->element . "'
				onclick=\"tjrContentUI.report.loadReport('" . $eachPlugin->element . "','" . $extension . "'); \" ><span
				class='icon-list'></span>" . JText::_($eachPlugin->name) . "</a>";
					$bar->appendButton('Custom', $button);
			endforeach;
		}
		else
		{
			JToolBarHelper::cancel('tjreport.cancel', 'JTOOLBAR_CANCEL');
		}
	}

	/**
	 * Add the script and Style.
	 *
	 * @return  Void
	 *
	 * @since	1.6
	 */
	protected function addDocumentHeaderData()
	{
		JHtml::_('formbehavior.chosen', 'select');
		$document = JFactory::getDocument();
		$document->addScript(JURI::root() . '/components/com_tjreports/assets/js/tjrContentService.js');
		$document->addScript(JURI::root() . '/components/com_tjreports/assets/js/tjrContentUI.js');
		$document->addStylesheet(JURI::root() . '/components/com_tjreports/assets/css/tjreports.css');
		$document->addScriptDeclaration('tjrContentUI.base_url = "' . Juri::base() . '"');
		$document->addScriptDeclaration('tjrContentUI.root_url = "' . Juri::root() . '"');
		JText::script('JERROR_ALERTNOAUTHOR');

		if (method_exists($this->model, 'getScripts'))
		{
			$plgScripts = (array) $this->model->getScripts();

			foreach ($plgScripts as $script)
			{
				$document->addScript($script);
			}
		}

		if (method_exists($this->model, 'getStyles'))
		{
			$styles = (array) $this->model->getStyles();

			foreach ($styles as $style)
			{
				$document->addStylesheet($style);
			}
		}
	}
}
