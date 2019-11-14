<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\Contact;
use Ddrv\OpenApiGenerator\Exception\OneOfArgumentIsRequiredException;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string|null $name
     * @param string|null $url
     * @param string|null $email
     * @param string|null $exception
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function testConstruct(?string $name, ?string $url, ?string $email, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $contact = new Contact($name, $url, $email);

        $name = trim((string)$name);
        $url = trim((string)$url);
        $email = trim((string)$email);
        $count = 3;
        if (!$name) {
            $name = null;
            $count--;
        }
        if (!$url) {
            $url = null;
            $count--;
        }
        if (!$email) {
            $email = null;
            $count--;
        }
        $this->assertSame($name, $contact->getName());
        $this->assertSame($url, $contact->getUrl());
        $this->assertSame($email, $contact->getEmail());
        $array = $contact->toArray();
        $this->assertCount($count, $array);
        if ($name) {
            $this->assertArrayHasKey('name', $array);
            $this->assertSame($name, $array['name']);
        } else {
            $this->assertArrayNotHasKey('name', $array);
        }
        if ($url) {
            $this->assertArrayHasKey('url', $array);
            $this->assertSame($url, $array['url']);
        } else {
            $this->assertArrayNotHasKey('url', $array);
        }
        if ($email) {
            $this->assertArrayHasKey('email', $array);
            $this->assertSame($email, $array['email']);
        } else {
            $this->assertArrayNotHasKey('email', $array);
        }
    }

    /**
     * @dataProvider provideSetName
     *
     * @param string|null $name
     * @param string|null $exception
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function testSetName(?string $name, ?string $exception)
    {
        $check = trim((string)$name);
        if (!$check) {
            $check = null;
        }
        $contact = new Contact('Test Phpunit', null, null);
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $contact->setName($name);
        $this->assertSame($check, $contact->getName());
        $array = $contact->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame($check, $array['name']);
    }

    /**
     * @dataProvider provideSetUrl
     *
     * @param string      $url
     * @param string|null $exception
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function testSetUrl(?string $url, ?string $exception)
    {
        $check = trim((string)$url);
        if (!$check) {
            $check = null;
        }
        $contact = new Contact(null, 'https://phpunit.de', null);
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $contact->setUrl($url);
        $this->assertSame($check, $contact->getUrl());
        $array = $contact->toArray();
        $this->assertArrayHasKey('url', $array);
        $this->assertSame($check, $array['url']);
    }

    /**
     * @dataProvider provideSetEmail
     *
     * @param string      $email
     * @param string|null $exception
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function testSetEmail(?string $email, ?string $exception)
    {
        $check = trim((string)$email);
        if (!$check) {
            $check = null;
        }
        $contact = new Contact(null, null, 'suite@phpunit.de');
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $contact->setEmail($email);
        $this->assertSame($check, $contact->getEmail());
        $array = $contact->toArray();
        $this->assertArrayHasKey('email', $array);
        $this->assertSame($check, $array['email']);
    }

    public function provideConstruct(): array
    {
        return [
            ['Test Phpunit', 'https://phpunit.de', 'test@phpunit.de', null],
            ['Test Phpunit', null,                 null,              null],
            [null,           'https://phpunit.de', null,              null],
            [null,           null,                 'test@phpunit.de', null],
            [null,           null,                 null,              OneOfArgumentIsRequiredException::class],
            ['            ', '                  ', '               ', OneOfArgumentIsRequiredException::class],
        ];
    }

    public function provideSetName(): array
    {
        return [
            ['Name Surname', null],
            [' Support    ', null],
            ['            ', OneOfArgumentIsRequiredException::class],
            ['',             OneOfArgumentIsRequiredException::class],
            [null,           OneOfArgumentIsRequiredException::class],
        ];
    }

    public function provideSetUrl(): array
    {
        return [
            ['https://examples.com', null],
            [' http://hostname.io ', null],
            ['                    ', OneOfArgumentIsRequiredException::class],
            ['',                     OneOfArgumentIsRequiredException::class],
            [null,                   OneOfArgumentIsRequiredException::class],
        ];
    }

    public function provideSetEmail(): array
    {
        return [
            ['test@example.com', null],
            [' tt@example.com ', null],
            ['                ', OneOfArgumentIsRequiredException::class],
            ['',                 OneOfArgumentIsRequiredException::class],
            [null,               OneOfArgumentIsRequiredException::class],
        ];
    }
}
