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
<div class="modal-body">
    <div class="input-group">
        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
            <i class="icon-flag"></i>
            {l s='Manage translations' d='Modules.Prestamassedit.ModalTranslationTpl'}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            {foreach from=$module_languages item=language}
                <li>
                    <a href="{$translateLinks[$language.iso_code]|escape:'html':'UTF-8'}">{$language.name|escape:'html':'UTF-8'}</a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
