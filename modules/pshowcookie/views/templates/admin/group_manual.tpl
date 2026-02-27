{if !$manual_group_reference}
    <p>
        {l s='Cookies are grouped by their purpose. You can add a new group by clicking on the "Add new group" button. You can also edit or delete existing groups.' mod='pshowcookie'}
    </p>
{else}
    <p>
        {l s='Control rendering of the template by using the "ifConsentGranted" smarty tag in the .tpl files.' mod='pshowcookie'}
        <br>
        {l s='Fragment of the template will be rendered only if the user has granted consent to the group.' mod='pshowcookie'}
        <br>
        <br>
        <code>
            {ldelim}ifConsentGranted group='{$manual_group_reference}'{rdelim}
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;
            ...&nbsp;{l s='this will be rendered only if the user has granted consent to the group with reference' mod='pshowcookie'}
            &nbsp;`{$manual_group_reference}` ...
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;
            &lt;script src="https://some.nice.analytics.service.com/script.js"&gt;&lt;/script&gt;
            <br>
            {ldelim}/ifConsentGranted{rdelim}
        </code>
    </p>
{/if}
