<?php

namespace Okapon\DoctrineSetTypeBundle\Form\Guess;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;
use Okapon\DoctrineSetTypeBundle\Exception\InvalidClassSpecifiedException;
use Symfony\Component\HttpKernel\Kernel;

/**
 * SetTypeGuesser
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 */
class SetTypeGuesser extends DoctrineOrmTypeGuesser
{
    /**
     * @var AbstractSetType[] $registeredTypes Array of registered types
     */
    protected $registeredTypes = [];

    /**
     * @var string parentSetTypeClass
     */
    protected $parentSetTypeClass;

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry Registry
     * @param array $registeredTypes Array of registered SET types
     * @param string $parentSetTypeClass
     */
    public function __construct(ManagerRegistry $registry, array $registeredTypes, $parentSetTypeClass)
    {
        parent::__construct($registry);

        $this->parentSetTypeClass = $parentSetTypeClass;

        foreach ($registeredTypes as $type => $details) {
            $this->registeredTypes[$type] = $details['class'];
        }
    }

    /**
     * @param string $class
     * @param string $property
     * @return TypeGuess|null
     */
    public function guessType($class, $property)
    {
        $classMetadata = $this->getMetadata($class);
        if (!$classMetadata) {
            return;
        }

        /**
         * @var \Doctrine\ORM\Mapping\ClassMetadata $metadata
         * @var string $name
         */
        list($metadata) = $classMetadata;
        $fieldType = $metadata->getTypeOfField($property);

        if (!isset($this->registeredTypes[$fieldType])) {
            return;
        }

        $fullClassName = $this->registeredTypes[$fieldType];

        if (!is_subclass_of($fullClassName, $this->parentSetTypeClass)) {
            return;
        }

        // render checkboxes
        $parameters = [
            'choices'  => array_flip($fullClassName::getChoices()),
            'expanded' => true,
            'multiple' => true,
            'required' => !$metadata->isNullable($property),
        ];

        if (Kernel::MAJOR_VERSION == 2) {
            $parameters['choices'] = $fullClassName::getChoices();
            return new TypeGuess('choice', $parameters, Guess::VERY_HIGH_CONFIDENCE);
        }

        return new TypeGuess(ChoiceType::class, $parameters, Guess::VERY_HIGH_CONFIDENCE);
    }
}
