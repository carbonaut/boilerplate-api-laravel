<?php

namespace Tests\Unit\App\Support;

use App\Support\Helpers;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HelpersTest extends TestCase
{
    /**
     * @dataProvider ips
     *
     * @param string $ip
     * @param string $expect
     *
     * @test
     */
    public function ipIsCorrectlyAnonymized(string $ip, string $expect)
    {
        $this->assertEquals(Helpers::anonymize_ip_address($ip), $expect);
    }

    public function ips()
    {
        return [
            ['207.142.131.005', '207.142.131.0'],
            ['2001:0db8::08d3::8a2e:0070:7344', '2001:0db8::::::'],
            ['2001:0db8::08d3::8a2e:0070:734a', '2001:0db8::::::'],
            ['207.142.131.5', '207.142.131.0'],
            ['2001:0db8::8d3::8a2e:7:7344', '2001:0db8::::::'],
            [':0db8::8d3::8a2e:7:7344', ':0db8::::::'],
            [':0db8::::8a2e:7:7344', ':0db8::::::'],
            [':::abcd:adbc:adfa:adfa:aedf', ':::::::'],
            ['127.0.0.1', '127.0.0.0'],
            ['hello', 'hello'],
            ['', ''],
        ];
    }
}
