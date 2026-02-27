<style>
    .toolbar_btn.btn-help {
        display: none !important;
    }
    .toolbar_btn#page-header-desc-configuration-update {
        background: #ff4f4f;
        border-color: #ff4f4f;
        color: #fff;
    }
</style>

{function showTip type='success' id='' message='No message'}
    {if !PShow_Settings::getInstance($smarty.current_dir)->get('tip_'|cat:$id)}
        <div class="alert alert-{$type} fade in tip" id="{$id}">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <p>{$message}</p>
        </div>
    {/if}
{/function}

{assign var='modulePath' value=($smarty.const._PS_MODULE_DIR_|cat:$module->name|cat:'/')}

<script>
    if (typeof SELECT_TAB !== 'undefined') {
        SELECT_TAB.init('{$select_menu_tab}');
    }
    let PSHOW_MODULE_CLASS_NAME_ = "{$PSHOW_MODULE_CLASS_NAME_}";
    let SETTINGS_URL = "{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}";
    let MOD_SETTINGS = JSON.parse('{$mod_settings|json_encode}');

    {if PShow_Settings::getInstance($smarty.current_dir)->get('fold_menu_on_enter')}
    $('body').addClass('page-sidebar-closed');
    {/if}

    // fix bug - prestashop 1.7.6.2 - address_token not defined
    $(document).ready(function () {
        if (typeof window.address_token === "undefined") {
            let match = RegExp('[?&]' + 'token' + '=([^&]*)').exec(window.location.search);
            window.address_token = match && decodeURIComponent(match[1].replace(/\+/g, ' '));
        }
    });
</script>

<div class="row">

    {if $action|lower == 'allnotifications'}
        <div class="col-lg-12 modulecontainer">
            {include file="./admin/main_allnotifications.tpl"}
        </div>
    {else}
        <div class="col-lg-2">

            <div class="tabs">
                <div class="list-group text-center">

                    <strong>
                        <a class="list-group-item inactive">
                            <big>
                                {$module->displayName}
                            </big>
                        </a>
                    </strong>

                    {include file=$modulePath|cat:'views/templates/side_menu.tpl'}

                    <a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Hook"}active{/if}"
                       href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Hook", true)}">
                        {l s='Positions' mod='pshowsystem'}
                    </a>

                    <a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Settings"}active{/if}"
                       href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}">
                        {l s='Module settings' mod='pshowsystem'}
                    </a>

                    {if isset($isMissingOverrides) && $isMissingOverrides}
                        <a class="list-group-item" href="{$link->getAdminLink({$smarty.get.controller}, true)}&page=checkOverrides">
                            &gt; {l s='See missing overrides' mod='pshowsystem'} &lt;
                        </a>
                    {/if}

                </div>
            </div>

            {if isset($pshowHook_below_side_menu)}{$pshowHook_below_side_menu}{/if}

            {if isset($serverConfig)}
                <div class="panel">
                    <h3>
                        {l s='Server info' mod='pshowsystem'}
                    </h3>
                    <div>
                        {foreach from=$serverConfig item='item'}
                            <div>
                                {$item.label}:
                                <span class="pull-right label {if !$item.is_ok}label-danger{else}label-success{/if}">
                                    {$item.value}
                                </span>
                            </div>
                        {/foreach}
                        <br>
                        <a href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&page=phpinfo"
                           class="btn btn-sm btn-default">
                            click to see phpinfo
                        </a>
                    </div>
                </div>
            {/if}

            <div class="panel">
                <div class="panel-heading">{l s='Recommended' mod='pshowsystem'}</div>
                {if $recommended['image']}
                    <div style="margin: -15px -20px 0;">
                        <a href="{$recommended['url']}" target="_blank">
                            <img class="img-responsive" alt="" src="{$recommended['image']}">
                        </a>
                    </div>
                {/if}
                <a href="{$recommended['url']}" target="_blank"><strong>{$recommended['name']}</strong></a>
                <p>{$recommended['description']}</p>
                <div class="panel-footer">
                    <a href="{$recommended['url']}" target="_blank">
                        <button type="button" class="btn btn-primary btn-block">
                            {l s='Discover'}
                        </button>
                    </a>
                </div>
            </div>

        </div>
        <div class="col-lg-10 modulecontainer">

            <div class="alert alert-danger {$PSHOW_MODULE_CLASS_NAME_}-update-available"
                    {if !$isUpdateAvailable} style="display: none;"{/if}>
                {l s='Update your module! Updates are very important.' mod='pshowsystem'}
            </div>

            <div id="module_content">
                {include file='./admin/alerts.tpl'}
                {include file='./admin/tips.tpl'}

                {if isset($content) && $content}
                    {$content}
                {else}
                    <div class="{if isset($module_content_container)}{$module_content_container}{/if}">
                        {if in_array($controllername, array('settings', 'hook', 'backup', 'update', 'reportbug'))}
                            {include file="./admin/{$controllername|lower}_{$action|lower}.tpl"}
                        {else}
                            {include file=$modulePath|cat:"views/templates/admin/{$controllername|lower}_{$action|lower}.tpl"}
                        {/if}
                    </div>
                {/if}
            </div>
        </div>
    {/if}
    <div class="clearfix"></div>
</div>
