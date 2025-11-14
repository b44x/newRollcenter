{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
<div class="variants-product">
{foreach from=$productVariants key=id_attribute_group item=group}
    {if $group.attributes|@count gt 0}
        <div class="clearfix product-variants-item">
            {if isset($configs.show_label) && $configs.show_label}
            <span class="control-label">{$group.name|escape:'htmlall':'UTF-8'}</span>
            {/if}
            {if $group.group_type == 'select'}
              <select
                class="form-control form-control-select"
                id="group_{$group.id_attribute_group|escape:'htmlall':'UTF-8'}"
                data-product-attribute="{$group.id_attribute_group|escape:'htmlall':'UTF-8'}"
                name="group[{$group.id_attribute_group|escape:'htmlall':'UTF-8'}]">
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                  <option value="{$id_attribute|escape:'htmlall':'UTF-8'}" title="{$group_attribute.name|escape:'htmlall':'UTF-8'}"{if $group_attribute.selected} selected="selected"{/if}>{$group_attribute.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
              </select>
            {elseif $group.group_type == 'color'}
              <ul id="group_{$group.id_attribute_group|escape:'htmlall':'UTF-8'}" class="groupUl">
                {foreach from=$group.attributes key=id_attribute item=group_attribute}
                  <li class="float-xs-left input-container">
                    <label>
                      <input class="input-color" type="radio" data-product-attribute="{$group.id_attribute_group|escape:'htmlall':'UTF-8'}" name="group[{$group.id_attribute_group|escape:'htmlall':'UTF-8'}]" value="{$id_attribute|escape:'htmlall':'UTF-8'}"{if $group_attribute.selected} checked="checked"{/if}>
                      <span
                        {if $group_attribute.html_color_code}class="color" style="background-color: {$group_attribute.html_color_code|escape:'htmlall':'UTF-8'}" {/if}
                        {if $group_attribute.texture}class="color texture" style="background-image: url({$group_attribute.texture|escape:'htmlall':'UTF-8'})" {/if}
                      ><span class="sr-only">{$group_attribute.name|escape:'htmlall':'UTF-8'}</span></span>
                    </label>
                  </li>
                {/foreach}
              </ul>	
        {elseif $group.group_type == 'radio'}
            <ul id="group_{$group.id_attribute_group|escape:'htmlall':'UTF-8'}" class="groupUl">
              {foreach from=$group.attributes key=id_attribute item=group_attribute}
                <li class="input-container float-xs-left groupLi">
                  <label>
                    <input class="input-radio" type="radio" data-product-attribute="{$group.id_attribute_group|escape:'htmlall':'UTF-8'}" name="group[{$group.id_attribute_group|escape:'htmlall':'UTF-8'}]" value="{$id_attribute|escape:'htmlall':'UTF-8'}"{if $group_attribute.selected} checked="checked"{/if}>
                    <span class="radio-label">{$group_attribute.name|escape:'htmlall':'UTF-8'}</span>
                  </label>
                </li>
              {/foreach}
            </ul>
          {/if}
        </div>
    {/if}
{/foreach}
</div>

