<?php

namespace Okapon\DoctrineSetTypeBundle\Tests\Fixtures\DBAL\Types;

use Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;

class UserGroupType extends AbstractSetType
{
    const GROUP1 = 'group1';
    const GROUP2 = 'group2';
    const GROUP3 = 'group3';

    /**
     * {@inheritdoc}
     */
     protected $name = 'UserGroupType';

    /**
     * Get map of available SET type, key and label
     *
     * @return array Values for the SET field
     */
    public static function getChoices()
    {
        return [
            self::GROUP1 => 'Group 1',
            self::GROUP2 => 'Group 2',
            self::GROUP3 => 'Group 3',
        ];
    }
}
