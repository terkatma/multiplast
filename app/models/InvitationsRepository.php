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
    public function updateCustomer($id, $ticket_count)
    {
        return $this->findBy(['id' => $id])->update([
            'ticket_count' => $ticket_count,
        ]);
    }
}