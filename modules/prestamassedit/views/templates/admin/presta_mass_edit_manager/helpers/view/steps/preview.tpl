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
            <i class="material-icons">preview</i>
            {l s='Preview' d='Modules.Prestamassedit.Admin'}
        </div>
        <table id="preview_before_update" class="table"></table>

        <div class="panel-footer clearfix">
            <button type="button" class="sw_prev_btn btn btn-outlined-primary">
                {l s='Back' d='Modules.Prestamassedit.Admin'}
            </button>
            <button type="button" id="prestaMassEditUpdateSubmit" onclick="return confirm({l s='apply changes for all selected products?' d='Modules.Prestamassedit.Admin'})" class="btn pull-right presta_submit_btn">
                {l s='Confirm and Update' d='Modules.Prestamassedit.Admin'}
            </button>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-heading">
            <i class="material-icons">settings</i>
            {l s='Selected Products' d='Modules.Prestamassedit.Admin'}
            <span class="badge selected_count">0</span>
        </div>
        <table class="table" id="selectedProductsForEdit2"></table>
    </div>
</div>
