<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\Contact;
use Ddrv\OpenApiGenerator\Document\Info;
use Ddrv\OpenApiGenerator\Document\License;
use Ddrv\OpenApiGenerator\Exception\InvalidTitleException;
use Ddrv\OpenApiGenerator\Exception\InvalidVersionException;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $title
     * @param string      $version
     * @param string|null $exception
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testConstruct(string $title, string $version, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $info = new Info($title, $version);
        $title = trim($title);
        $version = trim($version);
        $this->assertSame($title, $info->getTitle());
        $this->assertSame($version, $info->getVersion());
        $array = $info->toArray();
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('version', $array);
    }

    /**
     * @dataProvider provideTitle
     *
     * @param string      $title
     * @param string|null $exception
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testSetTitle(string $title, ?string $exception)
    {
        $info = new Info('test', '1.0.0');
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $info->setTitle($title);
        $title = trim($title);
        $this->assertSame($title, $info->getTitle());
        $array = $info->toArray();
        $this->assertArrayHasKey('title', $array);
        $this->assertSame($title, $array['title']);
    }

    /**
     * @dataProvider provideVersion
     *
     * @param string      $version
     * @param string|null $exception
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testSetVersion(string $version, ?string $exception)
    {
        $info = new Info('test', '1.0.0');
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $info->setVersion($version);
        $version = trim($version);
        $this->assertSame($version, $info->getVersion());
        $array = $info->toArray();
        $this->assertArrayHasKey('version', $array);
        $this->assertSame($version, $array['version']);
    }

    /**
     * @dataProvider provideDescription
     *
     * @param string|null $description
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testSetDescription(?string $description)
    {

        $info = new Info('test', '1.0.0');
        $info->setDescription($description);
        $description = trim((string)$description);
        if (!$description) {
            $description = null;
        }
        $this->assertSame($description, $info->getDescription());
        $array = $info->toArray();
        if ($description) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($description, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
    }

    /**
     * @dataProvider provideTerms
     *
     * @param string|null $termsOfService
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testSetTermsOfService(?string $termsOfService)
    {

        $info = new Info('test', '1.0.0');
        $info->setTermsOfService($termsOfService);
        $termsOfService = trim((string)$termsOfService);
        if (!$termsOfService) {
            $termsOfService = null;
        }
        $this->assertSame($termsOfService, $info->getTermsOfService());
        $array = $info->toArray();
        if ($termsOfService) {
            $this->assertArrayHasKey('termsOfService', $array);
            $this->assertSame($termsOfService, $array['termsOfService']);
        } else {
            $this->assertArrayNotHasKey('termsOfService', $array);
        }
    }

    /**
     * @dataProvider provideContact
     *
     * @param Contact|null $contact
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testSetContact(?Contact $contact)
    {

        $info = new Info('test', '1.0.0');
        $info->setContact($contact);
        $array = $info->toArray();
        if ($contact) {
            $this->assertInstanceOf(Contact::class, $info->getContact());
            $this->assertArrayHasKey('contact', $array);
            $this->assertSame(json_encode($contact->toArray()), json_encode($array['contact']));
        } else {
            $this->assertArrayNotHasKey('contact', $array);
            $this->assertNull($info->getContact());
        }
    }

    /**
     * @dataProvider provideLicense
     *
     * @param License|null $license
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function testSetLicense(?License $license)
    {

        $info = new Info('test', '1.0.0');
        $info->setLicense($license);
        $array = $info->toArray();
        if ($license) {
            $this->assertInstanceOf(License::class, $info->getLicense());
            $this->assertArrayHasKey('license', $array);
            $this->assertSame(json_encode($license->toArray()), json_encode($array['license']));
        } else {
            $this->assertNull($info->getLicense());
            $this->assertArrayNotHasKey('license', $array);
        }
    }

    public function provideConstruct(): array
    {
        return [
            ['Test App', '1.0.0', null],
            [' Test   ', ' 1.0 ', null],
            ['        ', '1.0.0', InvalidTitleException::class],
            ['',         '1.0.0', InvalidTitleException::class],
            ['Test App', ' ',     InvalidVersionException::class],
            ['Test App', '',      InvalidVersionException::class],
        ];
    }

    public function provideTitle(): array
    {
        return [
            ['Test App', null],
            [' Test   ', null],
            ['        ', InvalidTitleException::class],
            ['',         InvalidTitleException::class],
        ];
    }

    public function provideVersion(): array
    {
        return [
            ['1.0.0', null],
            [' 1.0 ', null],
            [' ',     InvalidVersionException::class],
            ['',      InvalidVersionException::class],
        ];
    }

    public function provideDescription(): array
    {
        return [
            [null],
            ['description'],
            [' description '],
        ];
    }

    public function provideTerms(): array
    {
        return $this->provideDescription();
    }

    public function provideContact(): array
    {
        return [
            [null],
            [new Contact('PHPUnit', null, null)],
        ];
    }

    public function provideLicense(): array
    {
        return [
            [null],
            [new License('MIT', 'https://opensource.org/licenses/mit-license.php')],
        ];
    }
}
