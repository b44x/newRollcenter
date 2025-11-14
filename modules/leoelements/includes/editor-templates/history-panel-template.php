<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

namespace LeoElements;

use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

?>
<script type="text/template" id="tmpl-elementor-panel-history-page">
	<div id="elementor-panel-elements-navigation" class="elementor-panel-navigation">
		<div id="elementor-panel-elements-navigation-history" class="elementor-panel-navigation-tab elementor-active" data-view="history"><?php echo Leo_Helper::__( 'Actions', 'elementor' ); ?></div>
		<div id="elementor-panel-elements-navigation-revisions" class="elementor-panel-navigation-tab" data-view="revisions"><?php echo Leo_Helper::__( 'Revisions', 'elementor' ); ?></div>
	</div>
	<div id="elementor-panel-history-content"></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-history-tab">
	<div id="elementor-history-list"></div>
	<div class="elementor-history-revisions-message"><?php echo Leo_Helper::__( 'Switch to Revisions tab for older versions', 'elementor' ); ?></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-history-no-items">
	<i class="elementor-nerd-box-icon eicon-nerd"></i>
	<div class="elementor-nerd-box-title"><?php echo Leo_Helper::__( 'No History Yet', 'elementor' ); ?></div>
	<div class="elementor-nerd-box-message"><?php echo Leo_Helper::__( 'Once you start working, you\'ll be able to redo / undo any action you make in the editor.', 'elementor' ); ?></div>
</script>

<script type="text/template" id="tmpl-elementor-panel-history-item">
	<div class="elementor-history-item__details">
		<span class="elementor-history-item__title">{{{ title }}}</span>
		<span class="elementor-history-item__subtitle">{{{ subTitle }}}</span>
		<span class="elementor-history-item__action">{{{ action }}}</span>
	</div>
	<div class="elementor-history-item__icon">
		<span class="eicon" aria-hidden="true"></span>
	</div>
</script>
