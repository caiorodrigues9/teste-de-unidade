<?php

namespace Caio\Leilao\Tests\Integration\Web;

use PHPUnit\Framework\TestCase;

class RestApiTest extends TestCase
{
    public function testApiRestDeveRetornarArrayDeleiloes()
    {
        $resposta = file_get_contents('http://localhost:8000/rest.php');
        self::assertStringContainsString('200 OK',$http_response_header[0]);
        self::assertIsArray(json_decode($resposta));
    }
}