<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

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
        $this->weAreDoingIt = $this->allGood();
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
            $goScript = $this->buildGoSqrScript();

            // Get current document and inject script
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($goScript, "text/javascript");

            // Only Do This once!
            $this->weAreDoingIt = false;
        }

        return true;
    }

    private function buildGoSqrScript()
    {
        $gs_js = $this->gosqr_script($this->website_GoSqr_Token);

        // Add any user properties that may be set
        $gs_js .= $this->getUserPropertiesJS();

        return $gs_js;
    }

    private function gosqr_script($token)
    {
        if ($token) {
            $gs_js = <<<gs_js
!function(g,s,q,r,d){r=g[r]=g[r]||function(){(r.q=r.q||[]).push(
  arguments)};d=s.createElement(q);q=s.getElementsByTagName(q)[0];
  d.src='//d1l6p2sc9645hc.cloudfront.net/tracker.js';q.parentNode.
  insertBefore(d,q)}(window,document,'script','_gs');

  _gs('$token');
gs_js;
        } else {
            $gs_js = '';
        }

        return $gs_js;
    }

    /**
     * Returns the User Properties setup in the plugin.
     */
    private function getUserPropertiesJS()
    {
        $user = JFactory::getUser();

        $properties = explode(',', $this->params->get('user_properties_to_track', ''));

        $userPropertiesJS = $this->buildGSUserProp($user, $properties);

        return $userPropertiesJS;
    }

    private function buildGSUserProp($user, $properties = array())
    {
        $gsUserPropJS = '';

        if (!$user->guest && is_array($properties) && count($properties)) {
            $userProfile = JUserHelper::getProfile($user->id);
            $profile = $userProfile->profile;

            foreach ($properties as $propertyKey) {
                if (isset($user->$propertyKey)) {
                    $keyValue = $user->$propertyKey;

                    switch ($propertyKey) {
                        case 'groups':
                            $keyValue = '\'' . $this->convertGroupIDsToNames($keyValue) . '\'';
                            break;
                        default:
                            $keyValue = is_string($keyValue) && !is_int($keyValue) ? "'$keyValue'" : $keyValue;
                    }

                    $gsUserPropJS .= "$propertyKey: $keyValue, ";

                } elseif (isset($profile[$propertyKey])) {
                    $keyValue = $profile[$propertyKey];
                    $keyValue = is_string($keyValue) && !is_int($keyValue) ? "'$keyValue'" : $keyValue;
                    $gsUserPropJS .= "$propertyKey: $keyValue, ";
                }
            }

            if ($gsUserPropJS != '') {
                $gsUserPropJS = "_gs('identify', { $gsUserPropJS });";
            }
        }

        return $gsUserPropJS;
    }

    private function convertGroupIDsToNames($usersGroups)
    {
        $groupNames = '';

        if (is_array($usersGroups)) {
            $allGroups = $this->getAllGroups();

            // Look for matches to build our return string.
            foreach ($allGroups as $group) {
                if (in_array($group->id, $usersGroups)) {
                    $groupNames .= $group->title . ',';
                }
            }

            if ($groupNames != '' && substr($groupNames, strlen($groupNames) - 1, 1) == ',') {
                $groupNames = substr($groupNames, 0, strlen($groupNames) - 1);
            }

        }

        return $groupNames;
    }

    /**
     * Simple bool check if we're enabled for backend or not in backend.
     *
     * @return bool
     * @throws Exception
     */
    private function allGood()
    {
        $app = JFactory::getApplication();
        $backEnd = $app->isAdmin();

        return ($this->trackAdmin && $backEnd) || !$backEnd;
    }

    /**
     * Get all group names
     *
     * @return mixed
     */
    private function getAllGroups()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__usergroups');
        $db->setQuery($query);
        $allGroups = $db->loadObjectList();
        return $allGroups;
    }
}

