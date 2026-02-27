<?php
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
 **/
if (!defined('_PS_VERSION_')) {
    exit;
}

class alcaurlcanonicalnofoundModuleFrontController extends FrontController
{
    public $php_self = 'pagenotfound';
    public $page_name = 'pagenotfound';
    public $ssl = true;

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        header('HTTP/1.1 410 Not Found');
        header('Status: 410 Not Found');
        $this->context->cookie->disallowWriting();
        parent::initContent();
        $this->context->smarty->tpl_vars['page']->value['meta']['title'] = 'error 410';
        $this->setTemplate('errors/404');
    }

    protected function canonicalRedirection($canonical_url = '')
    {
        // 404 - no need to redirect to the canonical url
    }

    protected function sslRedirection()
    {
        // 404 - no need to redirect
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');

        return $page;
    }
}
