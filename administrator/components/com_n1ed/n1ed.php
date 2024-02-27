<?php
defined('_JEXEC') or die(); // No direct access
/**
 * @version 4.0.15
 * @author N1ED
 * @copyright Copyright (c) 2024 EdSDK (https://n1ed.com/). All rights reserved.
 * @license GNU General Public License Version 3 or later
 */

$task = JFactory::getApplication()->input->get('task', '');

if (!empty($task)) {
    $controller = JControllerLegacy::getInstance('n1ed');
    $controller->execute($task);
    $controller->redirect();
} else {
    $link = 'index.php?option=com_config&view=component&component=com_n1ed';
    $mainframes = JFactory::getApplication();
    $link = JRoute::_("$link", false);
    $mainframes->redirect($link);
}
