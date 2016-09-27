Table of Contents generator for cebe/markdown
=============================================

This package provides a PHP _trait_ that to use with [cebe's Markdown implementation][cebe/markdown].
It allows to generate ToC from headlines in Makrdown documents and render them as lists. Note: the
output is a rendered HTML, not Markdown!

Installation
------------

Recommended installation is via [composer][composer] by running:

    composer require czukowski/markdown-toc "~1.0"

Alternatively you may add the following to the `require` section of your project's `composer.json`
manually and then run `composer update` from the command line:

```json
"czukowski/markdown-toc": "~1.0"
```

Usage
-----

This package provides a `TOCTrait` that may be used in classes extending the cebe's original Markdown
parsers.

For more information on how to extend the parser, refer to the [original Readme file][markdown-usage].

Generating Table of Contents can then be done by calling either of the two available public methods,
depending on whether you are generating ToC from one or multiple files:

```php
$markdown = new MyMarkdownWithTOC;

// Generate ToC from a single file:
$toc1 = $markdown->generateTableOfContents($source, 'index.md');

// Generate ToC from multiple files:
$toc2 = $markdown->generateTableOfContentsFromMultiple([
    [$intro, 'intro.md'],
    [$issues, 'issues.md'],
    [$reference, 'reference.md'],
]);
```

In the examples above, it's assumed that you've created a class named `MyMarkdownWithTOC` that uses
`TOCTrait`.

Additional optional arguments are available that define the list type used and limit the headline
levels that ToC is generated from.

Reference
---------

 - `generateTableOfContents($markdown, $url, $listType = 'ul', $fromLevel = 1, $toLevel = 6)`:
   * @param   _string_   `$markdown`   markdown source.
   * @param   _string_   `$url`        source URL.
   * @param   _string_   `$listType`   supported values: `ul` and `ol`.
   * @param   _integer_  `$fromLevel`  use headlines starting with this level
   * @param   _integer_  `$toLevel`    use headlines up to this level
   * @return  _string_

 - `generateTableOfContentsFromMultiple($sources, $listType = 'ul', $fromLevel = 1, $toLevel = 6)`:
   * @param   _array_    `$sources`    markdown sources and URLs.
   * @param   _string_   `$listType`   supported values: `ul` and `ol`.
   * @param   _integer_  `$fromLevel`  use headlines starting with this level
   * @param   _integer_  `$toLevel`    use headlines up to this level
   * @return  _string_

License
-------

The distribution is permitted under the MIT License. See LICENSE.md for details.


  [cebe/markdown]: https://github.com/cebe/markdown
  [composer]: https://getcomposer.org/
  [markdown-usage]: https://github.com/cebe/markdown#usage
