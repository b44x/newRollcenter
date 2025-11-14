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
<script type="text/template" id="tmpl-elementor-panel-revisions">
	<div class="elementor-panel-box">
	<div class="elementor-panel-scheme-buttons">
			<div class="elementor-panel-scheme-button-wrapper elementor-panel-scheme-discard">
				<button class="elementor-button" disabled>
					<i class="eicon-close" aria-hidden="true"></i>
					<?php echo Leo_Helper::__( 'Discard', 'elementor' ); ?>
				</button>
			</div>
			<div class="elementor-panel-scheme-button-wrapper elementor-panel-scheme-save">
				<button class="elementor-button elementor-button-success" disabled>
					<?php echo Leo_Helper::__( 'Apply', 'elementor' ); ?>
				</button>
			</div>
		</div>
	</div>

	<div class="elementor-panel-box">
		<div class="elementor-panel-heading">
			<div class="elementor-panel-heading-title"><?php echo Leo_Helper::__( 'Revisions', 'elementor' ); ?></div>
		</div>
		<div id="elementor-revisions-list" class="elementor-panel-box-content"></div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-panel-revisions-no-revisions">
	<i class="elementor-nerd-box-icon eicon-nerd" aria-hidden="true"></i>
	<div class="elementor-nerd-box-title"><?php echo Leo_Helper::__( 'No Revisions Saved Yet', 'elementor' ); ?></div>
	<div class="elementor-nerd-box-message">{{{ elementor.translate( elementor.config.revisions_enabled ? 'no_revisions_1' : 'revisions_disabled_1' ) }}}</div>
	<div class="elementor-nerd-box-message">{{{ elementor.translate( elementor.config.revisions_enabled ? 'no_revisions_2' : 'revisions_disabled_2' ) }}}</div>
</script>

<script type="text/template" id="tmpl-elementor-panel-revisions-loading">
	<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
</script>

<script type="text/template" id="tmpl-elementor-panel-revisions-revision-item">
	<div class="elementor-revision-item__wrapper {{ type }}">
		<div class="elementor-revision-item__gravatar">{{{ gravatar }}}</div>
		<div class="elementor-revision-item__details">
			<div class="elementor-revision-date">{{{ date }}}</div>
			<div class="elementor-revision-meta"><span>{{{ elementor.translate( type ) }}}</span> <?php echo Leo_Helper::__( 'By', 'elementor' ); ?> {{{ author }}}</div>
		</div>
		<div class="elementor-revision-item__tools">
			<# if ( 'current' === type ) { #>
				<i class="elementor-revision-item__tools-current eicon-star" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Current', 'elementor' ); ?></span>
			<# } else { #>
				<i class="elementor-revision-item__tools-delete eicon-close" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Delete', 'elementor' ); ?></span>
			<# } #>

			<i class="elementor-revision-item__tools-spinner eicon-loading eicon-animation-spin" aria-hidden="true"></i>
		</div>
	</div>
</script>
