# Generates Table Of Contents
<p align="center">
    <a href="https://travis-ci.com/camoo/toc" target="_blank">
        <img alt="Build Status" src="https://travis-ci.com/camoo/toc.svg?branch=master">
    </a>
	<!-- a href="https://codecov.io/gh/camoo/toc">
		<img src="https://codecov.io/gh/camoo/toc/branch/master/graph/badge.svg" />
	</a -->
</p>

## Feature
This package parses a Html content with H-tags and generate Table of Contents with anchors in your content.

### Installation:
```bash
  composer require camoo/toc
```

### Usage
```php
        $toc = new TableOfContents('<h1>Html Content</h1>');
		// get only table of contents part
        $onlyTableOfContents = $toc->getTableOfContents();

		// get full content with table of contents included
        $fullContent = $toc->getContent();
```
#### Basic Example:

```php
$htmlContent = <<<END
    <h1>This is a header tag h1</h1>
    <p>Lorum ipsum doler sit amet</p>
    <h2>This is a header tag h2</h2>
    <p>Foo Bar</p>
    <h3>This is a header tag h3</h3>
END;

$toc = new TableOfContents($htmlContent);

$fullHtml  = $toc->getContent();


echo $fullHtml;
```

This produces the following output:

```html
<h1 id="toc-header">Table of Contents</h1>
<hr />
<div>
    <ul>
        <li>
            <a href="#toc-0">This is a header tag h1</a>
            <ul>
                <li>
                    <a href="#toc-1">This is a header tag h2</a>
                    <ul>
                        <li><a href="#toc-2">This is a header tag h3</a></li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>
<hr />
<h1><a href="#" name="toc-0"></a>This is a header tag h1</h1>
<p>Lorum ipsum doler sit amet</p>
<h2><a href="#" name="toc-1"></a>This is a header tag h2</h2>
<p>Foo Bar</p>
<h3><a href="#" name="toc-2"></a>This is a header tag h3</h3>
```

pecifying Heading Levels to Include
-------------------------------------------
You can choose to include only specific *h1...h6* heading levels in your TOC. By default the script is parsing h1-h6.
To do this, pass the optional parameter `$depth` to the constructor

```php
$htmlContent = '<h1>Tester</h1><b>Bold</b><h2>Sub-Test</h2><a href="#">link</a><h3>3rd Level</h3><h4>4rd level</h4>';

// Get TOC using h1-h3
$toc = new TableOfContents($htmlContent,3);
```
