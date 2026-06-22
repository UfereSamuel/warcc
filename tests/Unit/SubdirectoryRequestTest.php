<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SubdirectoryRequestTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_ENV['APP_URL'], $_SERVER['REQUEST_URI']);
        putenv('APP_URL');

        parent::tearDown();
    }

    public function test_strips_subdirectory_prefix_from_request_uri(): void
    {
        require_once __DIR__.'/../../bootstrap/subdirectory.php';

        $_ENV['APP_URL'] = 'https://cbp.africacdc.org/warcc';
        $_SERVER['REQUEST_URI'] = '/warcc/';

        warcc_strip_subdirectory_prefix_from_request();

        $this->assertSame('/', $_SERVER['REQUEST_URI']);
    }

    public function test_strips_subdirectory_prefix_from_nested_paths(): void
    {
        require_once __DIR__.'/../../bootstrap/subdirectory.php';

        $_ENV['APP_URL'] = 'https://cbp.africacdc.org/warcc';
        $_SERVER['REQUEST_URI'] = '/warcc/staff/attendance?tab=today';

        warcc_strip_subdirectory_prefix_from_request();

        $this->assertSame('/staff/attendance?tab=today', $_SERVER['REQUEST_URI']);
    }

    public function test_leaves_request_uri_unchanged_when_app_url_has_no_path(): void
    {
        require_once __DIR__.'/../../bootstrap/subdirectory.php';

        $_ENV['APP_URL'] = 'http://localhost';
        $_SERVER['REQUEST_URI'] = '/about';

        warcc_strip_subdirectory_prefix_from_request();

        $this->assertSame('/about', $_SERVER['REQUEST_URI']);
    }
}
