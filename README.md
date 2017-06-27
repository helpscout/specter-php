Specter [![Build Status](https://travis-ci.org/helpscout/specter.svg?branch=master)](https://travis-ci.org/helpscout/specter) [![Code Climate](https://codeclimate.com/github/helpscout/specter/badges/gpa.svg)](https://codeclimate.com/github/helpscout/specter) [![Test Coverage](https://codeclimate.com/github/helpscout/specter/badges/coverage.svg)](https://codeclimate.com/github/helpscout/specter/coverage)
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

This is available through composer as `helpscout/specter`.

## Contributing
1. `git clone`
2. `composer install`
3. It will prompt you to please install our commit hooks driven by
   [pre-commit][pre-commit].

## Demonstration

Work together among your development teams to spec a new endpoint and create a
Specter JSON file that defines your new endpoint. This is a Specter JSON file:
```json
{
  "__specter": "Sample customer record",
  "id": "@randomDigitNotNull@",
  "fname": "@firstName@",
  "lname": "@lastName@",
  "company": "@company@",
  "jobTitle": "@jobTitle@",
  "background": "@catchPhrase@",
  "address": {
    "city": "@city@",
    "state": "@stateAbbr@",
    "zip": "@postcode@",
    "country": "@country@"
  },
  "emails": ["@companyEmail@", "@freeEmail@", "@email@" ]
}
```

Add a route to return it and use `SpecterMiddleware` to process it:
```php
$app->get('/api/v1/customer/{id}', function ($request, $response, $args) {
    return $response->withJson(getFixture('customer'));
})->add(new \HelpScout\Specter\SpecterMiddleware);
```

Receive random data from your endpoint that fulfills the JSON and use it to
build out your interface:
```json
{
   "__specter":"Sample customer record",
   "id":6,
   "fname":"Glenda",
   "lname":"Trantow",
   "company":"Kerluke, Rodriguez and Wisoky",
   "jobTitle":"Power Generating Plant Operator",
   "background":"Configurable multi-state standardization",
   "address":{
      "city":"Georgiannachester",
      "state":"TX",
      "zip":"89501",
      "country":"Afghanistan"
   },
   "emails":[
      "dward@friesen.org",
      "nwisozk@gmail.com",
      "juliet.dooley@yahoo.com"
   ]
}
```

Write a unit test for the endpoint to confirm that it's meeting the spec, and
then implement the endpoint for real:
```php
use SpecterTestTrait;

public function testCustomerRouteMeetsSpec()
{
    self::assertResponseContent(
        $this->client->get('/api/v1/customer/37'),
        'customer'
    );
}
```

## Custom Formatters

In addition to the Faker library, Specter provides a few
[other fomatters](https://github.com/helpscout/specter/tree/master/src/Provider)
that offer some useful mocking.

* `randomRobotAvatar`
* `randomGravatar`
* `relatedElement`

[spec]: https://raw.githubusercontent.com/helpscout/specter/master/tests/fixture/customer.json
[middleware]: https://github.com/helpscout/specter/blob/master/src/SpecterMiddleware.php
[testtrait]: https://github.com/helpscout/specter/blob/master/src/SpecterTestTrait.php
[pre-commit]: http://pre-commit.com/
.
