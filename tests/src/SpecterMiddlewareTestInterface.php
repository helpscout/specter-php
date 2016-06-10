<?php
namespace HelpScout\Specter\Tests;

interface SpecterMiddlewareTestInterface
{
    public function testMiddlewareCanProcessSimpleJson();

    public function testMiddlewareCanIgnoreNonSpecterFile();

    public function testMiddlewareFailsOnInvalidJson();

    public function testMiddlewareFailsOnInvalidProviderJson();
}
