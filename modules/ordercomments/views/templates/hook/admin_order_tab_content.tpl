<div class="tab-pane fade" id="orderCommentsTab" role="tabpanel" aria-labelledby="orderCommentsTab-tab">
    <div class="card">
        <div class="card-header">
            <h3 class="card-header-title">{l s='Uwagi do zamówienia i uwagi kuriera' mod='ordercomments'}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h4>{l s='Uwagi do zamówienia' mod='ordercomments'}</h4>
                    <div class="border p-2 bg-light">
                        {if $order_comments}
                            {$order_comments|nl2br}
                        {else}
                            <em>{l s='Brak uwag do zamówienia.' mod='ordercomments'}</em>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4>{l s='Uwagi dla kuriera' mod='ordercomments'}</h4>
                    <div class="border p-2 bg-light">
                        {if $courier_notes}
                            {$courier_notes|nl2br}
                        {else}
                            <em>{l s='Brak uwag dla kuriera.' mod='ordercomments'}</em>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
