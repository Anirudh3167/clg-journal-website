<?php
defined( '_JEXEC' ) or die;// No direct access
/**
 * @version 4.0.15
 * @author N1ED
 * @copyright Copyright (c) 2024 EdSDK (https://n1ed.com/). All rights reserved.
 * @license GNU General Public License Version 3 or later
*/

$controller = JControllerLegacy::getInstance( 'n1ed' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();