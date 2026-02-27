<h3>{l s='Module update' mod='pshowsystem'} "{$moduleName}"</h3>

<div class="row">

    <div class="col-xs-6">

        <p>
            {l s='The version of the module in your store' mod='pshowsystem'}:
            {$moduleVersionCurrent}
        </p>
        <p>
            {l s='The latest version of the module' mod='pshowsystem'}:
            {$moduleVersionLatest}
        </p>
        <p>
            {l s='The latest version of the module available for you' mod='pshowsystem'}:
            {$moduleVersionAllowed}
        </p>
        <p>
            {l s='The last check' mod='pshowsystem'}:
            {if $lastLicenseRefresh}
                {$lastLicenseRefresh|date_format:"%Y-%m-%d %H:%M:%S"}
                <a
                        href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&forceRefreshLicense"
                        title="{l s='Refresh now' mod='pshowsystem'}"
                        style="margin-left: 10px;"
                >
                    <i class="icon-refresh"></i>
                    {l s='Refresh' mod='pshowsystem'}
                </a>
            {else}
                0000-00-00 00:00:00
            {/if}
        </p>
        <p>
            {if $isUpdateAvailable}
                <a
                        class="btn btn-info"
                        href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&page=update"
                        onclick="if (!confirm('{l s='This action will override all module files! Are you sure?' mod='pshowsystem'}')) return false;"
                >
                    <i class="icon-arrow-up" style="margin-right: 10px;"></i>
                    {l s='Click to start update to ' mod='pshowsystem'} v{$moduleVersionAllowed}
                </a>
                {*            {else}*}
                {*                <a*}
                {*                    href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&page=update&force=1"*}
                {*                    onclick="if (!confirm('{l s='This action will override all module files! Are you sure?' mod='pshowsystem'}')) return false;"*}
                {*                >*}
                {*                    <button class="btn btn-warning">{l s='Click to refresh files' mod='pshowsystem'}</button>*}
                {*                </a>*}
                {*                <br>*}
                {*                <small>{l s='Use this button to override files of the module' mod='pshowsystem'}</small>*}
            {else}
                <button class="btn btn-success" disabled="disabled">
                    <i class="icon-check" style="margin-right: 10px;"></i>
                    {l s='No updates available' mod='pshowsystem'}
                </button>
                <a
                        class="btn btn-default"
                        href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Update", true)}&page=update&force=1"
                        onclick="if (!confirm('{l s='This action will override all module files! Are you sure?' mod='pshowsystem'}')) return false;"
                >
                    <i class="icon-refresh"></i>
                    {l s='Refresh module files' mod='pshowsystem'}
                </a>
            {/if}
        </p>

    </div>

</div>

</div>

<div class="panel">

    <div class="panel-heading">
        <i class="icon-cogs"></i>
        {l s='Changelog' mod='pshowsystem'}
    </div>

    <p>
        <strong>
            {l s='Remember to read changelog before every update to see what changes will be introduced.' mod='pshowsystem'}
        </strong>
    </p>

    <hr>

    <p style="overflow-y: scroll; height: 400px;">
        {$changelog|replace:"\n":"<br>" nofilter}
    </p>
