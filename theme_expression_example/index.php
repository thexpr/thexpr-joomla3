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


require_once(dirname(__FILE__) . '/inc/ThemeExpression.php');

$document = JFactory::getDocument();

$document->addStyleSheet($thexpr->path . '/css/template.css');

$page_width = intval(@$thexpr->params['page_width']);
if ($page_width > 0) {
    $document->addStyleDeclaration('.wrapper { width: ' . $page_width . 'px; }');
}

echo
'<!DOCTYPE html>
<html>
<head>
' . $thexpr->get_header() . '
</head>

<body>

<div class="wrapper">

' . $thexpr->get_region('header') . '

' . ($thexpr->region_count('left') ? $thexpr->get_region('left') : '') . '

' . $thexpr->get_body() . '

' . ($thexpr->region_count('right') ? $thexpr->get_region('right') : '') . '

' . $thexpr->get_region('footer') . '

</div>

' . $thexpr->get_region('debug') . '
</body>

</html>
';
