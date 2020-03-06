# Generates Table Of Contents

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
