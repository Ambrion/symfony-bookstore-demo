<?php

declare(strict_types=1);

namespace App\Contact\Factory;

use App\Contact\DTO\ContactDTO;
use App\Contact\Entity\Contact;

class ContactFactory implements ContactFactoryInterface
{
    public function create(ContactDTO $contactRequestDTO): Contact
    {
        $contact = new Contact();

        $contact->setEmail($contactRequestDTO->email);
        $contact->setName($contactRequestDTO->name);
        $contact->setPhone($contactRequestDTO->phone);
        $contact->setMessage($contactRequestDTO->message);

        return $contact;
    }
}
