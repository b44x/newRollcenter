<?php
/**
* 2007-2019 Alcalink ECOMMERCE & SEO
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
* @author Alcalink Multiservicios S.L. <info@alcalink.com>
* @copyright  2007-2019 Alcalink Multiservicios S.L.
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* Registered Trademark & Property of Alcalink Multiservicios S.L.
*/

use PrestaShopBundle\Entity\Lang;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * v.1.0.3
 * This function updates your module from previous versions to the version 1.2.4,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */

function upgrade_module_1_2_1($module)
{
    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'alcaurl_canonical` CHANGE `id` `id_alcaurl_canonical` INT(8) NOT NULL AUTO_INCREMENT;',
        'CREATE TABLE `' . _DB_PREFIX_ . 'alcaurl_canonical_lang` (
            `id_alcaurl_canonical` int(8) NOT NULL, 
            `url_redirect` varchar(2500) DEFAULT NULL, 
            `url` varchar(2500) DEFAULT NULL, 
            `id_lang` int(3) NOT NULL
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8 COLLATE utf8_general_ci',
    ];
    $languages = Language::getLanguages(false);
    foreach($languages as $l) {
        $queries[] = 'INSERT INTO ' . _DB_PREFIX_ . 'alcaurl_canonical_lang (id_alcaurl_canonical, url_redirect, url, id_lang)
            SELECT id_alcaurl_canonical, url_redirect, url, ' . (int)$l['id_lang'] . '
            FROM ' . _DB_PREFIX_ . 'alcaurl_canonical;';   
    }
    $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'alcaurl_canonical` DROP `url_redirect`;';
    $queries[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'alcaurl_canonical` DROP `url`;';

    foreach ($queries as $query) {
        Db::getInstance()->execute($query);
    }

    return true;
}
