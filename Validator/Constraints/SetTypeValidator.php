<?php

namespace Okapon\DoctrineSetTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * SetTypeValidator
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 */
class SetTypeValidator extends ChoiceValidator
{
    /**
     * Checks if the passed value is valid
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @throws ConstraintDefinitionException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var SetType $constraint */
        if (!$constraint->target) {
            throw new ConstraintDefinitionException('Target is not specified');
        }

        /** @var \Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType $target */
        $target = $constraint->target;
        $constraint->choices = $target::getValues();
        $constraint->multiple =true;

        return parent::validate($value, $constraint);
    }
}
