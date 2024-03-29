<?php
/**
* @package   Joomla Templates
* @author    WarpTheme http://www.warptheme.com
* @copyright Copyright (C) WarpTheme
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

return array(

    'path' => array(
        'theme'   => array(__DIR__),
        'js'      => array(__DIR__.'/js'),
        'css'     => array(__DIR__.'/css'),
        'less'    => array(__DIR__.'/less'),
        'layouts' => array(__DIR__.'/layouts')
    ),

    'less' => array(

        'vars' => array(
            'less:theme.less'
        ),

        'files' => array(
            '/css/theme.css'     => 'less:theme.less',
            '/css/bootstrap.css' => 'less:bootstrap.less'
        )

    ),

    'cookie' => $cookie = md5(__DIR__),

    'customizer' => isset($_COOKIE[$cookie])

);
