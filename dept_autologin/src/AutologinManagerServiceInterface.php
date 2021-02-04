<?php

namespace Drupal\dept_autologin;

/**
 * Interface AutologinManagerServiceInterface automatically logs in a user.
 */
interface AutologinManagerServiceInterface {

  /**
   * Function for logging in a user.
   *
   * @param string $auth_token
   *   The user supplied token from the url.
   *
   * @return bool
   *   Returns TRUE if the user is logged or False if not.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function loginUser($auth_token);

  /**
   * Check if token is valid length.
   *
   * @param string $token
   *   The token from the query string.
   *
   * @return bool
   *   Checks if the the token is a valid length.
   */
  public function isValidToken($token);

  /**
   * Searches a database table for a valid user with the supplied token.
   *
   * @param string $token
   *   The token from the query string.
   *
   * @return string|bool
   *   Returns a user id or false.
   */
  public function findUserByToken($token);

}
