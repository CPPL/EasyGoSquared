<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 *
 * @author Craig Phillips
 * @copyright Copyright © 2015 Craig Phillips Pty Ltd - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE file
 *
 */
class EasyGoSquaredHelper
{
    public static function buildGoSqrScript($params, $website_GoSqr_Token)
    {
        $gs_js = self::gosqr_script($website_GoSqr_Token);

        // Add any user properties that may be set
        $gs_js .= self::getUserPropertiesJS($params);

        return $gs_js;
    }

    private static function gosqr_script($token)
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
    private static function getUserPropertiesJS($params)
    {
        $user = JFactory::getUser();

        $properties = explode(',', $params->get('user_properties_to_track', ''));

        $userPropertiesJS = self::buildGSUserProp($user, $properties);

        return $userPropertiesJS;
    }

    private static function buildGSUserProp($user, $properties = array())
    {
        $gsUserPropJS = '';

        if (!$user->guest && is_array($properties) && count($properties)) {
            /**@PROBLOCK_START@**/
            $j3 = version_compare( JVERSION, '3.0', '<' ) != 1;
            $isFrontEnd = !JFactory::getApplication()->isAdmin();

            if ($j3 && $isFrontEnd) {
                $userProfile = JUserHelper::getProfile($user->id);
                $profile = $userProfile->profile;
            }
            /**@PROBLOCK_END@**/

            foreach ($properties as $propertyKey) {
                if (isset($user->$propertyKey)) {
                    $keyValue = $user->$propertyKey;

                    switch ($propertyKey) {
                        case 'groups':
                            $keyValue = '\'' . self::convertGroupIDsToNames($keyValue) . '\'';
                            break;
                        default:
                            $keyValue = is_string($keyValue) && !is_int($keyValue) ? "'$keyValue'" : $keyValue;
                    }

                    $gsUserPropJS .= "$propertyKey: $keyValue, ";

                }/**@PROBLOCK_START@**/ elseif ($j3 && isset($profile[$propertyKey])) {
                    $keyValue = $profile[$propertyKey];
                    $keyValue = is_string($keyValue) && !is_int($keyValue) ? "'$keyValue'" : $keyValue;
                    $gsUserPropJS .= "$propertyKey: $keyValue, ";
                }/**@PROBLOCK_END@**/
            }

            if ($gsUserPropJS != '') {
                $gsUserPropJS = "_gs('identify', { $gsUserPropJS });";
            }
        }

        return $gsUserPropJS;
    }

    private static function convertGroupIDsToNames($usersGroups)
    {
        $groupNames = '';

        if (is_array($usersGroups)) {
            $allGroups = self::getAllGroups();

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
    public static function allGood($trackAdmin)
    {
        $app = JFactory::getApplication();
        $backEnd = $app->isAdmin();

        return ($trackAdmin && $backEnd) || !$backEnd;
    }

    /**
     * Get all group names
     *
     * @return mixed
     */
    private static function getAllGroups()
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
