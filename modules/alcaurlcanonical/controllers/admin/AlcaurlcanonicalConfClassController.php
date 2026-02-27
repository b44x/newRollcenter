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

class AlcaurlcanonicalConfClassController extends AdminControllerCore
{
    public $id = 'ModuleAlcaurlcanonicalConfClass';

    public function __construct($forceControllerName = '', $default_theme_name = 'default')
    {
        $this->fields_list = [
          'id_alcaurl_canonical' => [
            'title' => 'id_alcaurl_canonical',
            'filter_key' => 'a!id_alcaurl_canonical',
          ],
          'type' => [
            'title' => 'type',
            'filter_key' => 'a!type',
          ],
          'id_object' => [
            'title' => 'id_object',
            'filter_key' => 'a!id_object',
          ],
          'id_product_attribute' => [
            'title' => 'id_product_attribute',
            'filter_key' => 'a!id_product_attribute',
          ],
          'url' => [
            'title' => 'url',
            'filter_key' => 'a!url',
          ],
          'url_redirect' => [
            'title' => 'url_redirect',
            'filter_key' => 'a!url_redirect',
          ],
          'object_redirect' => [
            'title' => 'object_redirect',
            'filter_key' => 'a!object_redirect',
          ],
          'id_object_redirect' => [
            'title' => 'id_object_redirect',
            'filter_key' => 'a!id_object_redirect',
          ],
          'redirect' => [
            'title' => 'redirect',
            'filter_key' => 'a!redirect',
          ],
          'id_shop' => [
            'title' => 'id_shop',
            'filter_key' => 'a!id_shop',
          ],
          'name' => [
            'title' => 'name',
            'filter_key' => false,
            'custom_filter' => [
              0 => 'p!name',
              1 => 'c!name',
              2 => 'cm!meta_title',
            ],
          ],
        ];
        $this->lang = 'true';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` p ON (p.`id_product` = a.`id_object` AND a.`type` = 1 AND p.`id_lang` = ' . (int) Context::getContext()->language->id . ' AND p.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` c ON (c.`id_category` = a.`id_object` AND a.`type` = 2 AND c.`id_lang` = ' . (int) Context::getContext()->language->id . ' AND c.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'cms_lang` cm ON (cm.`id_cms` = a.`id_object` AND a.`type` = 4 AND cm.`id_lang` = ' . (int) Context::getContext()->language->id . ' AND cm.`id_shop` = ' . (int) Context::getContext()->shop->id . ')';
        $this->_select = 'CASE a.`type`
        WHEN 1 THEN p.`name`
        WHEN 2 THEN c.`name`
        ELSE cm.`meta_title`
        END as name';
        $this->table = 'alcaurl_canonical';
        $this->_defaultOrderBy = 'id_alcaurl_canonical';
        $this->_default_pagination = '100';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;
        $this->_where = ' AND a.id_shop = ' . (int) Context::getContext()->shop->id;
        parent::__construct($forceControllerName, $default_theme_name);
    }

    public function trySetVarsObject(&$object, $vars)
    {
        if (!$object) {
            $object = $this;
        }

        if (is_array($vars) || is_object($vars)) {
            foreach ($vars as $key => $var) {
                if (property_exists($object, $key) == true) {
                    $object->$key = $var;
                }
            }
        }
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        if ($id_lang) {
            $this->_join = str_replace('$id_lang', $id_lang, $this->_join);
        }

        parent::getList(
            $id_lang,
            $order_by,
            $order_way,
            $start,
            $limit,
            $id_lang_shop
        );

        return [
            'list' => $this->_list,
            'list_total' => $this->_listTotal,
        ];
    }

    public function setCustomFilter($string)
    {
        $this->_filter .= $string;
    }

    public function getFilter()
    {
        return $this->_filter;
    }

    public function getListSql()
    {
        return $this->_listsql;
    }
}
