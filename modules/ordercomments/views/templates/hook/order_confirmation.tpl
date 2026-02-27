{if $order_comments || $courier_notes}
<div class="card">
    <div class="card-block">
        <div class="row">
            <div class="col-md-12">
                <h3 class="h3 card-title">{l s='Twoje uwagi' mod='ordercomments'}</h3>
                
                {if $order_comments}
                <div class="mb-3">
                    <h4>{l s='Uwagi do zamówienia' mod='ordercomments'}</h4>
                    <div class="border p-2 bg-light">
                        {$order_comments|nl2br}
                    </div>
                </div>
                {/if}
                
                {if $courier_notes}
                <div>
                    <h4>{l s='Uwagi dla kuriera' mod='ordercomments'}</h4>
                    <div class="border p-2 bg-light">
                        {$courier_notes|nl2br}
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>
{/if}
