<?php

namespace Tests\Unit\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use App\Http\Middleware\EnsureCorrectAPIHeaders;

class EnsureCorrectApiHeaderTest extends TestCase
{
    /** @test */
    public function it_aborts_request_if_accept_header_does_not_adhere_to_json_api_spec()
    {
        $request = Request::create('/test', 'GET');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            $this->fail('Did not abort request because of invalid Accept header');
        });

        $this->assertEquals(406, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_accept_request_if_accept_header_adheres_to_json_api_spec()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('accept', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_aborts_post_request_if_content_type_header_does_not_adhere_to_json_api_spec()
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('accept', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            $this->fail('Did not abort request because of invalid Content-Type header');
        });

        $this->assertEquals(415, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_aborts_patch_request_if_content_type_header_does_not_adhere_to_json_api_spec()
    {
        $request = Request::create('/test', 'PATCH');
        $request->headers->set('accept', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            $this->fail('Did not abort request because of invalid Content-Type header');
        });

        $this->assertEquals(415, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_accept_post_request_if_accept_header_adheres_to_json_api_spec()
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('accept', 'application/vnd.api+json');
        $request->headers->set('content-type', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_accept_patch_request_if_accept_header_adheres_to_json_api_spec()
    {
        $request = Request::create('/test', 'PATCH');
        $request->headers->set('accept', 'application/vnd.api+json');
        $request->headers->set('content-type', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_ensures_that_a_content_type_header_adhering_to_json_api_spec_is_on_response()
    {
        $request = Request::create('/test', 'PATCH');
        $request->headers->set('accept', 'application/vnd.api+json');
        $request->headers->set('content-type', 'application/vnd.api+json');

        $middleware = new EnsureCorrectAPIHeaders();

        /** @var Response $response */
        $response = $middleware->handle($request, function ($request) {
            return new Response();
        });

        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/vnd.api+json', $response->headers->get('content-type'));
    }
}
