<?php

namespace CamooToc\Test;

use PHPUnit\Framework\TestCase;
use Camoo\Toc\TableOfContents;
use Camoo\Toc\Exception\TocException;

/**
 * Class TableOfContentsTest
 * @author CamooSarl
 * @covers \Camoo\Toc\TableOfContents
 */
class TableOfContentsTest extends TestCase
{
    private $toc;

    public function setUp() : void
    {
        $this->toc = new TableOfContents('<h1>Test</h1>');
    }

    public function tearDown() : void
    {
        unset($this->toc);
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(TableOfContents::class, $this->toc);
    }

    /**
     * @covers \Camoo\Toc\TableOfContents::getTableOfContents
     */
    public function testGetTableOfContentsWithoutDOMDocumentThrowsException()
    {
        $this->expectException(TocException::class);
        $mockedToc = $this->getMockBuilder(TableOfContents::class)
            ->setMethods(['classExists'])
            ->setConstructorArgs(['<h1>Test</h1>'])
            ->getMock();

        $mockedToc->expects($this->once())
            ->method('classExists')
            ->willReturn(false);
        $mockedToc->getTableOfContents();
    }

    /**
     * @covers \Camoo\Toc\TableOfContents::getTableOfContents
     * @dataProvider provideTocInstance
     */
    public function testGetTableOfContentsSuccess($toc, $tag=null)
    {
        $result = $toc->getTableOfContents();
        $this->assertStringContainsString('h1', $result);
        $this->assertStringContainsString('ul', $result);
        $this->assertStringContainsString('li', $result);
        $this->assertStringContainsString('Tester', $result);
    }

    /**
     * @covers \Camoo\Toc\TableOfContents::getContent
     * @dataProvider provideTocInstance
     */
    public function testGetContentSuccess($toc, $tag)
    {
        $result = $toc->getContent();
        $this->assertStringContainsString($tag, $result);
    }

    /**
     * @covers \Camoo\Toc\TableOfContents::getTableOfContents
     */
    public function testGetTableOfContentsWithEmptyTagValueShouldSkeepTag()
    {
        $toc = new TableOfContents('<h6></h6>');
        $result = $toc->getTableOfContents();
        $this->assertStringNotContainsString('li', $result);
    }

    /**
     * @covers \Camoo\Toc\TableOfContents::getTableOfContents
     */
    public function testGetTableOfContentsWithNonConformLevel()
    {
        $toc = new TableOfContents('<h2>Non Conform</h2><h6></h6>');
        $result = $toc->getTableOfContents();
        $this->assertStringContainsString('li', $result);
    }

    /**
     * @covers \Camoo\Toc\TableOfContents::getTableOfContents
     */
    public function testGetTableOfContentsWithMultipleLevels()
    {
        $toc = new TableOfContents('
			<h1>Tester</h1><b>Bold</b><h2>Sub-Test</h2><a href="#">link</a><h3>3rd Level</h3><h4>4rd level</h4>
			<h1>Tester</h1><b>Bold</b><h2>Sub-Test</h2><a href="#">link</a><h3>3rd Level</h3>
			');
        $result = $toc->getTableOfContents();
        $this->assertStringContainsString('li', $result);
    }

    public function provideTocInstance()
    {
        return [
            [new TableOfContents('<h1>Tester</h1>'), 'h1'],
            [new TableOfContents('<h1>Tester</h1><b>Bold</b><h2>Sub-Test</h2>'), 'h2'],
            [new TableOfContents('<h1>Tester</h1><b>Bold</b><h2>Sub-Test</h2><a href="#">link</a><h3>3rd Level</h3>'), 'h3'],
            [new TableOfContents('<h1>Tester</h1><b>Bold</b><h2>Sub-Test</h2><a href="#">link</a><h3>3rd Level</h3><h4>4rd level</h4>'), 'h4'],
        ];
    }
}
