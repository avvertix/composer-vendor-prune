[![Build Status](https://travis-ci.org/avvertix/composer-vendor-prune.svg?branch=master)](https://travis-ci.org/avvertix/composer-vendor-prune)

# Composer Prune

> Clean the PHP [Composer](https://getcomposer.org/) `vendor` folder from files not needed in production

The vendor-prune remove `*.md` and `*.dist` files from the packages pulled via [Composer](https://getcomposer.org/). This might be useful for reducing the size of the production code.

## Usage

Download the build from the [release page](https://github.com/avvertix/composer-vendor-prune/releases) inside your project root folder.
Execute

```bash
vendor-prune.phar [--dry-run]
# symfony/console 882.97KB => 22.67KB 2.57%
# symfony/finder 135.02KB => 2.78KB 2.06%
```

**options**

- `--dry-run`: Do not a real pruning, but list the packages, their size and the expected size after pruning
- `--vendor-folder`: Specify the vendor folder that contains the packages, by default is assumed `./vendor`


## Tests

The project is covered with Unit Tests.

```bash
composer install
vendor/bin/phpunit
```

## Contributing

Pull requests are very welcomed!

You can contribute by fixing bugs or by improving the documentation. Be sure to base your branch on `master`.

## License

This project is licensed under the MIT license, see [LICENSE.txt](./LICENSE.txt).
