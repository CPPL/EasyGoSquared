<?php
/**
 * @package    EasyGoSquared
 * @author     Craig Phillips <craig@craigphillips.biz>
 * @copyright  2015 Craig Phillips Pty Ltd.
 * @license    GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @url        http://www.seepeoplesoftware.com
 */

defined('_JEXEC') or die('Restricted Access');

if (!JFactory::getUser()->authorise('core.manage', 'com_easygosquared')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('EasygoSquaredContentHelper', __DIR__ . '/helpers/easygosquared.php');

$controller = JControllerLegacy::getInstance('EasyGoSquared');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
