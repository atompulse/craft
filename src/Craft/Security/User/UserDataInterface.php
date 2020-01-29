<?php

namespace Craft\Security\User;

use Craft\Data\Container\DataContainerInterface;

/**
 * Interface UserDataInterface
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface UserDataInterface extends DataContainerInterface
{
    public function getExpireDate(): string;

    public function getExpires(): bool;

    public function getId(): int;

    public function getEmail(): string;

    public function getRole(): string;
}
