<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

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

class plgSystemEasyGoSquared extends JPlugin
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

        if ($this->installedProperly()) {
            // Load some settings
            $this->website_GoSqr_Token = $this->params->get('gosqr_token', 'GSN-000000-X');
            $this->trackAdmin = $this->params->get('track_admin', 0);
            $this->weAreDoingIt = EasyGoSquaredHelper::allGood($this->trackAdmin);
        } else {
            // JText doesn't work here... no language system yet.
            JFactory::getApplication()->enqueueMessage('EasyGoSquared Plugin incorrectly installed', 'WARNING');
            return null;
        }
    }

    /**
     * In the Admin Joomla we can use a content plugin so before the <head> tag is compiled we can add
     * the same script into the head if Admin analytics are enabled.
     *
     * @return bool
     */
    function onBeforeCompileHead()
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

    /**
     * A simple check to makes sure the plugins are installed correctly before
     * using the helper file functions (it also loads the helper file :))
     *
     * @return bool
     */
    private function installedProperly() {
        $path_to_helper = JPATH_PLUGINS . '/content/easygosquared/easygosquaredhelper.php';

        if (file_exists($path_to_helper)) {
            // Yes! Now lets get the helper
            require_once $path_to_helper;

            if (class_exists('EasyGoSquaredHelper')) {
                // Looks like the real helper, so lets try and get the content settings
                $params = new Joomla\Registry\Registry();
                $plugin = JPluginHelper::getPlugin('content', 'easygosquared');

                if ($plugin && isset($plugin->params)) {
                    // We have a plug-in whoa :D
                    $params->loadString($plugin->params);
                    if (isset($this->params) && is_object($this->params)) {
                        $this->params->merge($params);
                    } else {
                        $this->params = $params;
                    }
                    return true;
                }
            }
        }

        // Ok if we got here something went wrong.
        return false;
    }
}

