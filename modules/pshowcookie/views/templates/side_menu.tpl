<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Main"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Main", true)}">
    {l s='Manual' mod='pshowcookie'}
</a>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Group"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Group", true)}">
    {l s='Groups' mod='pshowcookie'}
</a>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Cookie"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Cookie", true)}">
    {l s='Cookies' mod='pshowcookie'}
</a>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Config"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Config", true)}">
    {l s='Configuration' mod='pshowcookie'}
</a>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}GoogleConsentMode"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}GoogleConsentMode", true)}">
    {l s='Google Consent Mode' mod='pshowcookie'}
</a>

<style>
    a.list-group-item[href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Hook", true)}"] { display: none; }
    a.list-group-item[href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}"] { display: none; }
</style>
