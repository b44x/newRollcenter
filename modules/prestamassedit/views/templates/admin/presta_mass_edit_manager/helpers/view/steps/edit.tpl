{**
* 2008-2025 Prestaworld
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one website.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
*
* DISCLAIMER
*
* Do not alter or add/update to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    prestaworld
* @copyright 2008-2025 Prestaworld
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
* International Registered Trademark & Property of prestaworld
*}
<div class="col-lg-8">
    <div class="panel">
        <div class="panel-heading">
            <i class="material-icons">edit_note</i>
            {l s='Edit Action' d='Modules.Prestamassedit.Admin'}
        </div>
        <form method="POST" id="presta_me_edit_form" class="defaultForm form-horizontal">
            <input type="hidden" name="presta_mass_edit[product_id_list]">
            <div class="clearfix">
                <div class="col-md-3">
                    <div class="list-group presta_me_sidebar">
                        <a href="#base_update" class="list-group-item presta-active active" data-toggle="tab">
                            {l s='Base Settings' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="base_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#category_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Category' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="category_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#brand_and_feature_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Brands & Features' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="brand_and_feature_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#related_prod_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Related Products' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="related_prod_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#pricing_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Pricing' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="pricing_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#specific_price_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Specific Price' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="specific_price_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#quantity_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Quantity' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="quantity_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#combinations_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Combinations' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="combinations_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#shipping_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Shipping' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="shipping_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#seo_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='SEO' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="seo_update_done" style="display: none;">task_alt</span>
                        </a>
                        <a href="#options_update" class="list-group-item presta-active" data-toggle="tab">
                            {l s='Options' d='Modules.Prestamassedit.Admin'}
                            <span class="material-icons presta-done-mark" id="options_update_done" style="display: none;">task_alt</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-9 tab-content">
                    {* <!-- Basic --> *}
                    <div id="base_update" class="list-group active tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/basic_update.tpl"}
                    </div>
                    {* <!-- Category --> *}
                    <div id="category_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/category_update.tpl"}
                    </div>
                    {* <!-- Brand & Feature --> *}
                    <div id="brand_and_feature_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/brand_and_feature_update.tpl"}
                    </div>
                    {* <!-- Related --> *}
                    <div id="related_prod_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/related_prod_update.tpl"}
                    </div>
                    {* <!-- Pricing --> *}
                    <div id="pricing_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/pricing_update.tpl"}
                    </div>
                    {* <!-- Specific Price --> *}
                    <div id="specific_price_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/specific_price_update.tpl"}
                    </div>
                    {* <!-- Quantity --> *}
                    <div id="quantity_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/quantity_update.tpl"}
                    </div>
                    {* <!-- Combinations --> *}
                    <div id="combinations_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/combinations_update.tpl"}
                    </div>
                    {* <!-- Shipping --> *}
                    <div id="shipping_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/shipping_update.tpl"}
                    </div>
                    {* <!-- SEO --> *}
                    <div id="seo_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/seo_update.tpl"}
                    </div>
                    {* <!-- Options --> *}
                    <div id="options_update" class="list-group tab-pane" role="tabpanel">
                        {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/_partials/options_update.tpl"}
                    </div>
                </div>
            </div>

            <div class="panel-footer clearfix">
                <button type="button" class="sw_prev_btn btn btn-outlined-primary">
                    {l s='Back' d='Modules.Prestamassedit.Admin'}
                </button>
                <button type="submit" id="prestaMassEditUpdateData" class="btn pull-right presta_submit_btn">
                    {l s='Next' d='Modules.Prestamassedit.Admin'}
                </button>
            </div>
        </form>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-heading">
            <i class="material-icons">settings</i>
            {l s='Selected Products' d='Modules.Prestamassedit.Admin'}
            <span class="badge selected_count">0</span>
        </div>
        <table class="table" id="selectedProductsForEdit"></table>
    </div>
</div>
