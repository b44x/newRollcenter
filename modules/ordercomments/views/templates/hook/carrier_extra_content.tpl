<div class="order-comments-fields">
    <div class="form-group">
        <label for="order_comments">{l s='Uwagi do zamówienia' mod='ordercomments'}</label>
        <textarea name="order_comments" id="order_comments" class="form-control">{$order_comments}</textarea>
        <small class="form-text text-muted">{l s='Jeśli masz jakieś dodatkowe uwagi dotyczące realizacji zamówienia możesz wpisać je w tym polu.' mod='ordercomments'}</small>
    </div>
    
    <div class="form-group mt-3">
        <label for="courier_notes">{l s='Uwagi dla kuriera' mod='ordercomments'}</label>
        <textarea name="courier_notes" id="courier_notes" class="form-control">{$courier_notes}</textarea>
        <small class="form-text text-muted">{l s='Jeśli masz jakieś uwagi dla kuriera, wpisz je tutaj. Przekażemy je.' mod='ordercomments'}</small>
    </div>
</div>
