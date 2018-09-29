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

    // update customer status is_sent in database
    public function updateCustomerIsSent($id, $tmp)
    {
        $this->findBy(['id' => $id])->update(['is_sent' => $tmp,]);
    }

    // update customer status is_answered in database
    public function updateCustomerIsAnswered($id, $tmp)
    {
        $this->findBy(['id' => $id])->update(['is_answered' => $tmp,]);
    }

    //sum is_sent invitation_count
    public function sumOfSentInvitations()
    {
        $customers = $this->findAll();
        $tmp = 0;
        foreach ($customers as $customer) {
        if($customer->is_sent)
        $tmp = $tmp + $customer->invitation_count;
        }
        return $tmp;
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