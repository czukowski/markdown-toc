<?php
namespace Cz\Markdown;
use InvalidArgumentException;

/**
 * TOCTrait
 * 
 * This trait uses two functions from `nette/neon` package by David Grudl under the New BSD License:
 * 
 *     Copyright (c) 2004, 2014 David Grudl (https://davidgrudl.com) All rights reserved.
 *     
 *     Redistribution and use in source and binary forms, with or without modification, are
 *     permitted provided that the following conditions are met:
 *     
 *      - Redistributions of source code must retain the above copyright notice, this list of
 *        conditions and the following disclaimer.
 *      - Redistributions in binary form must reproduce the above copyright notice, this list of
 *        conditions and the following disclaimer in the documentation and/or other materials
 *        provided with the distribution.
 *      - Neither the name of "Nette Framework" nor the names of its contributors may be used to
 *        endorse or promote products derived from this software without specific prior written
 *        permission.
 *     
 *     This software is provided by the copyright holders and contributors "as is" and any express
 *     or implied warranties, including, but not limited to, the implied warranties of
 *     merchantability and fitness for a particular purpose are disclaimed. In no event shall the
 *     copyright owner or contributors be liable for any direct, indirect, incidental, special,
 *     exemplary, or consequential damages (including, but not limited to, procurement of substitute
 *     goods or services; loss of use, data, or profits; or business interruption) however caused
 *     and on any theory of liability, whether in contract, strict liability, or tort (including
 *     negligence or otherwise) arising in any way out of the use of this software, even if advised
 *     of the possibility of such damage.
 * 
 * @author  David Grudl  <https://davidgrudl.com>  Original author of `nette/utils`
 * @author  czukowski
 */
trait TOCTrait
{
    /**
     * Creates a list block.
     * 
     * @param   string  $type   supported values: `ul` and `ol`.
     * @param   array   $items  list items (optional).
     * @return  array
     */
    protected function createList($type = 'ul', $items = []) {
        return ['list', 'list' => $type, 'items' => $items];
    }

    /**
     * Finds headlines in a markdown content.
     * 
     * @param   string   $markdown   source markdown.
     * @param   integer  $fromLevel  find headlines starting with this level
     * @param   integer  $toLevel    find headlines up to this level
     * @return  array
     */
    protected function findHeadlines($markdown, $fromLevel = 1, $toLevel = 6) {
        $headlines = [];
        foreach ($this->parseBlocks($this->splitLines($markdown)) as $block) {
            if ($this->isItemOfType($block, 'headline') && isset($block['level'])
                && $block['level'] >= $fromLevel && $block['level'] <= $toLevel
            ) {
                $headlines[] = $block;
            }
        }
        return $headlines;
    }

    /**
     * @param   string   $markdown   markdown source.
     * @param   string   $url        source URL.
     * @param   string   $listType   supported values: `ul` and `ol`.
     * @param   integer  $fromLevel  use headlines starting with this level
     * @param   integer  $toLevel    use headlines up to this level
     * @return  string
     */
    public function generateTableOfContents($markdown, $url, $listType = 'ul', $fromLevel = 1, $toLevel = 6) {
        return $this->generateTableOfContentsFromMultiple([[$markdown, $url]], $listType, $fromLevel, $toLevel);
    }

    /**
     * @param   array    $sources    markdown sources and URLs.
     * @param   string   $listType   supported values: `ul` and `ol`.
     * @param   integer  $fromLevel  use headlines starting with this level
     * @param   integer  $toLevel    use headlines up to this level
     * @return  string
     */
    public function generateTableOfContentsFromMultiple($sources, $listType = 'ul', $fromLevel = 1, $toLevel = 6) {
        $toc = [];
        foreach ($sources as $source) {
            if ( ! is_array($source)) {
                throw new InvalidArgumentException('Sources argument expected to be array of arrays each having exactly 2 elements');
            }
            list ($markdown, $url) = $source;
            $headlines = $this->findHeadlines($markdown, $fromLevel, $toLevel);
            if ($headlines) {
                $documentToc = $this->parseTableOfContents($headlines, $url, $listType);
                $toc = array_merge($toc, $documentToc);
            }
        }
        return $this->renderAbsy([$this->createList($listType, $toc)]);
    }

    /**
     * Checks for a block type.
     * 
     * @param   array   $item
     * @param   string  $type
     * @return  boolean
     */
    protected function isItemOfType( & $item, $type) {
        return isset($item[0]) && $item[0] === $type;
    }

