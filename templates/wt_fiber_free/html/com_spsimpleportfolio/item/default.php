<?php
/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2015 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

require_once JPATH_COMPONENT . '/helpers/helper.php';
SpsimpleportfolioHelper::generateMeta($this->item);


$doc = JFactory::getDocument();
$doc->addStylesheet( JURI::root(true) . '/components/com_spsimpleportfolio/assets/css/spsimpleportfolio.css' );

$tags = SpsimpleportfolioHelper::getTags( (array) $this->item->spsimpleportfolio_tag_id );
$newtags = array();
foreach ($tags as $tag) {
	$newtags[] 	 = $tag->title;
}

//video
if($this->item->video) {
	$video = parse_url($this->item->video);

	switch($video['host']) {
		case 'youtu.be':
		$video_id 	= trim($video['path'],'/');
		$video_src 	= '//www.youtube.com/embed/' . $video_id;
		break;

		case 'www.youtube.com':
		case 'youtube.com':
		parse_str($video['query'], $query);
		$video_id 	= $query['v'];
		$video_src 	= '//www.youtube.com/embed/' . $video_id;
		break;

		case 'vimeo.com':
		case 'www.vimeo.com':
		$video_id 	= trim($video['path'],'/');
		$video_src 	= "//player.vimeo.com/video/" . $video_id;
	}

}

?>

<div id="sp-simpleportfolio" class="sp-simpleportfolio sp-simpleportfolio-view-item">
	<div class="uk-grid">
    <div class="uk-width-medium-3-4">
			<div class="sp-simpleportfolio-image">
				<?php if($this->item->video) { ?>
				<div class="sp-simpleportfolio-embed">
					<iframe src="<?php echo $video_src; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
				</div>
				<?php } else { ?>
				<?php if($this->item->image) { ?>
				<img class="sp-simpleportfolio-img" src="<?php echo $this->item->image; ?>" alt="<?php echo $this->item->title; ?>">
				<?php } else { ?>
				<img class="sp-simpleportfolio-img" src="<?php echo $this->item->thumbnail; ?>" alt="<?php echo $this->item->title; ?>">
				<?php } ?>
				<?php } ?>
			</div>
		</div>
    <div class="uk-width-medium-1-4">
			<div class="sp-simpleportfolio-meta">

				<div class="sp-simpleportfolio-created">
					<h4><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_PROJECT_DATE'); ?></h4>
					<?php echo JHtml::_('date', $this->item->created_on, JText::_('DATE_FORMAT_LC3')); ?>
				</div>

				<div class="sp-simpleportfolio-tags">
					<h4><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_PROJECT_CATEGORIES'); ?></h4>
					<?php echo implode(', ', $newtags); ?>
				</div>

				<?php if ($this->item->url) { ?>
				<div class="sp-simpleportfolio-link">
					<a class="btn btn-primary" target="_blank" href="<?php echo $this->item->url; ?>"><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_VIEW_PROJECT'); ?></a>
				</div>
				<?php } ?>
			</div>
		</div>
</div>


	<div class="sp-simpleportfolio-details clearfix">

		<div class="sp-simpleportfolio-description">
			<h2><?php echo $this->item->title; ?></h2>
			<?php echo $this->item->description; ?>
		</div>

	</div>
</div>
