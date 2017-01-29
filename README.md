DoctrineSetTypeBundle
=====================

The `DoctrineSetTypeBundle` provides support MySQL SET type for Doctrine2 in your Symfony2 or Symfony3 application.

[![Latest Stable Version](https://poser.pugx.org/okapon/doctrine-set-type-bundle/v/stable.svg)](https://packagist.org/packages/okapon/doctrine-set-type-bundle)
[![Total Downloads](https://poser.pugx.org/okapon/doctrine-set-type-bundle/downloads)](https://packagist.org/packages/okapon/doctrine-set-type-bundle)
[![Build Status](https://travis-ci.org/okapon/DoctrineSetTypeBundle.svg?branch=master)](https://travis-ci.org/okapon/DoctrineSetTypeBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/okapon/DoctrineSetTypeBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/okapon/DoctrineSetTypeBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/okapon/DoctrineSetTypeBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/okapon/DoctrineSetTypeBundle/?branch=master)

[![License](https://poser.pugx.org/okapon/doctrine-set-type-bundle/license.svg)](https://packagist.org/packages/okapon/doctrine-set-type-bundle)

## Features

* SET type mapping for mysql
* SET type validation
* Doctrine migrations

## Requirements

* PHP ~5.5
* Symfony ~2.6 or ~3.0
* Doctrine ~2.3

## Supported platforms

* MySQL

## Installation

### Step 1: Download the Bundle

Using composer

```
$ composer require okapon/doctrine-set-type-bundle "0.5.0"
```

## Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Okapon\DoctrineSetTypeBundle\OkaponDoctrineSetTypeBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Enable the mapping_type

In order to use MySQL SET type, Add the following line in the `app/config/confing.yml`


```yml
doctrine:
    dbal:
        mapping_types:
            set: string
```

## Usage

### Create your SET type class

**Sample: UserGroupType class**

This class is Sample that user has multiple groups which is mysql set type.

Then, create UserGroupType and extend AbstractSetType.

```php
<?php

namespace AppBundle\DBAL\Types;

use Okapon\DoctrineSetTypeBundle\DBAL\Types\AbstractSetType;

class UserGroupType extends AbstractSetType
{
    const GROUP1 = 'group1';
    const GROUP2 = 'group2';
    const GROUP3 = 'group3';

    /**
     * {@inheritdoc}
     */
     protected $name = 'UserGroupType'; // This is Optional. Automatically registered shord class name.

    /**
     * define your SET type.
     */
    protected static $choices = [
        self::GROUP1 => 'Group 1',
        self::GROUP2 => 'Group 2',
        self::GROUP3 => 'Group 3',
    ];
}
```

Or you may define set type definition in entity by overrideing `AbstractSetType::getChoices()` method. 

```php
class UserGroupType extends AbstractSetType
{
    public static function getChoices()
    {
        return User::getGroupChoices();
    }
}

class User
{
    public static function getGroupChoices()
    {
        return [
            self::GROUP1 => 'Group 1',
            self::GROUP2 => 'Group 2',
            self::GROUP3 => 'Group 3',
        ];
    }
}
```

### Register your type

Register UserGroupType in `config.yml`

```yml
doctrine:
    dbal:
        ## ...
        types:
            UserGroupType: AppBundle\DBAL\Types\UserGroupType
```

###  Add mapping data to entity

This is annotaion sample.

```php
<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Okapon\DoctrineSetTypeBundle\Validator\Constraints as DoctrineAssert;
use AppBundle\DBAL\Types\UserGroupType;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;

    /**
     * @var array
     *
     * @DoctrineAssert\SetType(class="AppBundle\DBAL\Types\UserGroupType")
     * @ORM\Column(name="groups", type="UserGroupType", nullable=true) // mapping_type
     */
    private $groups;

    // ...

    /**
     * Set groups
     *
     * @param array $groups
     * @return User
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
```

You can set Groups with array to User entity

```php
$user->setGroups([UserGroupType::GROUP1, UserGroupType::GROUP2]);
```

And also You can validate your type by adding the following annotation.

```php
    /**
     * @DoctrineAssert\SetType(class="AppBundle\DBAL\Types\UserGroupType")
     */
    private $groups;
```

### Building the form

Pass `null` to the Second argument.

[SetTypeGuesser](https://github.com/okapon/DoctrineSetTypeBundle/blob/master/Form/Guess/SetTypeGuesser.php) extends ChoiseType and render the field as checkboxes.

So, you can use choice field type option. (see [choice Field Type](http://symfony.com/doc/current/reference/forms/types/choice.html))


```php
$builder->add('groups', null, [
    'required' => true,
    'invalid_message' => 'Given values are invalid!!'
]);
```


### Doctrine migrations

Following SQL is executed.

```sql
CREATE TABLE user (
    id INT AUTO_INCREMENT NOT NULL,
    username varchar(50) COLLATE utf8_unicode_ci NOT NULL,
    groups set('group1','group2') DEFAULT NULL COMMENT '(DC2Type:UserGroupType)',
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
```
