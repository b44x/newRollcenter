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

use LeoElements\Leo_Helper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly
}
?>
<script type="text/template" id="tmpl-elementor-templates-modal__header">
	<div class="elementor-templates-modal__header__logo-area"></div>
	<div class="elementor-templates-modal__header__menu-area"></div>
	<div class="elementor-templates-modal__header__items-area">
		<# if ( closeType ) { #>
			<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--{{{ closeType }}} elementor-templates-modal__header__item">
				<# if ( 'skip' === closeType ) { #>
				<span><?php echo Leo_Helper::__( 'Skip', 'elementor' ); ?></span>
				<# } #>
				<i class="eicon-close" aria-hidden="true" title="<?php echo Leo_Helper::__( 'Close', 'elementor' ); ?>"></i>
				<span class="elementor-screen-only"><?php echo Leo_Helper::__( 'Close', 'elementor' ); ?></span>
			</div>
		<# } #>
		<div id="elementor-template-library-header-tools"></div>
	</div>
</script>

<script type="text/template" id="tmpl-elementor-templates-modal__header__logo">
	<span class="elementor-templates-modal__header__logo__icon-wrapper">
		<i class="eicon-elementor"></i>
	</span>
	<span class="elementor-templates-modal__header__logo__title">{{{ title }}}</span>
</script>
