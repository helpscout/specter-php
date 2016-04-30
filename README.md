Specter [![Build Status](https://travis-ci.org/helpscout/specter.svg?branch=master)](https://travis-ci.org/helpscout/specter)
================================================================================
> __Mocking and Testing for PHP__
> Use a single JSON file to generate mock data and as an integration test assertion

Modern development is complicated. This project will help you decouple API
development from client implementation in three steps.

1. Client and Server teams build a JSON spec file together
2. With a couple lines of code the API begins offering an endpoint response
   that meets the JSON spec, but delivers it with sane, random data.
3. The client team can begin development with this endpoint, and iterate over
   and challenges with the JSON spec.
4. The server team can then implement the actual endpoint to meet that spec by
   using the same JSON spec file as part of their integration testing with
   PHPUnit.
