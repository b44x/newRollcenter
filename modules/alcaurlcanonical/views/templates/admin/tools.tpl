{**
 * 2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author ALCALINK E-COMMERCE & SEO, S.L.L. <info@alcalink.com>
 * @copyright  2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Registered Trademark & Property of ALCALINK E-COMMERCE & SEO, S.L.L.
*}
           <form action="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getTools&accion=checkDestination" class="form-group">
               <div class="row form-group">
                   <label>{l s='Product verification' mod='alcaurlcanonical'}</label>
                   <label>{l s='Example /2-category' mod='alcaurlcanonical'}</label>
               </div>
               <div class="form-group">
                   <input type="hidden" name="id" value="{$object->id|escape:'html':'UTF-8'}" />
                   <div class="row col-xs-12 form-group">
                       <strong class="col-xs-12">{l s='Verify the origin of the urls' mod='alcaurlcanonical'}</strong>
                       <input type="text" name="construct" />
                       <div class="help-block">
                           <p>{l s='Write the destination url, use words in brackets to include information from the sample database {name}' mod='alcaurlcanonical'}</p>
                           <p>{l s='Use to replace inside brackets |replace:texto_original:texto_nuevo' mod='alcaurlcanonical'}</p>
                       </div>
                   </div>

                   <div class="row col-xs-12 form-group">
                       <strong class="col-xs-12">{l s='Start at ID' mod='alcaurlcanonical'}</strong>
                       <input type="text" name="id_first" />
                       <div class="help-block">
                           <p>{l s='Go from where it will begin' mod='alcaurlcanonical'}</p>
                       </div>
                   </div>

                   <div class="form-group row col-xs-12">
                       <button type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule" class="btn btn-primary pull-right has-action btn-save-general">
                           <i class="icon-save"></i>&nbsp;{l s='Start process' mod='alcaurlcanonical'}
                       </button>
                   </div>

                   <div class="form-group row col-xs-12">
                       <div class="form-group row col-xs-12">
                           {l s='Results for htaccess' mod='alcaurlcanonical'}
                       </div>
                       <pre class="form-group row col-xs-12" id="consolehtacces">

        </pre>
                       <div class="form-group row col-xs-12">
                           {l s='Errores' mod='alcaurlcanonical'}
                       </div>
                       <pre class="form-group row col-xs-12" id="consoleerrors">

        </pre>
                   </div>
               </div>
           </form>



           <form action="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getTools&accion=checkDestinationCategory" class="form-group" id="formcategory">
               <div class="row form-group">
                   <label>{l s='Category verification' mod='alcaurlcanonical'}</label>
                   <label>{l s='Example /2-category' mod='alcaurlcanonical'}</label>
               </div>
               <div class="form-group">
                   <input type="hidden" name="id" value="{$object->id|escape:'html':'UTF-8'}" />
                   <div class="row col-xs-12 form-group">
                       <strong class="col-xs-12">{l s='Verify the origin of the urls' mod='alcaurlcanonical'}</strong>
                       <input type="text" name="construct" />
                       <div class="help-block">
                           <p>{l s='Write the destination url, use words in brackets to include information from the sample database {name}' mod='alcaurlcanonical'}</p>
                           <p>{l s='Use to replace inside brackets |replace:texto_original:texto_nuevo' mod='alcaurlcanonical'}</p>
                       </div>
                   </div>

                   <div class="row col-xs-12 form-group">
                       <strong class="col-xs-12">{l s='Start ID' mod='alcaurlcanonical'}</strong>
                       <input type="text" name="id_first" />
                       <div class="help-block">
                           <p>{l s='Id start' mod='alcaurlcanonical'}</p>
                       </div>
                   </div>

                   <div class="form-group row col-xs-12">
                       <button type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule" class="btn btn-primary pull-right has-action btn-save-general">
                           <i class="icon-save"></i>&nbsp;{l s='Start process' mod='alcaurlcanonical'}
                       </button>
                   </div>

                   <div class="form-group row col-xs-12">
                       <div class="form-group row col-xs-12">
                           {l s='Results for htacces' mod='alcaurlcanonical'}
                       </div>
                       <pre class="form-group row col-xs-12" id="consolehtacces">

    </pre>
                       <div class="form-group row col-xs-12">
                           {l s='Errors' mod='alcaurlcanonical'}
                       </div>
                       <pre class="form-group row col-xs-12" id="consoleerrors">

    </pre>
                   </div>
               </div>
           </form>



           <form action="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getTools&accion=checkhtaccss" class="form-group" id="formcategory">
               <div class="row form-group">
                   <label>{l s='Category verifications' mod='alcaurlcanonical'}</label>
                   <label>{l s='Example /2-category' mod='alcaurlcanonical'}</label>
               </div>
               <div class="form-group">

                   <div class="row col-xs-12 form-group">
                       <strong class="col-xs-12">{l s='Review htaccess' mod='alcaurlcanonical'}</strong>
                
                       <div class="help-block">
                    </div>
                   </div>

                   <div class="form-group row col-xs-12">
                       <button type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule" class="btn btn-primary pull-right has-action btn-save-general">
                           <i class="icon-save"></i>&nbsp;{l s='Start process' mod='alcaurlcanonical'}
                       </button>
                   </div>

                   <div class="form-group row col-xs-12">
                       <div class="form-group row col-xs-12">
                           {l s='Results htacces' mod='alcaurlcanonical'}
                       </div>
                       <pre class="form-group row col-xs-12" id="consolehtacces">

    </pre>
                       <div class="form-group row col-xs-12">
                           {l s='Errors' mod='alcaurlcanonical'}
                       </div>
                       <pre class="form-group row col-xs-12" id="consoleerrors">

    </pre>
                   </div>
               </div>
           </form>