<?php

namespace Okapon\DoctrineSetTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * SET type constraint
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 *
 * @Annotation
 */
class SetType extends Choice
{
    /**
     * @var string $target Entity
     */
    public $target;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['target'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'choices';
    }
}
