Table of Contents generator for cebe/markdown
=============================================

This package provides a PHP _trait_ that to use with [cebe's Markdown implementation][cebe/markdown].
It allows to generate ToC from headlines in Makrdown documents and render them as lists.

Installation
------------

Recommended installation is via [composer][composer] by running:

    composer require czukowski/markdown-toc "~1.0"

Alternatively you may add the following to the `require` section of your project's `composer.json`
manually and then run `composer update` from the command line:

```json
"czukowski/markdown-toc: "~1.0"
```

Usage
-----

This package provides a `TOCTrait` that may be used in classes extending the cebe's original Markdown
parsers.

For more information on how to extend the parser, refer to the [original Readme file][markdown-usage].

License
-------

The distribution is permitted under the MIT License. See LICENSE.md for details.


  [cebe/markdown]: https://github.com/cebe/markdown
  [composer]: https://getcomposer.org/
  [markdown-usage]: https://github.com/cebe/markdown#usage
