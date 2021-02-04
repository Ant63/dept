<?php

namespace Drupal\dept_autologin;

use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class AutologinManagerService logs in user if they supply a token in the url.
 */
class AutologinManagerService implements AutologinManagerServiceInterface {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new AutologinManagerService object.
   */
  public function __construct(Connection $database, EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user) {
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function loginUser($auth_token) {

    // Check if there is a valid token.
    if ($this->isValidToken($auth_token)) {
      // Find the user id by using the token.
      $uid = $this->findUserByToken($auth_token);
      if ($uid) {
        // Login user.
        /** @var Drupal\user\UserInterface $user */
        $user = $this->entityTypeManager->getStorage('user')->load($uid);
        $this->currentUser->setAccount($user);
        // The current user is set to the user found in the database. The
        // user_login_finalize function is used to set the cookies correctly.
        // It should be noted that this is not available in Drupal 9 from here
        // and an alternative method of authentication is needed. This is due
        // to changes which came with Symfony.
        user_login_finalize($user);
        return TRUE;
      }
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isValidToken($token) {
    return strlen($token) == 32;
  }

  /**
   * {@inheritdoc}
   */
  public function findUserByToken($token) {

    // Rather than load Entity Query, use the database object as its quicker and
    // lighter.
    $query = $this->database->select('user__field_auth_token', 'u');
    $query->condition('u.field_auth_token_value', $token, '=');
    $query->fields('u', ['entity_id']);
    $result = $query->execute()->fetchAssoc();

    return isset($result) ? $result['entity_id'] : FALSE;
  }

}
