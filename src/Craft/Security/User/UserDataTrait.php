<?php

namespace Craft\Security\User;

use Craft\Data\Container\DataContainerTrait;

/**
 * Class UserData
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
    use DataContainerTrait;

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
        return $this->getPropertyValue('expires') ?? null;
    }

    public function getExpireDate(): string
    {
        return $this->getPropertyValue('expireDate') ?? null;
    }

    public function getId(): int
    {
        return $this->getPropertyValue('id') ?? null;
    }

    public function getEmail(): string
    {
        return $this->getPropertyValue('email') ?? null;
    }

    public function getRole(): string
    {
        return $this->getPropertyValue('role') ?? null;
    }

}
