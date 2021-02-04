<?php

namespace Drupal\dept_autologin\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique integer.
 *
 * @Constraint(
 *   id = "AuthTokenLength",
 *   label = @Translation("Auth token length", context = "Validation"),
 *   type = "string"
 * )
 */
class AuthTokenLength extends Constraint {

  /**
   * The message that will be shown if the value is not an integer.
   */
  public $incorrectLength = '%value must be 32 characters long';

}
