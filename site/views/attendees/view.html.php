<?php
/**
 * @version 1.9.6
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');
/**
 * View class for the JEM attendees screen
 * @todo fix view
 *
 * @package JEM
 *
 */
class JEMViewAttendees extends JViewLegacy {

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		if($this->getLayout() == 'print') {
			$this->_displayprint($tpl);
			return;
		}

		//initialise variables
		$db			= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$params 	= $app->getParams();
		$menu		= $app->getMenu();
		$menuitem	= $menu->getActive();
		$user		= JFactory::getUser();
		$uri 		= JFactory::getURI();

		//redirect if not logged in
		if (!$user->get('id')) {
			$app->enqueueMessage(JText::_('COM_JEM_NEED_LOGGED_IN'), 'error');
			return false;
		}

		// Load css
		JHtml::_('stylesheet', 'com_jem/jem.css', array(), true);
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #jem dd { height: 1%; }</style><![endif]-->');

		//get vars
		$filter_order		= $app->getUserStateFromRequest('com_jem.attendees.filter_order', 'filter_order', 'u.username', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.attendees.filter_order_Dir',	'filter_order_Dir',	'', 'word');
		$filter_waiting	= $app->getUserStateFromRequest('com_jem.attendees.waiting',	'filter_waiting',	0, 'int');
		$filter 			= $app->getUserStateFromRequest('com_jem.attendees.filter', 'filter', '', 'int');
		$search 			= $app->getUserStateFromRequest('com_jem.attendees.filter_search', 'filter_search', '', 'string');
		$search 			= $db->escape(trim(JString::strtolower($search)));

		// Get data from the model
		$rows      	= $this->get('Data');
		$pagination = $this->get('Pagination');
		$event 		= $this->get('Event');

		// Merge params.
		// Because this view is not useable for menu item we always overwrite $params.
		$pagetitle = JText::_('COM_JEM_MYEVENT_MANAGEATTENDEES') . ' - ' . $event->title;
		$params->set('page_heading', JText::_('COM_JEM_MYEVENT_MANAGEATTENDEES')); // event title is shown separate
		//$params->set('show_page_heading', 1); // always show?

		// Add site name to title if param is set
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$pagetitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pagetitle);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$pagetitle = JText::sprintf('JPAGETITLE', $pagetitle, $app->getCfg('sitename'));
		}

		$document->setTitle($pagetitle);

		$pathway = $app->getPathWay();
		if($menuitem) {
			$pathway->setItemName(1, $menuitem->title);
		}
		$pathway->addItem('Att:'.$event->title);

		// Emailaddress
		$enableemailaddress = $params->get('enableemailaddress', 0);

		// TODO: can be removed
		if ($enableemailaddress == 1) {
			$emailaddress = '&em='.$enableemailaddress;
		}else {
			$emailaddress = '';
		}
		$print_link = 'index.php?option=com_jem&view=attendees&layout=print&task=print&tmpl=component&id='.$event->id.$emailaddress;
		$backlink = 'attendees';
		$view = 'attendees';


		//build filter selectlist
		$filters = array();
		/* $filters[] = JHtml::_('select.option', '1', JText::_('COM_JEM_NAME')); */
		$filters[] = JHtml::_('select.option', '2', JText::_('COM_JEM_USERNAME'));
		$lists['filter'] = JHtml::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter);

		// search filter
		$lists['search'] = $search;

		// waiting list status
		$options = array(JHtml::_('select.option', 0, JText::_('COM_JEM_ATT_FILTER_ALL')),
		                 JHtml::_('select.option', 1, JText::_('COM_JEM_ATT_FILTER_ATTENDING')),
		                 JHtml::_('select.option', 2, JText::_('COM_JEM_ATT_FILTER_WAITING'))) ;
		$lists['waiting'] = JHtml::_('select.genericlist', $options, 'filter_waiting', 'class="inputbox" onChange="this.form.submit();"', 'value', 'text', $filter_waiting);

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']		= $filter_order;

		//assign to template
		$this->params		= $params;
		$this->lists 		= $lists;
		$this->enableemailaddress = $enableemailaddress;
		$this->rows 		= $rows;
		$this->pagination 	= $pagination;
		$this->event 		= $event;
		$this->pagetitle	= $pagetitle;
		$this->backlink		= $backlink;
		$this->view			= $view;
		$this->print_link	= $print_link;
		$this->item			= $menuitem;
		$this->action		= $uri->toString();

		parent::display($tpl);
	}

	/**
	 * Prepares the print screen
	 *
	 * @param $tpl
	 */
	protected function _displayprint($tpl = null)
	{
		$document	= JFactory::getDocument();
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Load css
		JHtml::_('stylesheet', 'com_jem/jem.css', array(), true);
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);
		$document->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #jem dd { height: 1%; }</style><![endif]-->');
		JHtml::_('stylesheet', 'com_jem/print.css', array(), true);
		$document->setMetaData('robots', 'noindex, nofollow');

		// Emailaddress
		$enableemailaddress = $params->get('enableemailaddress', 0);

		$rows  	= $this->get('Data');
		$event 	= $this->get('Event');

		//assign data to template
		$this->rows 		= $rows;
		$this->event 		= $event;
		$this->enableemailaddress = $enableemailaddress;

		parent::display($tpl);
	}
}
?>