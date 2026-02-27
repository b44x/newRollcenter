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
        <i class="material-icons">done_all</i>
        {l s='Finished' d='Modules.Prestamassedit.Admin'}
    </div>
    <div class="panel-body">
        <div class="text-center presta_success_message_block">
            <div class="mb-2">
                <img src="{$presta_success_icon|escape:'html':'UTF-8'}" width="200" height="200" alt="Icon">
            </div>
            <h2 id="successful_message"></h2>
        </div>
    </div>

    <div class="panel-footer clearfix">
        <button type="button" onclick="window.location.reload()" class="btn btn-outlined-primary pull-right">
            {l s='Home' d='Modules.Prestamassedit.Admin'}
        </button>
        <button type="button" class="sw_prev_btn_to_edit btn btn-outlined-primary">
            {l s='Edit Again' d='Modules.Prestamassedit.Admin'}
        </button>
    </div>
</div>
