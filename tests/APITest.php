<?php
/**
 * PHPUnit test for the API class using Brain Monkey.
 */
 
namespace Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use CoolKidsNetwork\API;

class APITest extends TestCase {
    protected function setUp(): void {
        Monkey\setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
    }

    public function test_update_user_roles_invalid_role() {
        Functions\when('get_user_by')->justReturn(false);

        $request = new class {
            public function get_json_params() {
                return ['users' => [['identifier' => 'test@example.com', 'role' => 'invalid_role']]];
            }
        };

        $result = API::update_user_roles($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('results', $result);
        $this->assertEquals('Invalid role specified.', $result['results'][0]['message']);
    }
}