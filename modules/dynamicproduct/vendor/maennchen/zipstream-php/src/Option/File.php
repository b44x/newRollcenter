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

namespace ZipStream\Option;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DateTime;

final class File
{
    /**
     * @var string
     */
    private $comment = '';
    /**
     * @var Method
     */
    private $method;
    /**
     * @var int
     */
    private $deflateLevel;
    /**
     * @var DateTime
     */
    private $time;
    /**
     * @var int
     */
    private $size = 0;

    public function defaultTo(Archive $archiveOptions): void
    {
        $this->deflateLevel = $this->deflateLevel ?: $archiveOptions->getDeflateLevel();
        $this->time = $this->time ?: new DateTime();
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return Method
     */
    public function getMethod(): Method
    {
        return $this->method ?: Method::DEFLATE();
    }

    /**
     * @param Method $method
     */
    public function setMethod(Method $method): void
    {
        $this->method = $method;
    }

    /**
     * @return int
     */
    public function getDeflateLevel(): int
    {
        return $this->deflateLevel ?: Archive::DEFAULT_DEFLATE_LEVEL;
    }

    /**
     * @param int $deflateLevel
     */
    public function setDeflateLevel(int $deflateLevel): void
    {
        $this->deflateLevel = $deflateLevel;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     */
    public function setTime(DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }
}
