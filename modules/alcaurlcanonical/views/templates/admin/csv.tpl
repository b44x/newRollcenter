       
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
    <form 
        action="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getSetCsv&accionCSV=set"
        class="form-group">
        <div class="form-group">
            <div class="row col-xs-12 form-group">
                <a href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getSetCsv&accionCSV=download" target="_blank" class="btn btn-outline-primary">{l s='Download example CSV' mod='alcaurlcanonical'}</a>
            </div>
            <div class="row col-xs-12 form-group" style="margin-top:30px">
                <input name="csv" type="file" />
            </div>

        
            <div class="form-group row col-xs-12">
                <button type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule" class="btn btn-primary pull-right has-action btn-save-general">
                    <i class="icon-save"></i>&nbsp;{l s='Save all' mod='alcaurlcanonical'}
                </button>
            </div>   
        </div>
    </form>