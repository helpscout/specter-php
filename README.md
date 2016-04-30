Specter :: Mocking and Testing for PHP
================================================================================
> Use a single JSON file to generate mock data and as a test assertion

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
