<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class plgContentEasyGoSquared extends JPlugin
{
/**
 * @access      public
 * @param       object  $subject The object to observe
 * @param       array   $config  An array that holds the plugin configuration
 */
public function __construct(& $subject, $config)
{
        parent::__construct($subject, $config);

        // Strictly speaking Joomla doesn't really need you to load the language anymore.
        $this->loadLanguage();
}

/**
 * Based on the events you want to act on (within a your plugins group) you will
 * need a matching method of a form similar to this one.
 *
 * Plugin Events can be found here: http://docs.joomla.org/Plugin/Events
 *
 * Please note this is not a real event method, it's just an example of the form they take
 */
 function EventName()
 {
    $app = JFactory::getApplication();
 
        // Plugin code goes here.
        // You can access parameters via $this->params.
    return true;
 }
}
?>