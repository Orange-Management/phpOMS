<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\tests\Message\Http;

use phpOMS\Localization\Localization;
use phpOMS\Message\Http\Header;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\System\MimeType;

/**
 * @testdox phpOMS\tests\Message\HeaderTest: Header for http requests/responses
 *
 * @internal
 */
class HeaderTest extends \PHPUnit\Framework\TestCase
{
    protected Header $header;

    protected function setUp() : void
    {
        $this->header = new Header();
    }

    /**
     * @testdox The header has the expected default values after initialization
     * @covers phpOMS\Message\Http\Header
     */
    public function testDefaults() : void
    {
        self::assertFalse($this->header->isLocked());
        self::assertEquals(0, $this->header->getStatusCode());
        self::assertEquals('HTTP/1.1', $this->header->getProtocolVersion());
        self::assertEmpty(Header::getAllHeaders());
        self::assertEquals('', $this->header->getReasonPhrase());
        self::assertEquals([], $this->header->get('key'));
        self::assertFalse($this->header->has('key'));
        self::assertInstanceOf(Localization::class, $this->header->getL11n());
        self::assertEquals(0, $this->header->getAccount());
    }

    /**
     * @testdox Security policy headers get correctly identified
     * @covers phpOMS\Message\Http\Header
     */
    public function testSecurityHeader() : void
    {
        self::assertTrue(Header::isSecurityHeader('content-security-policy'));
        self::assertTrue(Header::isSecurityHeader('X-xss-protection'));
        self::assertTrue(Header::isSecurityHeader('x-conTent-tYpe-options'));
        self::assertTrue(Header::isSecurityHeader('x-frame-options'));
        self::assertFalse(Header::isSecurityHeader('x-frame-optionss'));
    }

    /**
     * @testdox Header data can be set, checked for existence and returned
     * @covers phpOMS\Message\Http\Header
     */
    public function testDataInputOutput() : void
    {
        self::assertTrue($this->header->set('key', 'header'));
        self::assertEquals(['header'], $this->header->get('key'));
        self::assertTrue($this->header->has('key'));
    }

    /**
     * @testdox Header data can be forced to get overwritten
     * @covers phpOMS\Message\Http\Header
     */
    public function testOverwrite() : void
    {
        self::assertTrue($this->header->set('key', 'header'));
        self::assertTrue($this->header->set('key', 'header3', true));
        self::assertEquals(['header3'], $this->header->get('key'));
    }

    /**
     * @testdox By default header data doesn't get overwritten
     * @covers phpOMS\Message\Http\Header
     */
    public function testInvalidOverwrite() : void
    {
        self::assertTrue($this->header->set('key', 'header'));
        self::assertFalse($this->header->set('key', 'header2'));
        self::assertEquals(['header'], $this->header->get('key'));
    }

    /**
     * @testdox Header data can be removed
     * @covers phpOMS\Message\Http\Header
     */
    public function testRemove() : void
    {
        self::assertTrue($this->header->set('key', 'header'));
        self::assertTrue($this->header->remove('key'));
        self::assertFalse($this->header->has('key'));
    }

    /**
     * @testdox None-existing header data cannot be removed
     * @covers phpOMS\Message\Http\Header
     */
    public function testInvalidRemove() : void
    {
        self::assertFalse($this->header->remove('key'));
    }

    /**
     * @testdox Account data can be set and returned
     * @covers phpOMS\Message\Http\Header
     */
    public function testAccountInputOutput() : void
    {
        $this->header->setAccount(2);
        self::assertEquals(2, $this->header->getAccount(2));
    }

    /**
     * @testdox Data can be defined as downloadable
     * @covers phpOMS\Message\Http\Header
     */
    public function testDownloadable() : void
    {
        $this->header->setDownloadable('testname', 'mp3');
        self::assertEquals(MimeType::M_BIN, $this->header->get('Content-Type')[0]);
    }

    /**
     * @testdox A header can be locked
     * @covers phpOMS\Message\Http\Header
     */
    public function testLockInputOutput() : void
    {
        $this->header->lock();
        self::assertTrue($this->header->isLocked());
    }

    /**
     * @testdox A locked header cannot add new data
     * @covers phpOMS\Message\Http\Header
     */
    public function testLockInvalidSet() : void
    {
        $this->header->lock();
        self::assertFalse($this->header->set('key', 'value'));
    }

    /**
     * @testdox A locked header cannot remove data
     * @covers phpOMS\Message\Http\Header
     */
    public function testLockInvalidRemove() : void
    {
        $this->header->lock();
        self::assertFalse($this->header->remove('key'));
    }

    /**
     * @testdox The header can generate default http headers based on status codes
     * @covers phpOMS\Message\Http\Header
     */
    public function testHeaderGeneration() : void
    {
        $this->header->generate(RequestStatusCode::R_403);
        self::assertEquals(403, \http_response_code());

        $this->header->generate(RequestStatusCode::R_404);
        self::assertEquals(404, \http_response_code());

        $this->header->generate(RequestStatusCode::R_406);
        self::assertEquals(406, \http_response_code());

        $this->header->generate(RequestStatusCode::R_407);
        self::assertEquals(407, \http_response_code());

        $this->header->generate(RequestStatusCode::R_503);
        self::assertEquals(503, \http_response_code());

        $this->header->generate(RequestStatusCode::R_500);
        self::assertEquals(500, \http_response_code());
    }

    /**
     * @testdox Security header data cannot be changed once defined
     * @covers phpOMS\Message\Http\Header
     */
    public function testInvalidOverwriteSecurityHeader() : void
    {
        self::assertTrue($this->header->set('content-security-policy', 'header'));
        self::assertFalse($this->header->set('content-security-policy', 'header', true));
    }
}
