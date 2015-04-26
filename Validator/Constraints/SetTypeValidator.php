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
        if (!$constraint->class) {
            throw new ConstraintDefinitionException('Target is not specified');
        }

        /** @var string $class class name of inheriting \Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType */
        $class = $constraint->class;
        if (!class_exists($class)) {
            throw new TargetClassNotExistException('Target class not exist.');
        }

        $constraint->choices = $class::getValues();
        $constraint->multiple =true;

        parent::validate($value, $constraint);
    }
}