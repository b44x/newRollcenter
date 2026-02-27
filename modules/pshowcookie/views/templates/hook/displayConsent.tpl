<script>

window.dataLayer = window.dataLayer || [];
window.gtag = function () {
    window.dataLayer.push(arguments);
}

gtag('consent', 'default', {ldelim}
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    analytics_storage: 'denied',
    personalization_storage: 'denied',
    wait_for_update: 1000
{rdelim});

{if isset($pshowcookie_gcm)}
    gtag('consent', 'update', {ldelim}
        ad_storage: "{if $pshowcookie_gcm['ad_storage']}granted{else}denied{/if}",
        ad_user_data: "{if $pshowcookie_gcm['ad_user_data']}granted{else}denied{/if}",
        ad_personalization: "{if $pshowcookie_gcm['ad_personalization']}granted{else}denied{/if}",
        analytics_storage: "{if $pshowcookie_gcm['analytics_storage']}granted{else}denied{/if}",
        personalization_storage: "{if $pshowcookie_gcm['personalization_storage']}granted{else}denied{/if}",
    {rdelim});
{/if}

</script>