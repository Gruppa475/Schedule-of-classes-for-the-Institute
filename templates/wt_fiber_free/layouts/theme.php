<?php
/**
* @package   Joomla Templates
* @author    WarpTheme http://www.warptheme.com
* @copyright Copyright (C) WarpTheme
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get theme configuration
include($this['path']->path('layouts:theme.config.php'));

?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"  data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

<head>
  <?php echo $this['template']->render('head');
  include($this['path']->path('layouts:preloader.php')); ?>
</head>

<body class="<?php echo $this['config']->get('body_classes'); ?>">
  <div class="body-innerwrapper">
  <?php if ($this['widgets']->count('toolbar')) : ?>
    <div class="tm-block-toolbar uk-hidden-small">
      <div class="uk-container uk-container-center">
        <div class="tm-toolbar-container">
          <?php echo $this['widgets']->render('toolbar'); ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('logo + menu + search + headerbar')) : ?>
    <div class="tm-navbar uk-navbar">
      <div class="uk-container uk-container-center">
        <nav class="uk-flex uk-flex-middle uk-flex-center uk-flex-space-between">
          <?php if ($this['widgets']->count('logo')) : ?>
            <a class="tm-logo uk-hidden-small" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['widgets']->render('logo'); ?></a>
          <?php endif; ?>
          <?php if ($this['widgets']->count('menu')) : ?>
            <div class="tm-nav uk-hidden-small">
              <?php echo $this['widgets']->render('menu'); ?>
            </div>
          <?php endif; ?>
          <?php if ($this['widgets']->count('search + headerbar')) : ?>
            <div class="tm-headerbar uk-navbar-flip uk-visible-large">
              <?php echo $this['widgets']->render('headerbar'); ?>
            </div>
            <div class="tm-search uk-navbar-flip uk-hidden-small">
              <?php echo $this['widgets']->render('search'); ?>
            </div>
          <?php endif; ?>
          <?php if ($this['widgets']->count('offcanvas')) : ?>
            <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
          <?php endif; ?>
          <?php if ($this['widgets']->count('logo-small')) : ?>
            <div class="uk-navbar-content uk-navbar-center uk-visible-small">
              <a class="uk-responsive-width uk-responsive-height" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['widgets']->render('logo-small'); ?></a>
            </div>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('top-a')) : ?>
    <div id="tm-top-a" class="tm-block-top-a uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.top-a']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('top-a', array('layout'=>$this['config']->get('grid.top-a.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('top-b')) : ?>
    <div id="tm-top-b" class="tm-block-top-b uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.top-b']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('top-b', array('layout'=>$this['config']->get('grid.top-b.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('top-c')) : ?>
    <div id="tm-top-c" class="tm-block-top-c uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.top-c']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('top-c', array('layout'=>$this['config']->get('grid.top-c.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('top-d')) : ?>
    <div id="tm-top-d" class="tm-block-top-d uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.top-d']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('top-d', array('layout'=>$this['config']->get('grid.top-d.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('main-top + main-bottom + sidebar-a + sidebar-b') || $this['config']->get('system_output', true)) : ?>
    <div id="tm-main" class="tm-block-main uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <div class="tm-middle uk-grid" data-uk-grid-match data-uk-grid-margin>
          <?php if ($this['widgets']->count('main-top + main-bottom') || $this['config']->get('system_output', true)) : ?>
            <div class="<?php echo $classes['layout.main'] ?>">
              <?php if ($this['widgets']->count('main-top')) : ?>
                <section id="tm-main-top" class="tm-main-top <?php echo $classes['grid.main-top']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
                  <?php echo $this['widgets']->render('main-top', array('layout'=>$this['config']->get('grid.main-top.layout'))); ?>
                </section>
              <?php endif; ?>
              <?php if ($this['config']->get('system_output', true)) : ?>
                <main id="tm-content" class="tm-content">
                  <?php if ($this['widgets']->count('breadcrumbs')) : ?>
                    <?php echo $this['widgets']->render('breadcrumbs'); ?>
                  <?php endif; ?>
                  <?php echo $this['template']->render('content'); ?>
                </main>
              <?php endif; ?>
              <?php if ($this['widgets']->count('main-bottom')) : ?>
                <section id="tm-main-bottom" class="tm-main-bottom <?php echo $classes['grid.main-bottom']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
                  <?php echo $this['widgets']->render('main-bottom', array('layout'=>$this['config']->get('grid.main-bottom.layout'))); ?>
                </section>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <?php foreach($sidebars as $name => $sidebar) : ?>
            <aside class="<?php echo $classes["layout.$name"] ?>"><?php echo $this['widgets']->render($name) ?></aside>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-a')) : ?>
    <div id="tm-bottom-a" class="tm-block-bottom-a uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-a']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-a', array('layout'=>$this['config']->get('grid.bottom-a.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-b')) : ?>
    <div id="tm-bottom-b" class="tm-block-bottom-b uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-b']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-b', array('layout'=>$this['config']->get('grid.bottom-b.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-c')) : ?>
    <div id="tm-bottom-c" class="tm-block-bottom-c uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-c']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-c', array('layout'=>$this['config']->get('grid.bottom-c.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-d')) : ?>
    <div id="tm-bottom-d" class="tm-block-bottom-d uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-d']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-d', array('layout'=>$this['config']->get('grid.bottom-d.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-e')) : ?>
    <div id="tm-bottom-e" class="tm-block-bottom-e uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-e']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-e', array('layout'=>$this['config']->get('grid.bottom-e.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-f')) : ?>
    <div id="tm-bottom-f" class="tm-block-bottom-f uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-f']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-f', array('layout'=>$this['config']->get('grid.bottom-f.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('bottom-g')) : ?>
    <div id="tm-bottom-g" class="tm-block-bottom-g uk-block uk-block-default">
      <div class="uk-container uk-container-center">
        <section class="<?php echo $classes['grid.bottom-g']; ?>" data-uk-grid-match="{target:'> div > .uk-panel'}" data-uk-grid-margin>
          <?php echo $this['widgets']->render('bottom-g', array('layout'=>$this['config']->get('grid.bottom-g.layout'))); ?>
        </section>
      </div>
    </div>
  <?php endif; ?>
  <?php if ($this['widgets']->count('footer + debug') || $this['config']->get('warp_branding', true) || $this['config']->get('totop_scroller', true)) : ?>
    <footer id="tm-footer" class="tm-footer">
      <div class="uk-container uk-container-center uk-margin-large">
        <?php
        echo $this['widgets']->render('footer');
        $this->output('warp_branding');
        echo $this['widgets']->render('debug');
        ?>
        <?php if ($this['config']->get('totop_scroller', true)) : ?>
          <a class="tm-totop-scroller uk-margin" data-uk-smooth-scroll href="#"></a>
        <?php endif; ?>
      </div>
    </footer>

  <?php endif; ?>
  <?php if ($this['widgets']->count('offcanvas')) : ?>
    <div id="offcanvas" class="uk-offcanvas">
      <div class="uk-offcanvas-bar"><?php echo $this['widgets']->render('offcanvas'); ?></div>
    </div>
  <?php endif; ?>
  <?php echo $this->render('footer'); ?>
</div>
</body>
</html>
