<?php
namespace Cz\Markdown;
use cebe\markdown\Markdown,
    PHPUnit_Framework_TestCase;

/**
 * TOCTestcase
 * 
 * @author  czukowski
 */
abstract class TOCTestcase extends PHPUnit_Framework_TestCase
{
    protected $object;

    /**
     * @param   string  $filename
     * @return  string
     */
    protected function loadFile($filename) {
        $path = __DIR__.'/docs/'.$filename;
        return file_get_contents($path);
    }

    /**
     * @param   string  $content
     * @reutrn  string
     */
    protected function render($content) {
        $markdown = new Markdown;
        return $markdown->parse($content);
    }
}