    /**
     * @param   array   $headlines
     * @param   string  $url
     * @param   string  $listType
     * @return  array
     */
    protected function parseTableOfContents(array & $headlines, $url, $listType) {
        $block = [];
        $head = & $block;
        $parents = [ & $block];
        $prevLevel = reset($headlines)['level'];
        foreach ($headlines as $i => $item) {
            // Beware: very tricky stuff!
            $level = $item['level'];
            for ($j = $prevLevel; $j < $level; $j++) {
                // Save current head as a parent.
                $parents[$j] = & $head;
                // Look up item indexes we'll need momentarily.
                $lastHead = count($head) - 1;  // Last item index in the current head.
                $newItem = count($lastHead);   // New item index in the current head.
                // Add new list at the end of the last element of the current head. NOT by reference!
                $head[$lastHead][] = $this->createList($listType);
                // Set new list items as a current head. Must look it up in the previous head and
                // assign by reference to work correctly!
                $head = & $head[$lastHead][$newItem]['items'];
            }
            for ($j = $prevLevel; $j > $level; $j--) {
                // Change head back to the parent's head.
                $head = & $parents[$j - 1];
                unset($parents[$j - 1]);
            }
            $head[] = [
                [
                    'link',
                    'url' => $url.($i === 0 ? '' : '#'.$this->formatAnchor($item['content'])),
                    'text' => $item['content'],
                ],
            ];
            $prevLevel = $level;
        }
        return $block;
    }

    /**
     * @param   string  $content
     * @return  string
     */
    protected function formatAnchor($content) {
        return self::webalize($this->renderAbsy($content));
    }

    /**
     * @param   string  $text
     * @return  array
     */
    protected function splitLines($text) {
        return explode("\n", str_replace(["\r\n", "\n\r", "\r"], "\n", $text));
    }

    /**
     * Converts string to ASCII.
     * 
     * This function is taken from `nette/utils` package.
     * 
     * @author   David Grudl <https://davidgrudl.com>
     * @see      https://github.com/nette/utils
     * @license  https://github.com/nette/utils/blob/master/license.md
     * 
     * @param   string  $s  UTF-8 encoding
     * @return  string  ASCII
     */
    protected static function toAscii($s) {
        static $transliterator = NULL;
        if ($transliterator === NULL && class_exists('Transliterator', FALSE)) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        }

        $s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s);
        $s = strtr($s, '`\'"^~?', "\x01\x02\x03\x04\x05\x06");
        $s = str_replace(
            ["\xE2\x80\x9E", "\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x9A", "\xE2\x80\x98", "\xE2\x80\x99", "\xC2\xB0"],
            ["\x03", "\x03", "\x03", "\x02", "\x02", "\x02", "\x04"], $s
        );
        if ($transliterator !== NULL) {
            $s = $transliterator->transliterate($s);
        }
        if (ICONV_IMPL === 'glibc') {
            $s = str_replace(
                ["\xC2\xBB", "\xC2\xAB", "\xE2\x80\xA6", "\xE2\x84\xA2", "\xC2\xA9", "\xC2\xAE"],
                ['>>', '<<', '...', 'TM', '(c)', '(R)'], $s
            );
            $s = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT//IGNORE', $s); // intentionally @
            $s = strtr($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
                ."\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
                ."\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
                ."\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe"
                ."\x96\xa0\x8b\x97\x9b\xa6\xad\xb7",
                'ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt- <->|-.');
            $s = preg_replace('#[^\x00-\x7F]++#', '', $s);
        } else {
            $s = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s); // intentionally @
        }
        $s = str_replace(['`', "'", '"', '^', '~', '?'], '', $s);
        return strtr($s, "\x01\x02\x03\x04\x05\x06", '`\'"^~?');
    }

    /**
     * Converts to web safe characters [a-z0-9-] text.
     * 
     * This function is taken from `nette/utils` package.
     * 
     * @author   David Grudl <https://davidgrudl.com>
     * @see      https://github.com/nette/utils
     * @license  https://github.com/nette/utils/blob/master/license.md
     * 
     * @param   string   $s         UTF-8 encoding
     * @param   string   $charlist  allowed characters
     * @param   boolean  $lower     convert to lower case?
     * @return  string
     */
    protected static function webalize($s, $charlist = NULL, $lower = TRUE) {
        $s = self::toAscii($s);
        if ($lower) {
            $s = strtolower($s);
        }
        $s = preg_replace('#[^a-z0-9'.preg_quote($charlist, '#').']+#i', '-', $s);
        $s = trim($s, '-');
        return $s;
    }

    /**
     * @param  array  $lines
     */
    abstract protected function parseBlocks($lines);

    /**
     * @param  array  $blocks
     */
    abstract protected function renderAbsy($blocks);
}
