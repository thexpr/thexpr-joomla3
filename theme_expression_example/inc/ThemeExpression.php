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


class ThemeExpression {

    public $path;
    public $sitepath;
    public $params;
    public $region_widths;
    public $wstyles;

    public function __construct() {
        $template = JFactory::getApplication()->getTemplate(true);
        $this->path = JUri::base() . 'templates/' . $template->template;
        $this->sitepath = substr(JUri::base(), 0, -1);
        $this->params = $template->params->toArray();

        $this->region_widths = array();
        if (isset($this->params['region_widths'])) {
            foreach (explode("\n", str_replace("\r", '', $this->params['region_widths'])) as $line) {
                $e = explode(':', $line, 2);
                $k = strtolower(trim(@$e[0]));
                $v = trim(@$e[1]);
                if (preg_match('/^[a-z0-9_]+$/', $k) && ctype_digit($v)) {
                    $this->region_widths[$k] = intval($v);
                }
            }

            unset($this->params['region_widths']);
        }

        $this->wstyles = array();
        require(dirname(__FILE__) . '/wstyles.php');
    }

    public function tt($msg) {
        return JText::_($msg);
    }

    public function show_body() {
        return true;
    }

    public function get_header() {
        return
'<jdoc:include type="head" />
';
    }

    function get_message() {
        return
'<jdoc:include type="message" />
';
    }

    public function get_body($add_wrapper = true, $add_message = true) {
        $result =
'<jdoc:include type="component" />
';

        if ($add_message) {
            $result = $this->get_message() . $result;
        }

        if ($add_wrapper) {
            $result =
'<div id="mainbody" class="mainbody"' . (empty($this->region_widths['mainbody']) ? '' : ' style="width:' . $this->region_widths['mainbody'] . 'px"') . '>
' . $result . '</div>
';
        }

        return $result;
    }

    public function get_widget($title, $body, $wstyle = '') {
        // helper for /html/modules.php

        $wstyle = trim((string) $wstyle);
        $title = trim((string) $title);

        // system default format
        $result =
'<div class="wtitle"><[title]></div>
<[body]>
';

        // eliminate non-existing style
        if ($wstyle != '' && !isset($this->wstyles[$wstyle])) {
            $wstyle = '';
        }

        // adapt custom default, if it exists
        if ($wstyle == '' && isset($this->wstyles['default'])) {
            $wstyle = 'default';
        }

        // decide on the version of the style
        if ($wstyle != '') {
            if ($title == '') {
                if (isset($this->wstyles[$wstyle]['title0'])) {
                    $result = $this->wstyles[$wstyle]['title0'];
                }
                else if (isset($this->wstyles[$wstyle]['title1'])) {
                    $result = $this->wstyles[$wstyle]['title1'];
                }
            }
            else {
                if (isset($this->wstyles[$wstyle]['title1'])) {
                    $result = $this->wstyles[$wstyle]['title1'];
                }
                else if (isset($this->wstyles[$wstyle]['title0'])) {
                    $result = $this->wstyles[$wstyle]['title0'];
                }
            }
        }

        // inject data
        $result = str_replace('<[path]>', $this->path, $result);
        $result = str_replace('<[sitepath]>', $this->sitepath, $result);
        $result = str_replace('<[wstyle]>', $wstyle, $result);
        $result = str_replace('<[title]>', htmlspecialchars($title), $result);
        $result = str_replace('<[body]>', $body, $result);

        return
'<div class="widget' . ($wstyle == '' ? '' : ' widget-' . $wstyle) . '">
' . $result . '
</div>
';
    }

    public function td_width($region_name, $shrink = false) {
        if (is_array($region_name)) {
            $max = 0;
            foreach ($region_name as $name) {
                if (intval(@$this->region_widths[$name]) > $max) {
                    $max = $this->region_widths[$name];
                }
            }

            if ($max > 0) {
                return ' width="' . $max . '"';
            }
        }
        else {
            if (isset($this->region_widths[$region_name])) {
                return ' width="' . $this->region_widths[$region_name] . '"';
            }
        }

        return $shrink ? ' style="width:1%; white-space:nowrap;"' : '';
    }

    public function region_count($region_name) {
        static $results = array();

        $result = @$results[$region_name];
        if ($result === null) {
            $result = JFactory::getDocument()->countModules($region_name);
            $results[$region_name] = $result;
        }

        return $result;
    }

    public function get_region($region_name, $collapsible = true, $clearfix = false) {
        $result = '';
        if (!$collapsible || $this->region_count($region_name)) {
            $width = isset($this->region_widths[$region_name]) ? ' style="width:' . $this->region_widths[$region_name] . 'px"' : '';
            $result =
'<div id="region_' . $region_name . '" class="region_' . $region_name . '"' . $width . '>
<jdoc:include type="modules" name="' . $region_name . '" style="thexpr" />
</div>
';

            if ($clearfix) {
                $result .=
'<div class="clearfix"></div>
';
            }
        }
        return $result;
    }

    public function get_regions($region_names, $spacer = '', $spacer_down = true, $clearfix = true, $id = '', $tdgap = false) {
        $result = '';
        $regions = array();
        for ($i = 0, $len = count($region_names); $i < $len; $i++) {
            if ($this->region_count($region_names[$i])) {
                $regions[] = $region_names[$i];
            }
        }

        if (empty($regions)) {
            return;
        }

        $count = count($regions);

        if ($spacer != '' && !$spacer_down) {
            $result .= '<div id="' . $spacer . '"></div>' . "\n";
        }

        if ($count > 1) {
            $result .= '<table ' . ($id == '' ? '' : ' id="' . $id . '"') . 'cellspacing="0" cellpadding="0" width="100%"><tr>' . "\n";
        }

        if ($tdgap === false) {
            $tdgap = '<td class="tdgap"></td>' . "\n";
        }

        $first = true;
        foreach ($regions as $region_name) {
            if ($first) {
                $first = false;
            }
            else {
                $result .= $tdgap;
            }

            if ($count > 1) {
                $result .= '<td valign="top"' . $this->td_width($region_name) . '>' . "\n";
            }

            $result .= $this->get_region($region_name, true, $clearfix);

            if ($count > 1) {
                $result .= '</td>' . "\n";
            }
        }

        if ($count > 1) {
            $result .= '</tr></table>' . "\n";
        }

        if ($spacer != '' && $spacer_down) {
            $result .= '<div id="' . $spacer . '"></div>' . "\n";
        }

        return $result;
    }

    public function get_hidden_region($region_name) {
        $result = '';
        if ($this->region_count($region_name)) {
            $result =
'<div id="region_' . $region_name . '" class="region_' . $region_name . '" style="display:none">
<jdoc:include type="modules" name="' . $region_name . '" style="thexpr" />
</div>
';
        }
        return $result;
    }

}

global $thexpr;
$thexpr = new ThemeExpression();

?>