<div id="ModuleLeoSizeChart">
	<input type="hidden" name="submitted_tabs[]" value="ModuleLeoSizeChart" />
	<h2>{l s='Add or modify customizable properties' mod='leosizechart'}</h2>
	{if isset($display_common_field) && $display_common_field}
		<div class="alert alert-info">{l s='Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product'  mod='leosizechart' sprintf=$bullet_common_field}</div>
	{/if}
    <div class="form-group">
		<label for="id_leosizechart">{l s='Select from created guides' mod='leosizechart'}</label>
		<select name="id_leosizechart" id="id_leosizechart" class="form-control">
			<option value="0">- {l s='Choose (optional)' mod='leosizechart'} -</option>
			{if isset($guides)}
			{foreach from=$guides item=guide}
			<option value="{$guide.id_guide}" {if isset($selectedGuide) && ($guide.id_guide == $selectedGuide)}selected="selected"{/if}>{$guide.title}</option>
			{/foreach}
			{/if}
		</select>
    </div>
    <div class="form-group">
        <a class="btn btn-primary" target="_blank" href="{$link->getAdminLink('AdminModules')}&configure=leosizechart&addGuide=1">
            <i class="material-icons">open_in_new</i>
            {l s='Create new guide' mod='leosizechart'}
        </a>
    </div>
</div>
