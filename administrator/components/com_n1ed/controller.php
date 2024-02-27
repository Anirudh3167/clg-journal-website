<?php

/**
 * @version 4.0.15
 * @author N1ED
 * @copyright Copyright (c) 2024 EdSDK (https://n1ed.com/). All rights reserved.
 * @license GNU General Public License version 3 or later
 */

defined('_JEXEC') or die(); // No direct access

class N1edController extends JControllerLegacy
{
    /**
     * flmngr Method to display a file manager.
     *
     * @return json string or null
     */
    public function flmngr()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.edit', 'com_n1ed') == true) {
            require JPATH_SITE .
                '/components/com_n1ed/libraries/file-manager/flmngr.php';
        }
        die();
    }

    /**
     *  Method for setting API key and access token from N1ED in the article
     */
    public function setApiKey()
    {
        $user = JFactory::getUser();
        if ($user->authorise('core.edit', 'com_n1ed') == true) {
            // Load the current component params.
            $params = JComponentHelper::getParams('com_n1ed');
            // Set new value of param(s)
            $params->set('api_key', $_POST['n1edApiKey'] . ':' . $_POST['n1edToken']);

            // Save the parameters
            $componentid = JComponentHelper::getComponent('com_n1ed')->id;
            $table = JTable::getInstance('extension');
            $table->load($componentid);
            $table->bind(['params' => $params->toString()]);

            // check for error
            if (!$table->check()) {
                http_response_code(500);
                echo $table->getError();
                die();
            }
            // Save to database
            if (!$table->store()) {
                http_response_code(500);
                echo $table->getError();
                die();
            }
        }

        http_response_code(200);
        die();
    }

}
