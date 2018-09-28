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
    // update customer ticket_count and note in database
    public function updateCustomer($id, $ticket_count, $note)
    {
        return $this->findBy(['id' => $id])->update([
            'ticket_count' => $ticket_count,
            'note' => $note,
        ]);
    }

    // update customer hash in database
    public function updateCustomersHash($id, $hash)
    {
        $this->findBy(['id' => $id])->update(['hash' => $hash,]);
    }

    public function getIdByHash($hash)
    {
        return $this->findBy(['hash' => $hash])->fetch();
    }

    //checks hash with database
    public function checkKeyDuplicity($hash)
    {
        $dup = $this->findOneBy(['hash' => $hash]);
        if (!isset($dup['hash'])) {
            return false;
        }
        return true;
    }
}