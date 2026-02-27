<div class="alert alert-info">
    <p>
        {l s='Control rendering of the template by using the "ifConsentGranted" smarty block in the .tpl files.' mod='pshowcookie'}
        <br>
        <br>
        <code>
            {ldelim}ifConsentGranted group='analytics'{rdelim}
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;
            ...&nbsp;{l s='this will be rendered only if the user has granted consent to the group with reference' mod='pshowcookie'}
            `analytics` ...
            <br>
            &nbsp;&nbsp;&nbsp;&nbsp;
            &lt;script src="https://some.nice.analytics.service.com/script.js"&gt;&lt;/script&gt;
            <br>
            {ldelim}/ifConsentGranted{rdelim}
        </code>
    </p>
</div>

<div class="alert alert-info">
    <p>
        {l s='Display "Manage consents" button by placing this code in the .tpl file:' mod='pshowcookie'}
        <code>{ldelim}hook h='displayConsentChangeButton'{rdelim}</code>
    </p>
</div>

<div class="alert alert-info">
    <p>
        {l s='To customize appearence of the modals, copy file:' mod='pshowcookie'}
        <br>
        <code>modules/pshowcookie/views/css/cookieconsent-override.css</code>
        <br>
        {l s='to the theme folder and modify it:' mod='pshowcookie'}
        <br>
        <code>themes/your_theme/modules/pshowcookie/css/cookieconsent-override.css</code>
    </p>
</div>

<div class="alert alert-info">
    <p>
        {l s='If you want the consent event to be sent to GTM earlier, insert this code:' mod='pshowcookie'}
        <br>
        <code>{literal}{hook h='displayConsent' mod='pshowcookie'}{/literal}</code>
        <br>
        {l s='at the beginning of the file:' mod='pshowcookie'}
        <br>
        <code>themes/your_theme/templates/_partials/head.tpl</code>
        <br>
        {l s='and detach the module from the displayHeader hook' mod='pshowcookie'}
    </p>
</div>
