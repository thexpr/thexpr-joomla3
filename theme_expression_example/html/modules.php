<?php

/**
 * Theme Expression
 *
 * @version 1.0
 * @author Creative Pulse
 * @copyright Creative Pulse 2013
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link http://www.creativepulse.gr
 */


function modChrome_thexpr($module, &$params, &$attribs) {
    echo $GLOBALS['thexpr']->get_widget(
        $module->showtitle ? $module->title : '',
        $module->content,
        $params->get('moduleclass_sfx')
    );
}
