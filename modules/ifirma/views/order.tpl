<div class="row" id="start_products">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-header-title"><img src="../img/admin/invoice.gif">Fakturowanie - ifirma.pl</h3>
            </div>
            <div class="card-body" style="margin-top: 20px;">
                {$sendResultMessage}
                {$invoiceValidationMessage}
                
                    {if $invoice}
                        <a href="/modules/ifirma/get.php?id={$invoice->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz fakturę krajową &raquo;</a>
                    {else}
                        <a href="/modules/ifirma/send.php?id={$orderId}&type={$actionInvoice}&h={$hash}"><img src="../img/admin/next.gif"/>Wystaw fakturę krajową &raquo;</a>
                    {/if}
                    <br/>

                {if !$isVat}
                    {if $bill}
                        <a href="/modules/ifirma/get.php?id={$bill->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz rachunek &raquo;</a>
                    {else}
                        <a href="/modules/ifirma/send.php?id={$orderId}&type={$actionBill}&h={$hash}"><img src="../img/admin/next.gif"/>Wystaw rachunek &raquo;</a>
                    {/if}
                {else}
                    {if $invoiceSend}
                        <a href="/modules/ifirma/get.php?id={$invoiceSend->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz fakturę wysyłkową&raquo;</a>
                    {else}
                        <a href="/modules/ifirma/send.php?id={$orderId}&type={$actionInvoiceSend}&h={$hash}"><img src="../img/admin/next.gif"/>Wystaw fakturę wysyłkową &raquo;</a>
                    {/if}
                    <br/>
                    {if $invoiceProforma}
                        <a href="/modules/ifirma/get.php?id={$invoiceProforma->id}&h={$hash}"><img src="{$ifirmaImg}pdf.gif"/>Pobierz fakturę proforma&raquo;</a>
                        {if !$invoice}
                             <br />
                            <a href="/modules/ifirma/send.php?id={$invoiceProforma->id}&type={$actionInvoiceFromProforma}&h={$hash}"><img src="../img/admin/next.gif"/>Wystaw fakturę krajową na podstawie faktury proforma &raquo;</a>
                        {/if}
                    {else}
                        <a href="/modules/ifirma/send.php?id={$orderId}&type={$actionInvoiceProforma}&h={$hash}"><img src="../img/admin/next.gif"/>Wystaw fakturę proforma &raquo;</a>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</div>
