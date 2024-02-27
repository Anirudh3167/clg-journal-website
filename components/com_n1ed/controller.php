<?php
defined('_JEXEC') or die(); // No direct access

/**
 * @version 4.0.15
 * @author N1ED
 * @copyright Copyright (c) 2024 EdSDK (https://n1ed.com/). All rights reserved.
 * @license GNU General Public License Version 3 or later
 */
class N1edController extends JControllerLegacy
{
    /**
     * flmngr Method to display a file manager.
     * @return json string or null
     */
    public function flmngr()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.edit', 'com_n1ed')) {
            require JPATH_BASE .
                '/components/com_n1ed/libraries/file-manager/flmngr.php';
        }
        die();
    }

}
