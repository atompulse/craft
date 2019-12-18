<?php

namespace Craft\Security\User;

use Atompulse\Component\Domain\Data\DataContainerInterface;

/**
 * Interface UserDataInterface
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface UserDataInterface extends DataContainerInterface
{
    public function getId(): string;

    public function getEmail(): string;

    public function getRole(): string;
}
