<?php

namespace Drupal\dept_autologin\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the AuthTokenLength constraint.
 */
class AuthTokenLengthValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      $string = $item->value;
      if (strlen($string) < 32) {
        // String is less than 32 characters long. Add validation error.
        $this->context->addViolation($constraint->incorrectLength, ['%value' => $item->value]);
      }
    }
  }

}
