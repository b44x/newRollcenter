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
<div id="presta_sw">
    <ul class="nav">
        <li>
            <a class="nav-link" href="#filter"><span class="num">1</span>{l s='Filter' d='Modules.Prestamassedit.Admin'}</a>
        </li>
        <li>
            <a class="nav-link" href="#edit"><span class="num">2</span>{l s='Edit' d='Modules.Prestamassedit.Admin'}</a>
        </li>
        <li>
            <a class="nav-link" href="#preview"><span class="num">3</span>{l s='Preview' d='Modules.Prestamassedit.Admin'}</a>
        </li>
        <li>
            <a class="nav-link" href="#finish"><span class="num">4</span>{l s='Finish' d='Modules.Prestamassedit.Admin'}</a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="filter" class="tab-pane" role="tabpanel">
            {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/filter.tpl"}
        </div>
        <div id="edit" class="tab-pane" role="tabpanel">
            {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/edit.tpl"}
        </div>
        <div id="preview" class="tab-pane" role="tabpanel">
            {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/preview.tpl"}
        </div>
        <div id="finish" class="tab-pane" role="tabpanel">
            {include file="module:prestamassedit/views/templates/admin/presta_mass_edit_manager/helpers/view/steps/finish.tpl"}
        </div>
    </div>

    {* Growl messages will be append here *}
    <div id="growls" class="default presta-growl"></div>
</div>
