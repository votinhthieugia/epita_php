<?php
 /**
  * <p>Ancestor of resources used as REST Web Services.
  * Responds to methods
  * GET, PUT, DELETE, POST and HEAD, setting status code,
  * headers and body of the response in its attributes
  * $statusCode, $headers and $body.
  * <br/>By default, send back "405, method not allowed".</p>
  * <p>Usage: for your particular resource, make a class
  * heir of HttpResource, and at the file end, run its method
  * <code>run</code>. Example:</p>
  * <pre>
  * &lt;?php
  * class MyRessource extends HttpResource {
  *   function do_get() {
  *     // your code here
  *     $this->statusCode = ...;
  *     $this->headers = ...;
  *     $this->body = ...;
  *   }
  *   // Même chose pour do_put, do_post etc.
  * }
  * MyRessource::run();
  * ?></pre>
  * <p><code>run</code> instantiate the class, exécutes its procedure
  * <code>init</code> then its do_get, do_put etc. according to the
  * request HTTP method.</p>
  * By default, GET and HEAD send back 200,
  * PUT and DELETE 204, POST 201.
  * <p>Require PHP 5.3 at least (to instantiate the class in
  * procedure <code>run</code>. If you have a previous version of PHP,
  * please see the comment on the run method.</p>
  */
class HttpResource {
  /** HTTP response status (200, 201, 204, 400, 401, 404, 409 etc.)
  * <br/>By default, 200 in GET and HEAD, 201 in POST,
   * and 204 in PUT and DELETE.
  */
  protected $statusCode;

  /** Response headers */
  protected $headers = array();

  /** Response body. Par default, empty */
  protected $body = "";

  /** Initialize the resource. For example, get the id in the URL
   * if the resource depends on it. By default, do nothing.
   */
  protected function init() {
  }

  /** Response to DELETE. By default, send back 405 (not allowed) */
  protected function do_delete() {
    $this->statusCode = 405;
  }

  /** Response to GET. By default, send back 405 (not allowed) */
  protected function do_get() {
    $this->statusCode = 405;
  }

  /** Response to HEAD. By default, send back 405 (not allowed) */
  protected function do_head() {
    $this->statusCode = 405;
  }

  /** Response to OPTIONS. By default, send back 405 (not allowed) */
  protected function do_options() {
    $this->statusCode = 405;
  }

  /** Response to POST. By default, send back 405 (not allowed) */
  protected function do_post() {
    $this->statusCode = 405;
  }

  /** Response to PUT. By default, send back 405 (not allowed) */
  protected function do_put() {
    $this->statusCode = 405;
  }

  /** Send back the status line with its associated message.
    */
  public static function send_status($codeHttp) {
    header("HTTP/1.1 $codeHttp ".self::$http_codes[$codeHttp]);
  }

  /** Set the status line, and the body as $message (optional),
   * and send the response.
    */
  public static function exit_error($codeHttp, $message = "") {
    self::send_status($codeHttp);
    die($message);
  }

  /** Response to a request. */
  // If PHP < 5.3, remove static, replace instance by this and
  // begin at $this->init.
  // In descending classes, call run in the constructor,
  // and end the resource php file by: new MyResource();
  // This bypass the lack of get_called_class.
  public static function run() {
    // Instantiate the classe which inherits HttpResource
    $className = get_called_class();
    $instance = new $className;
    // Initialize the instance
    $instance->init();
    // Responds
    switch ($_SERVER['REQUEST_METHOD']) {
      case "DELETE":
        $instance->statusCode = 204;
        $instance->do_delete();
        break;
      case "GET":
        $instance->statusCode = 200;
        $instance->do_get();
        break;
      case "HEAD":
        $instance->statusCode = 200;
        $instance->do_head();
        break;
      case "OPTIONS":
        $instance->statusCode = 200;
        $instance->do_options();
        break;
      case "POST":
        $instance->statusCode = 201;
        $instance->do_post();
        break;
      case "PUT":
        $instance->statusCode = 204;
        $instance->do_put();
        break;
      default:
        $instance->statusCode = 405;
    }
    $instance->send_status($instance->statusCode);
    foreach ($instance->headers as $i => $header) {
      header($header);
    }
    print $instance->body;
  }

  /** HTTP codes. Specially:
    * 200 => "OK",
    * 201 => "Created",
    * 204 => "No content",
    * 400 => "Bad request",
    * 401 => "Unauthorized",
    * 404 => "Not found",
    * 405 => "Method not allowed",
    * 409 => "Conflict",
    * 500 => "Internal server error");
    */
  protected static $http_codes = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Switch Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    425 => 'Unordered Collection',
    426 => 'Upgrade Required',
    449 => 'Retry With',
    450 => 'Blocked by Windows Parental Controls',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    509 => 'Bandwidth Limit Exceeded',
    510 => 'Not Extended'
  );
}

