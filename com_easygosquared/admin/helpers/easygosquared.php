<?php
/**
 * @package     EasyGoSquared
 *
 * @copyright   2015 Craig Phillips Pty Ltd.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * EasyGoSquared component helper.
 *
 * @since  1.2
 */
class EasyGoSquaredContentHelper extends JHelperContent
{
    public static $extension = 'com_easygosquared';

    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     *
     * @since   1.6
     */
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_EASYGOSQUARED_VIEW_NOW'),
            'index.php?option=com_content&start_view=now',
            $vName == 'controlpanel'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_EASYGOSQUARED_VIEW_TRENDS'),
            'index.php?option=com_content&start_view=trends',
            $vName == 'controlpanel'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_EASYGOSQUARED_VIEW_ECOM'),
            'index.php?option=com_content&start_view=ecommerce',
            $vName == 'controlpanel'
        );
    }
}
