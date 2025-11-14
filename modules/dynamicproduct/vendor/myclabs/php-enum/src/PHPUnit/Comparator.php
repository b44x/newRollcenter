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
namespace MyCLabs\Enum\PHPUnit;

if (!defined('_PS_VERSION_')) {
    exit;
}

use MyCLabs\Enum\Enum;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Use this Comparator to get nice output when using PHPUnit assertEquals() with Enums.
 *
 * Add this to your PHPUnit bootstrap PHP file:
 *
 * \SebastianBergmann\Comparator\Factory::getInstance()->register(new \MyCLabs\Enum\PHPUnit\Comparator());
 */
final class Comparator extends \SebastianBergmann\Comparator\Comparator
{
    public function accepts($expected, $actual)
    {
        return $expected instanceof Enum && (
                $actual instanceof Enum || $actual === null
            );
    }

    /**
     * @param Enum $expected
     * @param Enum|null $actual
     *
     * @return void
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        if ($expected->equals($actual)) {
            return;
        }

        throw new ComparisonFailure(
            $expected,
            $actual,
            $this->formatEnum($expected),
            $this->formatEnum($actual),
            false,
            'Failed asserting that two Enums are equal.'
        );
    }

    private function formatEnum(Enum $enum = null)
    {
        if ($enum === null) {
            return "null";
        }

        return get_class($enum)."::{$enum->getKey()}()";
    }
}
