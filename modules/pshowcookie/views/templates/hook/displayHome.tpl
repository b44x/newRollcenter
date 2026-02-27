<div class="row">
    <div class="col-xs-12 col-lg-6 card p-1">
        <strong>{l s='Code in the .tpl file in your theme:' mod='pshowcookie'}</strong>
    </div>
    <div class="hidden-md-down col-lg-1">
        <br>
        ->
    </div>
    <div class="col-xs-12 col-lg-5 card p-1">
        <strong>{l s='Result:' mod='pshowcookie'}</strong>
    </div>
</div>

{foreach from=$data item='group'}
    <div class="row">
        <div class="col-xs-12 col-lg-6 card p-1" style="font-size: 0.8rem">
            <code>{ldelim}ifConsentGranted group='{$group.reference}'{rdelim}
                <br>
                &nbsp;&nbsp;&nbsp;
                {l s='Group of cookies' mod='pshowcookie'}
                &lt;strong&gt;{$group.name}&lt;/strong&gt;
                {l s='has been granted' mod='pshowcookie'}.
                <br>
                &nbsp;{ldelim}/ifConsentGranted{rdelim}
            </code>
        </div>
        <div class="hidden-md-down col-lg-1">
            <br>
            <br>
            ->
        </div>
        <div class="col-xs-12 col-lg-5 card p-1">
            <br>
            {ifConsentGranted group=$group.reference}
            {l s='Group of cookies' mod='pshowcookie'} <strong>{$group.name}</strong> {l s='has been granted' mod='pshowcookie'}.
            {/ifConsentGranted}
            <br>
            <br>
        </div>
    </div>
{/foreach}

<div class="row">
    <div class="col-xs-12 col-lg-6 card p-1" style="font-size: 0.8rem">
        <code>{ldelim}hook h='displayConsentChangeButton'{rdelim}</code>
    </div>
    <div class="hidden-md-down col-lg-1">
        <br>
        ->
    </div>
    <div class="col-xs-12 col-lg-5 card p-1">
        {hook h='displayConsentChangeButton'}
    </div>
</div>

<p>&nbsp;</p>