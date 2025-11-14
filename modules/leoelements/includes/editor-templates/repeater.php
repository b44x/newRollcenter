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
<script type="text/template" id="tmpl-elementor-repeater-row">
	<div class="elementor-repeater-row-tools">
		<# if ( itemActions.drag_n_drop ) {  #>
			<div class="elementor-repeater-row-handle-sortable">
				<i class="eicon-ellipsis-v" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Drag & Drop', 'elementor' ); ?></span>
			</div>
		<# } #>
		<div class="elementor-repeater-row-item-title"></div>
		<# if ( itemActions.duplicate ) {  #>
			<div class="elementor-repeater-row-tool elementor-repeater-tool-duplicate">
				<i class="eicon-copy" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Duplicate', 'elementor' ); ?></span>
			</div>
		<# }
		if ( itemActions.remove ) {  #>
			<div class="elementor-repeater-row-tool elementor-repeater-tool-remove">
				<i class="eicon-close" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Remove', 'elementor' ); ?></span>
			</div>
		<# } #>
	</div>
	<div class="elementor-repeater-row-controls"></div>
</script>
