{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<script type="text/template" id="btn-edit-page-builder-category">
    <div>
		{if $urlPageBuilder }
			<br />
			<a href="{$urlPageBuilder}" target="_blank" class="button button-primary button-hero edit_with_button_link">
                            <img src="{$icon_url}" alt="Leo Elements Logo">
                            {l s='Edit with Leo Elements Creator' mod='leoelements'}
			</a>
		{else}
			<br />
			<div class="alert alert-info">{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
		{/if}
    </div>
</script>

<script type="text/template" id="btn-edit-page-builder-product">
    <div>
		{if $urlPageBuilder }
                    <br />
                    <a href="{$urlPageBuilder}" target="_blank" class="button button-primary button-hero edit_with_button_link">
                        <img src="{$icon_url}" alt="Leo Elements Logo">
                        {l s='Edit with Leo Elements Creator' mod='leoelements'}
                    </a>
		{else}
                    <br />
                    <div class="alert alert-info">{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
		{/if}
    </div>
</script>
	
<script type="text/template" id="btn-edit-page-builder-cms">
    <div>
		{if $urlPageBuilder }
			<br />
                        <a href="{$urlPageBuilder}" target="_blank" class="button button-primary button-hero edit_with_button_link">
                            <img src="{$icon_url}" alt="Leo Elements Logo">
                            {l s='Edit with Leo Elements Creator' mod='leoelements'}
                        </a>
		{else}
			<br />
			<div class="alert alert-info">{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
		{/if}
    </div>
</script>

<script type="text/template" id="btn-edit-page-builder-blog">
    <div class="form-group">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-9">
			{if $urlPageBuilder }
				<a href="{$urlPageBuilder}" class="btn btn-info leo-btn-edit"><i class="icon-external-link"></i>
					{l s='Edit content with - LeoElements' mod='leoelements'}
				</a>
			{else}
				<div class="alert alert-info">&nbsp;{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
			{/if}
		</div>
    </div>
</script>
	 
<script type="text/template" id="btn-edit-page-builder-manufacturer">
    <div>
		{if $urlPageBuilder }
			<br />
			<a href="{$urlPageBuilder}" class="btn btn-info leo-btn-edit"><i class="icon-external-link"></i> 
				{l s='Edit content with - LeoElements' mod='leoelements'}
			</a>
		{else}
			<br />
			<div class="alert alert-info">&nbsp;{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
		{/if}
    </div>
</script>
	 
<script type="text/template" id="btn-edit-page-builder-supplier">
    <div>
		{if $urlPageBuilder }
			<br />
			<a href="{$urlPageBuilder}" class="btn btn-info leo-btn-edit"><i class="icon-external-link"></i> 
				{l s='Edit content with - LeoElements' mod='leoelements'}
			</a>
		{else}
			<br />
			<div class="alert alert-info">&nbsp;{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
		{/if}
    </div>
</script>

<script type="text/template" id="btn-edit-page-builder-leoguide">
    <div>
		{if $urlPageBuilder }
			<br />
                        <a href="{$urlPageBuilder}" target="_blank" class="button button-primary button-hero edit_with_button_link">
                            <img src="{$icon_url}" alt="Leo Elements Logo">
                            {l s='Edit with Leo Elements Creator' mod='leoelements'}
                        </a>
		{else}
			<br />
			<div class="alert alert-info">{l s='Save page first to enable Leoelements' mod='leoelements'}</div>
		{/if}
    </div>
</script>

<script type="text/javascript">
	$(document).ready(function () {
		var $wrapperCategory = $('div#category_description, div#root_category_description').closest('.col-sm'),
			$wrapperProduct = $('#features'),
			$wrapperCms = $('#cms_page_content'),
			$wrapperBlog = $('#smart_blog_post_form').find("[name^=content_]").first().parents('.form-group').last(),
			$wrapperManufacturer = $('div#manufacturer_description').closest('.col-sm'),
			$wrapperSupplier = $('div#supplier_description').closest('.col-sm'),
                        $wrapperCms = $('#cms_page_content'),
                        $wrapperLeoGuide = $('.js_guide_content').closest('.col-lg-8'),

			$btnTemplateCategory = $('#btn-edit-page-builder-category'),
			$btnTemplateProduct = $('#btn-edit-page-builder-product'),
			$btnTemplateCms = $('#btn-edit-page-builder-cms'),
			$btnTemplateBlog = $('#btn-edit-page-builder-blog'),
			$btnTemplateManufacturer = $('#btn-edit-page-builder-manufacturer'),
			$btnTemplateSupplier = $('#btn-edit-page-builder-supplier'),
                        $btnTemplateLeoGuide = $('#btn-edit-page-builder-leoguide');

			$wrapperCategory.append($btnTemplateCategory.html());
			$wrapperProduct.prepend($btnTemplateProduct.html());
			$wrapperCms.after($btnTemplateCms.html());
			$wrapperBlog.after($btnTemplateBlog.html());
			$wrapperManufacturer.append($btnTemplateManufacturer.html());
			$wrapperSupplier.append($btnTemplateSupplier.html());
                        $wrapperLeoGuide.append($btnTemplateLeoGuide.html());
            if(!$wrapperProduct.length){
            	$wrapperProduct = $('#product_description_description').parent();
            	$wrapperProduct.append($btnTemplateProduct.html());
            }
	});
</script>
