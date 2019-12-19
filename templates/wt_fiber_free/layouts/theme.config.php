<?php
/**
* @package   Joomla Templates
* @author    WarpTheme http://www.warptheme.com
* @copyright Copyright (C) WarpTheme
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$config  = $this['config'];
$classes = array();

/*
 * Layouts
 */
$width = 60;
foreach ($sidebars = $config->get('sidebars', array()) as $name => $sidebar) {
    if (!$this['widgets']->count($name)) {
        unset($sidebars[$name]);
        continue;
    }
    $width -= @$sidebar['width'];
}
foreach (($sidebars + array('main'=> array('width' => $width))) as $name => $column) {
    $classes["layout.$name"][] = sprintf('tm-%s uk-width-medium-%s%s', $name, GridHelper::getFraction(@$column['width']), @$column['first'] ? ' uk-flex-order-first-medium' : '');
}
if ($count = count($sidebars)) {
    $classes['body'][] = 'tm-sidebars-'.$count;
}

/*
 * Grid
 */
$displays  = array('small', 'medium', 'large');
foreach (array_keys($config->get('grid', array())) as $name) {
    $grid = array("tm-{$name} uk-grid");
    if ($this['config']->get("grid.{$name}.divider", false)) {
        $grid[] = 'uk-grid-divider';
    }
    $widgets = $this['widgets']->load($name);
    foreach($displays as $display) {
        if (!array_filter($widgets, function($widget) use ($config, $display) { return (bool) $config->get("widgets.{$widget->id}.display.{$display}", true); })) {
            $grid[] = "uk-hidden-{$display}";
        }
    }
    $classes["grid.$name"] = $grid;
}


/*
 * Add body classes
 */
$classes['body'][] = $this['system']->isBlog() ? 'tm-isblog' : 'tm-noblog';
$classes['body'][] = $config->get('page_class');
$classes['body'][] = ' layout-'.$this['config']->get('layout_width');
$classes['body'][] = ' '.$config->get('article');
$classes['body'][] = $this['config']->get('page_title') ? 'tm-page-title-false' : '';

$classes = array_map(function($array) { return implode(' ', $array); }, $classes);

/*
 * Add body classes to config
 */
$config->set('body_classes', trim($classes['body']));
/*
 * Add social buttons
 */
$config->set('body_config', json_encode(array(
    'twitter'  => (int) $config->get('twitter', 0),
    'plusone'  => (int) $config->get('plusone', 0),
    'facebook' => (int) $config->get('facebook', 0),
    'style'    => $config->get('style')
)));

/*
 * Add assets
 */

// add css
$this['asset']->addFile('css', 'css:theme.css');
$this['asset']->addFile('css', 'css:custom.css');

// add scripts
$this['asset']->addFile('js', 'js:uikit.js');
$this['asset']->addFile('js', 'warp:vendor/uikit/js/components/search.js');
$this['asset']->addFile('js', 'warp:vendor/uikit/js/components/autocomplete.js');
$this['asset']->addFile('js', 'warp:vendor/uikit/js/components/tooltip.js');
$this['asset']->addFile('js', 'js:social.js');
$this['asset']->addFile('js', 'js:theme.js');

if (isset($head)) {
    $this['template']->set('head', implode("\n", $head));
}

class GridHelper
{
    public static function gcf($a, $b = 60) {
        return (int) ($b > 0 ? self::gcf($b, $a % $b) : $a);
    }
    public static function getFraction($nominator, $divider = 60)  {
        $factor = self::gcf($nominator, $divider);
        return $nominator / $factor .'-'. $divider / $factor;
    }
}
