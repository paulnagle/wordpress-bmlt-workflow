<?php

declare(strict_types=1);

use wbw\REST\Handlers\BMLTServerHandler;

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;
use function Patchwork\{redefine, getFunction, always};
require_once('config_phpunit.php');


/**
 * @covers wbw\REST\Handlers\BMLTServerHandler
 * @uses wbw\WBW_Debug
 * @uses wbw\REST\HandlerCore
 * @uses wbw\BMLT\Integration
 * @uses wbw\WBW_WP_Options
 */
final class BMLTServerHandlerTest extends TestCase
{
    use \wbw\WBW_Debug;

    protected function setVerboseErrorHandler()
    {
        $handler = function ($errorNumber, $errorString, $errorFile, $errorLine) {
            echo "
ERROR INFO
Message: $errorString
File: $errorFile
Line: $errorLine
";
        };
        set_error_handler($handler);
    }

    protected function setUp(): void
    {

        $this->setVerboseErrorHandler();
        $basedir = getcwd();
        require_once($basedir . '/vendor/antecedent/patchwork/Patchwork.php');
        require_once($basedir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/class-wp-error.php');
        require_once($basedir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/class-wp-http-response.php');
        require_once($basedir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/rest-api/endpoints/class-wp-rest-controller.php');
        require_once($basedir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/rest-api/class-wp-rest-response.php');
        require_once($basedir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/rest-api/class-wp-rest-request.php');
        if (!class_exists('wpdb')){
            require_once($basedir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/wp-db.php');
        }

        Brain\Monkey\setUp();

        Functions\when('\wp_json_encode')->returnArg();
        Functions\when('\apply_filters')->returnArg(2);
        Functions\when('\current_time')->justReturn('2022-03-23 09:22:44');
        Functions\when('\absint')->returnArg();
        Functions\when('wp_safe_remote_post')->returnArg();


    }

    protected function tearDown(): void
    {
        Brain\Monkey\tearDown();
        parent::tearDown();
        Mockery::close();

        unset($this->wbw_dbg);
    }

// test for GET bmltserver (get server test settings)
    /**
     * @covers wbw\REST\Handlers\BMLTServerHandler::get_bmltserver_handler
     */
    public function test_can_get_bmltserver_with_success(): void
    {
        
        $request = new WP_REST_Request('GET', "http://54.153.167.239/flop/wp-json/wbw/v1/bmltserver");
        $request->set_header('content-type', 'application/json');
        $request->set_route("/wbw/v1/bmltserver");
        $request->set_method('GET');

        $WBW_WP_Options =  Mockery::mock('WBW_WP_Options');
        /** @var Mockery::mock $WBW_WP_Options test */
        $WBW_WP_Options->shouldReceive('wbw_get_option')->andReturn("success");

        $rest = new BMLTServerHandler(null, $WBW_WP_Options);

        $response = $rest->get_bmltserver_handler($request);

        $this->debug_log(($response));

        $this->assertInstanceOf(WP_REST_Response::class, $response);

        $this->assertEquals($response->get_data()['wbw_bmlt_test_status'], 'success');
    }

    // test for GET bmltserver (get server test settings)
    /**
     * @covers wbw\REST\Handlers\BMLTServerHandler::get_bmltserver_handler
     */
    public function test_can_get_bmltserver_with_failure(): void
    {

        $request = new WP_REST_Request('GET', "http://54.153.167.239/flop/wp-json/wbw/v1/bmltserver");
        $request->set_header('content-type', 'application/json');
        $request->set_route("/wbw/v1/bmltserver");
        $request->set_method('GET');

        $WBW_WP_Options =  Mockery::mock('WBW_WP_Options');
        /** @var Mockery::mock $WBW_WP_Options test */
        $WBW_WP_Options->shouldReceive('wbw_get_option')->andReturn("failure");

        $rest = new BMLTServerHandler(null,$WBW_WP_Options);

        $response = $rest->get_bmltserver_handler($request);

        
        $this->debug_log(($response));

        $this->assertInstanceOf(WP_REST_Response::class, $response);

        $this->assertEquals($response->get_data()['wbw_bmlt_test_status'], 'failure');
    }

    // test for POST bmltserver
    /**
     * @covers wbw\REST\Handlers\BMLTServerHandler::post_bmltserver_handler
     */

    public function test_can_post_bmltserver_with_valid_parameters(): void
    {

        $request = new WP_REST_Request('POST', "http://54.153.167.239/flop/wp-json/wbw/v1/bmltserver");
        $request->set_header('content-type', 'application/json');
        $request->set_route("/wbw/v1/bmltserver");
        $request->set_method('POST');
        $request->set_param('wbw_bmlt_server_address', 'http://1.1.1.1/main_server/');
        $request->set_param('wbw_bmlt_username', 'test');
        $request->set_param('wbw_bmlt_password', 'test');

        $WBW_WP_Options =  Mockery::mock('WBW_WP_Options');
        /** @var Mockery::mock $WBW_WP_Options test */
        $WBW_WP_Options->shouldReceive('wbw_get_option')->andReturn("success");

        Functions\when('\update_option')->returnArg(1);

        $stub = \Mockery::mock('Integration');
        /** @var Mockery::mock $stub test */
        $stub->shouldReceive('testServerAndAuth')->andReturn('true');

        Functions\when('\is_wp_error')->justReturn(false);

        $rest = new BMLTServerHandler($stub);

        $response = $rest->post_bmltserver_handler($request);

        
        $this->debug_log(($response));

        $this->assertInstanceOf(WP_REST_Response::class, $response);

        $this->assertEquals($response->get_data()['wbw_bmlt_test_status'], 'success');
    }

    // test for POST bmltserver

    /**
     * @covers wbw\REST\Handlers\BMLTServerHandler::post_bmltserver_handler
     */
    public function test_cant_post_bmltserver_with_invalid_server(): void
    {
        $request = new WP_REST_Request('POST', "http://54.153.167.239/flop/wp-json/wbw/v1/bmltserver");
        $request->set_header('content-type', 'application/json');
        $request->set_route("/wbw/v1/bmltserver");
        $request->set_method('POST');
        $request->set_param('wbw_bmlt_server_address', 'test');
        $request->set_param('wbw_bmlt_username', 'test');
        $request->set_param('wbw_bmlt_password', 'test');

        $WBW_WP_Options =  Mockery::mock('WBW_WP_Options');
        /** @var Mockery::mock $WBW_WP_Options test */
        $WBW_WP_Options->shouldReceive('wbw_get_option')->andReturn("success");

        Functions\when('\update_option')->returnArg(1);
        $rest = new BMLTServerHandler();

        $response = $rest->post_bmltserver_handler($request);

        
        $this->debug_log(($response));

        $this->assertInstanceOf(WP_Error::class, $response);
        $this->assertEquals($response->get_error_data()['wbw_bmlt_test_status'], 'failure');
    }

    /**
     * @covers wbw\REST\Handlers\BMLTServerHandler::post_bmltserver_handler
     */
    public function test_cant_post_bmltserver_with_blank_username(): void
    {
        $request = new WP_REST_Request('POST', "http://54.153.167.239/flop/wp-json/wbw/v1/bmltserver");
        $request->set_header('content-type', 'application/json');
        $request->set_route("/wbw/v1/bmltserver");
        $request->set_method('POST');
        $request->set_param('wbw_bmlt_server_address', 'test');
        $request->set_param('wbw_bmlt_username', '');
        $request->set_param('wbw_bmlt_password', 'test');

        $WBW_WP_Options =  Mockery::mock('WBW_WP_Options');
        /** @var Mockery::mock $WBW_WP_Options test */
        $WBW_WP_Options->shouldReceive('wbw_get_option')->andReturn("success");

        Functions\when('\update_option')->returnArg(1);
        $rest = new BMLTServerHandler();

        $response = $rest->post_bmltserver_handler($request);

        $this->debug_log(($response));

        $this->assertInstanceOf(WP_Error::class, $response);
        $this->assertEquals($response->get_error_data()['wbw_bmlt_test_status'], 'failure');
    }

    /**
     * @covers wbw\REST\Handlers\BMLTServerHandler::post_bmltserver_handler
     */
    public function test_cant_post_bmltserver_with_blank_password(): void
    {
        $request = new WP_REST_Request('POST', "http://54.153.167.239/flop/wp-json/wbw/v1/bmltserver");
        $request->set_header('content-type', 'application/json');
        $request->set_route("/wbw/v1/bmltserver");
        $request->set_method('POST');
        $request->set_param('wbw_bmlt_server_address', 'test');
        $request->set_param('wbw_bmlt_username', 'test');
        $request->set_param('wbw_bmlt_password', '');

        $WBW_WP_Options =  Mockery::mock('WBW_WP_Options');
        /** @var Mockery::mock $WBW_WP_Options test */
        $WBW_WP_Options->shouldReceive('wbw_get_option')->andReturn("success");

        Functions\when('\update_option')->returnArg(1);
        $rest = new BMLTServerHandler();

        $response = $rest->post_bmltserver_handler($request);

        
        $this->debug_log(($response));

        $this->assertInstanceOf(WP_Error::class, $response);
        $this->assertEquals($response->get_error_data()['wbw_bmlt_test_status'], 'failure');
    }
}