<!-- logo -->
<img src="https://cdn.yaglot.com/branding/160x160.png" height="40" />

# PHP library

## Overvie
This library allows you to quickly and easily use the Yaglot API via PHP. 

## Requirements
- PHP version 5.6 and later
- Yaglot API Key, from [dashboard](https://dashboard.yaglot.com)

## Installation
You can install the library via [Composer](https://getcomposer.org/). Run the following command:

```bash
composer require yaglot/yaglot-php
```

To use the library, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once __DIR__. '/vendor/autoload.php';
```

## Examples

You can take a look at: [examples](./examples) folder. You'll find a short README with details about each example.


### Parser

The Parser is a big part in our developer kits.

It's used to match sentences to translate from DOM and to make clean API objects to send them after through the Client.

There is no documentation for the Parser at the moment since we plan a heavy rework in next month on it, we'll make sure there is one after this rework.


## License
[The MIT License (MIT)](LICENSE.txt)
