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

namespace ZipStream;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DeflateStream extends Stream
{
    protected $filter;

    /**
     * @var Option\File
     */
    protected $options;

    /**
     * Rewind stream
     *
     * @return void
     */
    public function rewind(): void
    {
        // deflate filter needs to be removed before rewind
        if ($this->filter) {
            $this->removeDeflateFilter();
            $this->seek(0);
            $this->addDeflateFilter($this->options);
        } else {
            rewind($this->stream);
        }
    }

    /**
     * Remove the deflate filter
     *
     * @return void
     */
    public function removeDeflateFilter(): void
    {
        if (!$this->filter) {
            return;
        }
        stream_filter_remove($this->filter);
        $this->filter = null;
    }

    /**
     * Add a deflate filter
     *
     * @param Option\File $options
     * @return void
     */
    public function addDeflateFilter(Option\File $options): void
    {
        $this->options = $options;
        // parameter 4 for stream_filter_append expects array
        // so we convert the option object in an array
        $optionsArr = [
            'comment' => $options->getComment(),
            'method' => $options->getMethod(),
            'deflateLevel' => $options->getDeflateLevel(),
            'time' => $options->getTime()
        ];
        $this->filter = stream_filter_append(
            $this->stream,
            'zlib.deflate',
            STREAM_FILTER_READ,
            $optionsArr
        );
    }
}
