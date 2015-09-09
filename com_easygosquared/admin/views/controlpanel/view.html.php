<?php
/**
 * Joomla Integration for the GoSquared Analytics platform.
 *
 * @package   EasyGoSquared
 * @author    Craig Phillips <craig@craigphillips.biz>
 * @copyright 2015 Craig Phillips Pty Ltd.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @url       http://www.seepeoplesoftware.com
 *
 */

// No Direct Access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 * Records View
 *
 * @since 1.2
 */
class EasyGoSquaredViewControlPanel extends JViewLegacy
{
    protected $params;
    protected $api_key;
    protected $token;
    protected $start_view;
    protected $body;

    /**
     * View display method
     *
     * @param string $tpl Template file to use.
     *
     * @return void
     *
     * @since 1.2
     **/
    public function display($tpl = null)
    {
        EasyGoSquaredContentHelper::addSubmenu('controlpanel');

        // Get our component params.
        $this->params     = JComponentHelper::getParams('com_easygosquared');
        $this->api_key    = $this->params->get('api_key', false);
        $this->token      = $this->params->get('token', false);
        $this->start_view = $this->params->get('start_view', 'now');

        // Check for an overriding value.
        $vo = JFactory::getApplication()->input->get('start_view', false);
        $this->start_view = $vo ? $vo : $this->start_view;

        // Create content
        $this->body       = $this->_createContent();

        // Setup layout, toolbar, js, css
        $this->_addToolbar();
        $this->_addCSSEtc();

        $this->sidebar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    /**
     * Sets up our toolbar for the view.
     *
     * @return void
     *
     * @since 1.2
     */
    private function _addToolbar()
    {
        // Setup the Toolbar
        $toolbar = JToolbar::getInstance('toolbar');
        JToolBarHelper::title(
            JText::_('COM_EASYGOSQUARED_VIEW_TITLE'),
            'easygosquared'
        );

        // Get our User
        $user = JFactory::getUser();

        // Views
        // Instantiate a new JLayoutFile instance
        $layout = new JLayoutFile('joomla.toolbar.link');

        // Now
        $title = JText::_('COM_EASYGOSQUARED_VIEW_NOW');
        $displayData = array(
            'text' => $title,'class' => '',
            'doTask' => 'index.php?option=com_easygosquared&start_view=now'
        );

        $dhtml = $layout->render($displayData);
        $toolbar->appendButton('Custom', $dhtml);

        // Trends
        $title = JText::_('COM_EASYGOSQUARED_VIEW_TRENDS');
        $displayData = array(
            'text' => $title,'class' => '',
            'doTask' => 'index.php?option=com_easygosquared&start_view=trends'
        );

        $dhtml = $layout->render($displayData);
        $toolbar->appendButton('Custom', $dhtml);

        // Ecommerce
        $title = JText::_('COM_EASYGOSQUARED_VIEW_ECOM');
        $displayData = array(
            'text' => $title,'class' => '',
            'doTask' => 'index.php?option=com_easygosquared&start_view=ecommerce'
        );

        $dhtml = $layout->render($displayData);
        $toolbar->appendButton('Custom', $dhtml);

        // Can we do?
        $canDo = JHelperContent::getActions('com_easygosquared', '', 0);

        if ($user->authorise('core.admin', 'com_easygosquared')
            || $user->authorise('core.options', 'com_easygosquared')
        ) {
            JToolbarHelper::preferences('com_easygosquared');
        }
    }

    /**
     * Adds CSS file to the document head .
     *
     * @return void
     *
     * @since 1.2
     */
    private function _addCSSEtc()
    {
        // Use minified files if not debugging.
        $minOrNot = !JDEBUG ? '.min' : '';

        // Get the document object
        $document = JFactory::getDocument();

        // Add CSS to the document
        $styleSheet =  JURI::root();
        $styleSheet .= "media/com_easygosquared/css/easygosquared$minOrNot.css";
        $document->addStyleSheet($styleSheet);

    }

    /**
     * Assemble the body.
     *
     * @return string
     */
    private function _createContent()
    {
        if ($this->api_key && $this->token) {
            $srcURL = 'https://www.gosquared.com/labs/_embed/?api_key='
                . $this->api_key . '&site_token='
                . $this->token . '&dashboard='
                . $this->start_view;
            $content = <<<content
<iframe src="$srcURL"
        style="height:100em;width:100%" height="100%
        width="100%" height="100%"></iframe>
content;
        } else {
            $msg = JText::_('COM_EASYGOSQUARED_NOT_CONFIGURED_MSG');
            $content = <<<content
<div class="egs_no_settings_msg" id="egs_no_settings_msg" >$msg</div>
content;
        }

        return $content;
    }
}
