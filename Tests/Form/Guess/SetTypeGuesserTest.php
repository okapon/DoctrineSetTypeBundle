<?php

namespace Okapon\DoctrineSetTypeBundle\Tests\DBAL\Types;

use Okapon\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser;
use Phake;

/**
 * SetTypeGueserTest
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 */
class SetTypeGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $managerRegistory = Phake::mock('Doctrine\Common\Persistence\ManagerRegistry');
        $registeredTypes = ['UserGroupType' => ['class' => 'Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\UserGroupType']];
        /*
         * @var Okapon\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser
         */
        $this->guesser = Phake::partialMock(
            'Okapon\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser',
            $managerRegistory,
            $registeredTypes,
            'Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType'
        );
    }

    public function testNotGuessType()
    {
        $class = 'Okapon\SomeEntity';
        $property = 'groups';

        Phake::when($this->guesser)->getMetadata($class)->thenReturn(null);
        $this->assertNull($this->guesser->guessType($class, $property));
    }

    public function testNotRegisteredType()
    {
        $class = 'Okapon\SomeEntity';
        $property = 'string_field';

        $classMetadata = Phake::mock('Doctrine\ORM\Mapping\ClassMetadata');
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('string');

        Phake::when($this->guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertNull($this->guesser->guessType($class, $property));
    }

    public function testThrowsException()
    {
        $managerRegistory = Phake::mock('Doctrine\Common\Persistence\ManagerRegistry');
        $registeredTypes = ['InvalidType' => ['class' => 'Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types\InvalidType']];

        $guesser = Phake::partialMock(
            'Okapon\DoctrineSetTypeBundle\Form\Guess\SetTypeGuesser',
            $managerRegistory,
            $registeredTypes,
            'Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType'
        );

        $class = 'Okapon\SomeEntity';
        $property = 'groups';

        $classMetadata = Phake::mock('Doctrine\ORM\Mapping\ClassMetadata');
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('InvalidType');

        Phake::when($guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertNull($guesser->guessType($class, $property));
    }

    public function testGessingSetType()
    {
        $class = 'Okapon\SomeEntity';
        $property = 'groups';

        $classMetadata = Phake::mock('Doctrine\ORM\Mapping\ClassMetadata');
        Phake::when($classMetadata)->getTypeOfField($property)->thenReturn('UserGroupType');

        Phake::when($this->guesser)->getMetadata($class)->thenReturn([$classMetadata, 'default']);
        $this->assertInstanceOf('Symfony\Component\Form\Guess\TypeGuess', $this->guesser->guessType($class, $property));
    }
}
