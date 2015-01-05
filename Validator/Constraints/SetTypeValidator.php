<?php

namespace Okapon\DoctrineSetTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

use Okapon\DoctrineSetTypeBundle\Exception\TargetClassNotExistException;

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

        /** @var string $target class name of inheriting \Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType */
        $target = $constraint->target;
        if (!class_exists($target)) {
            throw new TargetClassNotExistException('Target class not exist.');
        }

        $constraint->choices = $target::getValues();
        $constraint->multiple =true;

        return parent::validate($value, $constraint);
    }
}