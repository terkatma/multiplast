<?php
/**
 * Created by PhpStorm.
 * User: terka
 * Date: 21.09.2018
 * Time: 15:37
 */

namespace DB;


class InvitationsRepository extends Repository
{
    // update customer ticket_count in database
    public function updateCustomer($id, $ticket_count, $note)
    {
        return $this->findBy(['id' => $id])->update([
            'ticket_count' => $ticket_count,
            'note' => $note,
        ]);
    }

    public function findDuplicity($name, $company, $email) {
        return $this
            ->findBy(["name" => $name, "company" => $company, "email" => $email])
            ->fetch();
    }
    public function getIdByHash($hash)
    {
        return $this->findBy(['hash' => $hash])->fetch();
    }
}