<?php

namespace Drupal\dept_autologin;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class DeptAutologinMiddleware to automatically login a user.
 */
class DeptAutologinMiddleware implements HttpKernelInterface {

  use ContainerAwareTrait;

  /**
   * The decorated kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The Autologin manager.
   *
   * @var \Drupal\dept_autologin\AutologinManagerServiceInterface
   */
  protected $autologinManager;

  /**
   * Constructs a new DeptAutologinMiddleware object.
   */
  public function __construct(HttpKernelInterface $http_kernel,
                              AccountInterface $current_user,
                              AutologinManagerServiceInterface $autologin_manager
  ) {
    $this->httpKernel = $http_kernel;
    $this->currentUser = $current_user;
    $this->autologinManager = $autologin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {

    // Check the query paramaters for an auth token.
    $auth_token = $request->query->get('authtoken');

    // If the user is already logged in or there is no auth token
    // do not interrupt the flow of Drupal. Middleware needs to be
    // kept as skinny as possible.
    if (!$this->applies($auth_token)) {
      return $this->httpKernel->handle($request, $type, $catch);
    }

    // Pass the token to the AutologinManagerService to log the user in.
    if ($this->autologinManager->loginUser($auth_token)) {
      return $this->httpKernel->handle($request, $type, $catch);
    }
    else {
      // Return a basic response. We could redirect to Access Denied here.
      return new Response(new FormattableMarkup('The auth token @auth_token is unknown', ['@auth_token' => $auth_token]), 403);
    }
  }

  /**
   * Check if the middleware applies to this request.
   *
   * @param string $auth_token
   *   The auth token from the url if it is supplied.
   *
   * @return bool
   *   Returns TRUE if applies. False if not applicable.
   */
  public function applies($auth_token) {
    if ($this->currentUser->isAuthenticated() || !$auth_token) {
      // If the use is already logged in or there is no auth token, do not go
      // further as every request hits this this middleware.
      return FALSE;
    }
    else {
      // As the user is not logged in and has a token we can attempt to log
      // the user in.
      return TRUE;
    }
  }

}
