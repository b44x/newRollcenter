<script>
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({$rc_datalayer_json nofilter});
</script>

{if $rc_purchase_payload_json}
<script>
  window.rcPurchasePayload = {$rc_purchase_payload_json nofilter};
</script>
{/if}

{if $rc_js.purchase}
  <script src="{$rc_js.purchase|escape:'htmlall':'UTF-8'}" defer></script>
{/if}

{if $rc_js.category}
  <script src="{$rc_js.category|escape:'htmlall':'UTF-8'}" defer></script>
{/if}

{if $rc_js.category_select}
  <script src="{$rc_js.category_select|escape:'htmlall':'UTF-8'}" defer></script>
{/if}

{if $rc_js.cart}
  <script src="{$rc_js.cart|escape:'htmlall':'UTF-8'}" defer></script>
{/if}

{if $rc_js.checkout}
  <script src="{$rc_js.checkout|escape:'htmlall':'UTF-8'}" defer></script>
{/if}

{if $rc_purchase_payload_json}
<script>
  window.rcPurchasePayload = {$rc_purchase_payload_json nofilter};
</script>
{/if}
