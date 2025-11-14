{* 
* @Module Name: AP Page Builder
* @Website: apollotheme.com - prestashop template provider
* @author Apollotheme <apollotheme@gmail.com>
* @copyright Apollotheme
* @description: ApPageBuilder is module help you can build content for your shop
*}
<!-- @file modules\leoextratab\views\templates\admin\leoextratab_tab\helpers\form\form -->
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'html_leotab'}
    <div class="col-lg-9 col-lg-offset-3">
        <p class="alert alert-danger">{$input.text}</p>
        <a class href="https://addons.prestashop.com/en/page-customization/20111-ap-page-builder.html">{l s='https://addons.prestashop.com/en/page-customization/20111-ap-page-builder.html'}</a>
    </div>
    {/if}
    {if $input.type == 'html_category'}
    <div class="col-lg-9 col-lg-offset-3">
        {$input.html_category}
    </div>
    {/if}
    {$smarty.block.parent}
{/block}