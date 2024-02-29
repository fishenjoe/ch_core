<?php

namespace CH\ChCore\Tests\Detections\BrowserDetection;

use CH\ChCore\Detections\BrowserDetection;
use PHPUnit\Framework\TestCase;

class BrowserDetectionTest extends TestCase
{
    protected BrowserDetection $detection;

    protected function setUp(): void
    {
        $this->detection = new BrowserDetection();
    }

    public function testInternetExplorer11IsOutdated(): void
    {
        $this->assertTrue(
            $this->detection->isBrowserOutdated(
                'User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)'
            )
        );
    }

    public function testModernChromeIsNotOutdated(): void
    {
        $this->assertFalse(
            $this->detection->isBrowserOutdated(
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36'
            )
        );
    }

    public function testModernFirefoxIsNotOutdated(): void
    {
        $this->assertFalse(
            $this->detection->isBrowserOutdated(
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0'
            )
        );
    }
}
