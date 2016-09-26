<?php
namespace Cz\Markdown;

/**
 * TOCTraitTest
 * 
 * @author  czukowski
 * 
 * @property  TOCTestObject  $object
 */
class TOCTraitTest extends TOCTestcase
{
    /**
     * @dataProvider  provideParseTableOfContents
     */
    public function testParseTableOfContents($source, $url, $type, $from, $to, $compareTo) {
        $actual = $this->object->generateTableOfContents($this->loadFile($source), $url, $type, $from, $to);
        $expected = $this->render($this->loadFile($compareTo));
        $this->assertEquals($expected, $actual);
    }

    public function provideParseTableOfContents() {
        return [
            ['Sample.md', 'example.html', 'ol', 1, 1, 'FirstLevelOl.md'],
            ['Sample.md', 'example.html', 'ul', 1, 1, 'FirstLevelUl.md'],
            ['Sample.md', 'example.html', 'ol', 1, 2, 'FirstToSecondLevelOl.md'],
            ['Sample.md', 'example.html', 'ul', 1, 2, 'FirstToSecondLevelUl.md'],
            ['Sample.md', 'example.html', 'ol', 2, 3, 'SecondToThirdLevelOl.md'],
            ['Sample.md', 'example.html', 'ul', 2, 3, 'SecondToThirdLevelUl.md'],
            ['Sample.md', 'example.html', 'ol', 1, 6, 'AllLevelsOl.md'],
            ['Sample.md', 'example.html', 'ul', 1, 6, 'AllLevelsUl.md'],
        ];
    }

    public function setUp() {
        $this->object = new TOCTestObject;
    }
}
