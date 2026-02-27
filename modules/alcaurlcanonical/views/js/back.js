/**
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
*/
$(document).on('change', '[name="type"]', function() {
  $('[data-display]').hide();
  id = $(this).find('option:selected').val();
  $('[data-display]').find('select').each(function() {
     $(this).attr('data-name', $(this).attr('name'));
     $(this).removeAttr('name');
  });
  $('[data-display="' +id + '"]').show();
  o = $('[data-display="' + id + '"]').find('select').first();
  $(o).attr('name', $(o).attr('data-name'));
});

$(document).on('change', '.alcaurlcanonicaltogglechange', function() {
  setChangeAlcaCanonical(this);
});

function setChangeAlcaCanonical(obj) { 
  id = $(obj).find('option:selected').first().val();
  target = $(obj).attr('data-target');
  name = $(target).first().attr('data-attr');
  $(target + ' [data-id]').hide();
  $(target + ' [data-id]').find('input,select').each(function () { 
    if ($(this).attr('name') != 'biteateAjaxSearchProduct' && $(this).attr('name') != 'url_redirect') {
      $(this).removeAttr('name');
    }
  });
  $(target + ' [data-id=' + id + ']').show();
  $(target + ' [data-id=' + id + ']').find('input,select').each(function () {
    if ($(this).attr('name') != 'biteateAjaxSearchProduct' && $(this).attr('name') != 'url_redirect') {
      $(this).attr('name', name);
    }
  });
}

$(document).on('change', '.alcaurlcanonicaltogglechange2', function() {
  setChangeAlcaCanonical2(this);
});

function setChangeAlcaCanonical2(obj) {
  val = $(obj).find('option:selected').first().val();
  target = $(obj).attr('data-target');
  if (val == '' || val == '410') {
    $(target).hide();
  } else {
    $(target).show();
  }
}