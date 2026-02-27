{*
* @author    Edrone sp. z o.o <hello@edrone.me>
* @copyright Edrone sp. z o.o
* @license   https://edrone.me/integration-license/
*}

{literal}
<script>
    var doc = document.createElement("script");
    doc.type = "text/javascript";
    doc.async = true;
    doc.src = ("https:" == document.location.protocol ? "https:" : "http:") + '//d3bo67muzbfgtl.cloudfront.net/edrone_2_0.js';
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(doc, s);
    window._edrone = window._edrone || {};
{/literal}
    {foreach $edroneVariables as $key => $content}
        {if is_numeric($content) && is_float($content + 0)}
            {$content = floor($content * 100) / 100}
        {/if}
        _edrone.{$key|escape:'htmlall':'UTF-8'} = `{$content|escape:'htmlall':'UTF-8'}`;
    {/foreach}
    {if $page.page_name == 'order-confirmation' && !$customer.is_logged}
        {if $customer.newsletter}
            _edrone.send_additional_newsletter_trace = 1;
        {/if}
    {/if}
{literal}
</script>
{/literal}
