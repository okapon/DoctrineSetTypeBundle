<?php

namespace Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class InvalidType extends Type
{

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
    }

    public function getName()
    {
    }
}