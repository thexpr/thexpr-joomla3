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


// Widget styles
//
// Usage: $this->wstyles['<widget name>']['<title1 or title0>'] = '';
// 
// Replace <widget name> with the name for your widget style.
//
// Replace <title1 or title0> with "title1" if your style contains a title, or "title0" if it doesn't.
// You may write two styles for the same widget name, one with title1 and another with title0.
// The system will automatically pick one depending on whether the user has set a title for the widget and enabled/disabled it.
//
// Content variables:
// <[path]> = Path to the template
// <[sitepath]> = Path to the site
// <[wstyle]> = Widget style name aka Module suffix
// <[title]> = Widget title
// <[body]> = Widget body


$this->wstyles['default']['title0'] =
'<[body]>
';

$this->wstyles['default']['title1'] =
'<div class="wtitle"><[title]></div>
<[body]>
';

