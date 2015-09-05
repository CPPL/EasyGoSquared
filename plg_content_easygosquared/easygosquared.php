<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/easygosquaredhelper.php';

/**
 *
 * @author Craig Phillips
 * @copyright Copyright © 2015 Craig Phillips Pty Ltd - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE file
 *
 * This plugin is based off the CPPL Skeleton Plugin which you can find on GitHub to
 * build your own Joomla plugins. https://github.com/cppl/Skeleton-Plugin-for-Joomla
 *
 */

class plgContentEasyGoSquared extends JPlugin
{
    protected $trackAdmin;

    protected $website_GoSqr_Token;

    protected $enabledUserProperties;

    protected $weAreDoingIt;

    /**
     * @access      public
     * @param       object $subject The object to observe
     * @param       array $config An array that holds the plugin configuration
     */
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);

        // Strictly speaking Joomla doesn't really need you to load the language anymore.
        $this->loadLanguage();

        // Load some settings
        $this->website_GoSqr_Token = $this->params->get('gosqr_token', 'GSN-000000-X');
        $this->trackAdmin = $this->params->get('track_admin', 0);
        $this->weAreDoingIt = EasyGoSquaredHelper::allGood($this->trackAdmin);
    }

    /**
     * Based on the events you want to act on (within a your plugins group) you will
     * need a matching method of a form similar to this one.
     *
     * Plugin Events can be found here: http://docs.joomla.org/Plugin/Events
     *
     * Please note this is not a real event method, it's just an example of the form they take
     */
    function onContentPrepare()
    {
        if ($this->weAreDoingIt) {
            // Build the Script
            $goScript = EasyGoSquaredHelper::buildGoSqrScript($this->params, $this->website_GoSqr_Token);

            // Get current document and inject script
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($goScript, "text/javascript");

            // Only Do This once!
            $this->weAreDoingIt = false;
        }

        return true;
    }

}

