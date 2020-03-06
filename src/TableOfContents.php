<?php
declare(strict_types=1);

namespace Camoo\Toc;

use DOMDocument;
use Camoo\Toc\Exception\TocException;

/**
 * Class TableOfContents
 * @author CamooSarl
 */
class TableOfContents
{

  /** @var string $content */
    private $content;

    /** @var int $depth */
    private $depth;

    /** @var array $tagsStructure */
    private $tagsStructure = [];

    public function __construct(string $content, int $depth = 6)
    {
        $this->content = $content;
        $this->depth = $depth;
    }

    /**
     * @return string
     */
    private function getAllowedTags() : string
    {
        $allowed = '';
        for ($i = 1; $i <= $this->depth; $i++) {
            $allowed .= sprintf('<h%d>', $i);
        }
        return $allowed;
    }

    /**
     * @return void
     */
    protected function parseAllowedTags() : void
    {
        $count = 0;
        $this->content = preg_replace_callback(sprintf("#<h[1-%d]*[^>]*>.*?<\\/h[1-%d]>#", $this->depth, $this->depth), function ($match) use (&$count) {
            $normalized = strip_tags($match[0], $this->getAllowedTags());
            $normalized = strtr($normalized, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));
            $normalized = trim($normalized, chr(0xC2) . chr(0xA0));
            $checkEmpty = preg_replace("/\s+/", "", strip_tags($normalized));
            if (empty($checkEmpty)) {
                return $match[0];
            }

            preg_match('~<([^/][^>]*?)>~', $normalized, $arrTag);
            $depthLevel = (int)substr($arrTag[1], 1);
            $anchor = 'toc-' . $count;
            $this->addToStructure($depthLevel, $normalized, $anchor);
            $count++;
            return preg_replace(sprintf('/<h([1-%s])>/', $this->depth), '<h$1><a href="#" name="' . $anchor . '"></a>', $match[0]);
        }, $this->content);
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return html_entity_decode($this->getTableOfContents()) . $this->content;
    }

    /**
     * @param int    $depthLevel
     * @param string $normalized
     * @param string $anchor
     */
    private function addToStructure(int $depthLevel, string $normalized, string $anchor) : void
    {
        --$depthLevel;
        $this->tagsStructure[] = [
              'name'   => strip_tags($normalized),
              'depth'  => $depthLevel,
              'anchor' => $anchor
        ];
    }

    protected function classExists(string $name) : bool
    {
        return class_exists($name);
    }

    /**
     * @return string
     */
    public function getTableOfContents() : string
    {
        if (!$this->classExists('DOMDocument')) {
            throw new TocException(sprintf('Class %s is missing', 'DOMDocument'));
        }

        $this->parseAllowedTags();

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $rootNode = $doc->createElement('div');
        $doc->appendChild($rootNode);

        $rootList = $doc->createElement('ul');
        $rootNode->appendChild($rootList);

        $listStack = [$rootList];
        $depth = 0;

        foreach ($this->tagsStructure as $nael) {
            while ($depth < $nael['depth']) {
                if ($listStack[$depth]->lastChild === null) {
                    $li = $doc->createElement('li');
                    $listStack[$depth]->appendChild($li);
                }
                $listEl = $doc->createElement('ul');
                $listStack[$depth]->lastChild->appendChild($listEl);
                $listStack[] = $listEl;

                $depth++;
            }

            while ($depth > $nael['depth']) {
                array_pop($listStack);
                $depth--;
            }

            $li = $doc->createElement('li');
            $a = $doc->createElement('a');
            $a->setAttribute('href', '#' . $nael['anchor']);
            $a->appendChild($doc->createTextNode($nael['name']));
            $li->appendChild($a);
            $listStack[$depth]->appendChild($li);
        }

        return '<h1 id="toc-header">Table of Contents</h1><hr />' . $doc->saveHTML() . '<hr />';
    }
}
