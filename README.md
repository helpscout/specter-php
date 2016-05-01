Specter [![Build Status](https://travis-ci.org/helpscout/specter.svg?branch=master)](https://travis-ci.org/helpscout/specter)
================================================================================
> __Mocking and Testing for PHP__
> Use a single JSON file to generate mock data and as an integration test assertion

Modern development is complicated. This project decouples front end and back end
development by providing fixture data and a testing spec with a single file.

1. Client and Server teams build [a JSON spec file][spec] together
2. Mock the endpoint, and have it return that spec file and add the
   [Specter Middleware][middleware] to convert that spec file into a response
   filled with random, but sane, data
3. The client team can begin development with this endpoint, and iterate over
   any changes with the JSON spec. The endpoint delivers real data, and they
   can set a `SpecterSeed` header to get repeatable results.
4. The server team can then implement the actual endpoint to meet that spec at
   their own pace, perhaps in the next sprint. They can use the **same** spec
   file to drive an PHPUnit integration test by handing the spec file to the
   [SpecterTestTrait][testtrait]

This lets the teams rapidly create an endpoint specification together, the
front end team uses the data from it, and the platform team tests with it.

## Installation

This project is not yet published on Packagist.

## Contributing
1. `git clone`
2. `composer install`
3. It will prompt you to please install our commit hooks driven by
   [pre-commit][pre-commit].


[spec]: https://raw.githubusercontent.com/helpscout/specter/master/tests/fixture/customer.json
[middleware]: https://github.com/helpscout/specter/blob/master/src/SpecterMiddleware.php
[testtrait]: https://github.com/helpscout/specter/blob/master/src/SpecterTestTrait.php
[pre-commit]: http://pre-commit.com/
