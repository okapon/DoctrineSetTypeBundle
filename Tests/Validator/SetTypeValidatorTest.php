<?php

namespace Okapon\DoctrineSetTypeBundle\Tests\Validator;

use Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType;
use Okapon\DoctrineSetTypeBundle\Validator\Constraints\SetType;
use Okapon\DoctrineSetTypeBundle\Validator\Constraints\SetTypeValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Phake;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * SetTypeValidatorTest
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 */
class SetTypeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SetTypeValidator $setTypeValidator SET validator
     */
    private $setTypeValidator;

    /**
     * @var ExecutionContext|\PHPUnit_Framework_MockObject_MockObject $context Context
     */
    private $context;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->setTypeValidator = new SetTypeValidator();

        $this->context = Phake::mock('Symfony\Component\Validator\ExecutionContext');
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function testThrowsExceptionInTargetOptionIsNone()
    {
        new SetType();
    }

    /**
     * Test that creation of SET Constraint without type class throws ConstraintDefinitionException
     *
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function testThrowsExceptionInNotDefinedTargetClass()
    {
        $constraint = new SetType([
            'target' => null,
        ]);

        $this->setTypeValidator->validate([UserGroupType::GROUP1], $constraint);
    }


    /**
     * Test valid parameters
     *
     * Maybe fix this test from Symfony3.0
     *
     * @dataProvider testValidParamProvider
     */
    public function testValidParam($param)
    {
        $constraint = new SetType([
            'target' => 'Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType'
        ]);

        $this->setTypeValidator->initialize($this->context);
        $this->setTypeValidator->validate($param, $constraint);

        Phake::verify($this->context, Phake::never())->addViolation(Phake::anyParameters());
    }

    /**
     * Data provider for method testValidParam
     */
    public function testValidParamProvider()
    {
        return [
            [
                null,
            ],
            [
                [],
            ],
            [
                [UserGroupType::GROUP1],
            ],
            [
                [UserGroupType::GROUP1, UserGroupType::GROUP2],
            ],
        ];

    }

    public function testInvalidTypeParam()
    {
        $constraint = new SetType([
            'target' => 'Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType'
        ]);

        $this->setTypeValidator->initialize($this->context);
        $this->setTypeValidator->validate(['InvalidValue'], $constraint);

        Phake::verify($this->context)->addViolation(Phake::anyParameters());
    }

    /**
     * Test invalid Parameter for Symfony >=3.0
     */
    public function testInvalidParameterTypeForSymfony3()
    {
        // ExcecutionContext for Symfony ~3.0 @Since 2.5
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');

        $constraint = new SetType([
            'target' => 'Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType'
        ]);

        $message = 'One or more of the given values is invalid.';

        $constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        Phake::when($constraintViolationBuilder)->setParameter('{{ value }}', '"InvalidValue"')->thenReturn($constraintViolationBuilder);
        Phake::when($constraintViolationBuilder)->setCode(Choice::NO_SUCH_CHOICE_ERROR)->thenReturn($constraintViolationBuilder);
        Phake::when($constraintViolationBuilder)->setInvalidValue('InvalidValue')->thenReturn($constraintViolationBuilder);

        Phake::when($this->context)->buildViolation($message, $parameters = [])->thenReturn($constraintViolationBuilder);

        $this->setTypeValidator->initialize($this->context);
        $this->setTypeValidator->validate(['InvalidValue'], $constraint);

        Phake::verify($this->context)->buildViolation($message, $parameters = []);
    }
}
