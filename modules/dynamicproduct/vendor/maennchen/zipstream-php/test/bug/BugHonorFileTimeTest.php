<?php
/**
 * 2007-2025 TuniSoft
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    TuniSoft (tunisoft.solutions@gmail.com)
 * @copyright 2007-2025 TuniSoft
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace BugHonorFileTimeTest;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DateTime;
use PHPUnit\Framework\TestCase;
use ZipStream\Option\{
    Archive,
    File
};
use ZipStream\ZipStream;

use function fopen;

/**
 * Asserts that specified last-modified timestamps are not overwritten when a
 * file is added
 */
class BugHonorFileTimeTest extends TestCase
{
    public function testHonorsFileTime(): void
    {
        $archiveOpt = new Archive();
        $fileOpt = new File();
        $expectedTime = new DateTime('2019-04-21T19:25:00-0800');

        $archiveOpt->setOutputStream(fopen('php://memory', 'wb'));
        $fileOpt->setTime(clone $expectedTime);

        $zip = new ZipStream(null, $archiveOpt);

        $zip->addFile('sample.txt', 'Sample', $fileOpt);

        $zip->finish();

        $this->assertEquals($expectedTime, $fileOpt->getTime());
    }
}
