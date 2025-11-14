{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<div class="modal fade" id="position-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{l s='Create New' mod='leoelements'}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
            <div class="row">
                <label class="control-label col-lg-4 required">{l s='Name' mod='leoelements'}</label>
                <div class="col-lg-8">
                    <input type="text" name="position-name" id="position-name" value="" class="" required="required">
                </div>
            </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='leoelements'}</button>
        <button type="button" class="btn btn-primary btn-save-position">{l s='Create New' mod='leoelements'}</button>
      </div>
    </div>
  </div>
</div>
<div class="col-md-2 left-column-config">
    <div id="leoprofile-tabs">
        <span class="tab list-group-item active" data-tab="fieldset_general">
            {l s='General' mod='leoelements'}
        </span>
        <span class="tab list-group-item spanheader" data-tab="fieldset_header">
            {l s='Header' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_content">
            {l s='Home Content' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_footer">
            {l s='Footer' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_background">
            {l s='Background' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_breadcrumb">
            {l s='Breadcrumb' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_productlist">
            {l s='Product List: Category, Manufacture, Search' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_category">
            {l s='Category Page' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_product">
            {l s='Product Detail Page' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_font">
            {l s='Font Config' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_custom">
            {l s='Custom Css and Js' mod='leoelements'}
        </span>
        <span class="tab list-group-item" data-tab="fieldset_variable_css">
            {l s='Set Variable CSS' mod='leoelements'}
        </span>
    </div>
</div>
<div class="col-md-10 right-column-config">
{$leoformcontent}
</div>