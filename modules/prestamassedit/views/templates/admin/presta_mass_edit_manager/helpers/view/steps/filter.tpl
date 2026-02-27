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
<div class="panel">
    <div class="panel-heading">
        <i class="material-icons">settings</i>
        {l s='Filter Product' d='Modules.Prestamassedit.Admin'}
    </div>
    <form method="POST" id="presta_me_filter_form" class="defaultForm form-horizontal">
        <div class="clearfix">
            <div class="col-lg-8 pl-lg-2">
                <div class="form-group clearfix">
                    {* Jquery queryBuilder *}
                    <div id="presta_qb"></div>
                </div>
            </div>
            <div class="panel col-lg-4 clearfix">
                <div class="panel-heading">
                    <i class="material-icons">lists</i>
                    {l s='Matched Products' d='Modules.Prestamassedit.Admin'}
                    <span class="badge matched_count">0</span>
                </div>
                <div class="alert alert-warning" id="presta_no_product">{l s='No Matched Product.'  d='Modules.Prestamassedit.Admin'}</div>
                <table id="presta_dt" class="table"></table>
                <div id="ajax_loader_product" class="presta_me_loader" style="display: none;">
                    <img src="{$presta_ajax_loader|escape:'htmlall':'UTF-8'}" width="60">
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" id="submitPrestaMassEditFilter" disabled class="btn presta_submit_btn pull-right">
                {l s='Next' d='Modules.Prestamassedit.Admin'}
            </button>
        </div>
    </form>
</div>
