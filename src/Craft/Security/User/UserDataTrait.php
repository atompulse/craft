<?php

namespace Craft\Security\User;

use Atompulse\Component\Domain\Data\DataContainer;


/**
 * Class UserData
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property int id;
 * @property string email;
 * @property string role;
 * @property bool expires;
 * @property string expireDate;
 */
trait UserDataTrait
{
    use DataContainer;

    public function __construct(array $data = null)
    {
        $this->defineProperty('expires', ['bool']);
        $this->defineProperty('expireDate', ['string', 'null']);
        $this->defineProperty('id', ['int']);
        $this->defineProperty('email', ['string']);
        $this->defineProperty('role', ['string']);

        if ($data !== null) {
            $this->fromArray($data);
        }
    }

    public function getExpires(): bool
    {
        return $this->properties['expires'] ?? null;
    }

    public function getExpireDate(): string
    {
        return $this->properties['expireDate'] ?? null;
    }

    public function getId(): int
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
