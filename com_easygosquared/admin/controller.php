<?php
/**
 * @package    EasyGoSquared
 * @author     Craig Phillips <craig@craigphillips.biz>
 * @copyright  2015 Craig Phillips Pty Ltd.
 * @license    GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @url        http://www.seepeoplesoftware.com
 */

defined('_JEXEC') or die('Restricted access');

/**
 * EasyGoSquared Component Controller
 *
 * @package EasyGoSquared
 *
 * @since 1.2
 */
class EasyGoSquaredController extends JControllerLegacy
{
    /**
     * The default view.
     *
     * @var string
     */
    protected $default_view = 'controlpanel';


    /**
     * Method to display a view.
     *
     * @param boolean $cachable  If true, the view output will be cached
     * @param mixed   $urlparams An array of safe url parameters and their variable
     *                           types, for values see {@link JFilterInput::clean()}.
     *
     * @return JController  $this object to support chaining.
     */
    public function display($cachable = false, $urlparams = false)
    {
        parent::display($cachable);

        return $this;
    }
}
