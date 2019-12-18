<?php

namespace Craft\Security\User;

use Atompulse\Component\Domain\Data\DataContainer;


/**
 * Class UserData
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string id;
 * @property string email;
 * @property string role;
 */
trait UserDataTrait
{
    use DataContainer;

    public function __construct(array $data = null)
    {
        $this->defineProperty('id', ['string']);
        $this->defineProperty('email', ['string']);
        $this->defineProperty('role', ['string']);

        if ($data !== null) {
            $this->fromArray($data);
        }
    }

    public function getId(): string
    {
        return $this->properties['id'] ?? null;
    }

    public function getEmail(): string
    {
        return $this->properties['email'] ?? null;
    }

    public function getRole(): string
    {
        return $this->properties['role'] ?? null;
    }

}
