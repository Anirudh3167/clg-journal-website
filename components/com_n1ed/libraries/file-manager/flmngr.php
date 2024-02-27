<?php

/**
 * N1ED extension for Joomla 3
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

defined('_JEXEC') or die('Restricted access');

require __DIR__ . '/vendor/autoload.php';

use EdSDK\FlmngrServer\FlmngrServer;

clearstatcache();

$site = JFactory::getApplication()->input->get('site', '');

if ($site == 'admin') {
    $dirs = JPATH_SITE . '/';
    $path_to = '../';
} else {
    $dirs = '';
    $path_to = '';
}


$dir = $dirs . 'images';
if (!is_dir("$dir"))
    mkdir("$dir", 0755, true); // create dir for joomla root

$dir = $dirs . 'images/n1ed_tmp';
if (!is_dir("$dir"))
    mkdir("$dir", 0755, true); // create dir for joomla root

$dir = $dirs . 'cache/n1ed_cache';
if (!is_dir("$dir"))
    mkdir("$dir", 0755, true); // create dir for joomla root


FlmngrServer::flmngrRequest(
    array(
        'dirFiles' => $path_to . 'images',
        'dirTmp' => $path_to . 'images/n1ed_tmp',
        'dirCache' => $path_to . 'cache/n1ed_cache'
    )
);