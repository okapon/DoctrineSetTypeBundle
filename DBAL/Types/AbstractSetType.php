<?php

namespace Okapon\DoctrineSetTypeBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * AbstractSetType
 *
 * Provides MySQL Set type for Doctrine in Symfony applications
 *
 * @author Yuichi Okada <yuuichi177@gmail.com>
 */
abstract class AbstractSetType extends Type
{
    /**
     * @var string $name Name of this type
     */
    protected $name = '';

    /**
     * @var array $choices Map of available SET type, key and label
     * @static
     */
    protected static $choices = [];

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_array($value) || count($value) <= 0) {
            return null;
        }

        $diff = array_diff($value, $this->getValues());
        if (count($diff) > 0) {
            throw new \InvalidArgumentException(sprintf(
                    'Invalid value "%s". It is not defined in "%s::$choices"',
                    implode(',', $diff),
                    get_class($this)
                )
            );
        }

        return implode(',', $value);
    }

    /**
     * @param string|null $value
     * @param AbstractPlatform $platform
     * @return array
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (strpos($value, ',') === false) {
            return [$value];
        }

        return explode(',', $value);
    }

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(', ', array_map(function ($value) {
                    return "'{$value}'";
                },
                $this->getValues()
            )
        );

        if (!$platform instanceof MySqlPlatform) {
            return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
        }

        return sprintf('SET(%s)', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name ?: (new \ReflectionClass(get_class($this)))->getShortName();
    }

    /**
     * Get choices Label for the Set form field type
     *
     * @return array Values for the SET field
     */
    public static function getChoices()
    {
        return static::$choices;
    }

    /**
     * Get values for the SET field
     *
     * @return array Values for the SET field
     */
    public static function getValues()
    {
        return array_keys(static::getChoices());
    }
}
