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

    // update customer name in database
    public function updateCustomerName($id, $name)
    {
        $this->findBy(['id' => $id])->update(['name' => $name,]);
    }

    // update customer addressing in database
    public function updateCustomerAddressing($id, $addressing)
    {
        $this->findBy(['id' => $id])->update(['addressing' => $addressing,]);
    }

    // update customer company in database
    public function updateCustomerCompany($id, $company)
    {
        $this->findBy(['id' => $id])->update(['company' => $company,]);
    }

    // update customer email in database
    public function updateCustomerEmail($id, $email)
    {
        $this->findBy(['id' => $id])->update(['email' => $email,]);
    }

    // update customer ticket_count in database
    public function updateCustomerTicketCount($id, $ticket_count)
    {
        $this->findBy(['id' => $id])->update(['ticket_count' => $ticket_count,]);
    }

    // update customer invitation_count in database
    public function updateCustomerInvitationCount($id, $invitation_count)
    {
        $this->findBy(['id' => $id])->update(['invitation_count' => $invitation_count,]);
    }

    // update customer status is_sent in database
    public function updateCustomerIsSent($id, $value)
    {
        $this->findBy(['id' => $id])->update(['is_sent' => $value,]);
    }

    // update customer status is_answered in database
    public function updateCustomerIsAnswered($id, $value)
    {
        $this->findBy(['id' => $id])->update(['is_answered' => $value,]);
    }

    // update customer status is_woman in database
    public function updateCustomerIsWoman($id, $value)
    {
        $this->findBy(['id' => $id])->update(['is_woman' => $value,]);
    }

    // update customer language in database
    public function updateCustomerLanguage($id, $value)
    {
        $this->findBy(['id' => $id])->update(['language' => $value,]);
    }

    // update customer reply deadline in database
    public function updateCustomerReplyDeadline($id, $date)
    {
        $this->findBy(['id' => $id])->update(['reply_deadline' => $date,]);
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
    public function getLanguageById($id)
    {
        return $this->findBy(['id' => $id])->fetch();
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