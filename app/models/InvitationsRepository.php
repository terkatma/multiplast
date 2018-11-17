<?php
/**
 * Created by PhpStorm.
 * User: terka
 * Date: 21.09.2018
 * Time: 15:37
 */

namespace DB;

use app\entities\Customer;
use Nette;
use Nette\Utils\DateTime;
use Phinx\Util\Util;

class InvitationsRepository extends Repository
{

    /* @var Customer $values */
    /*
    public function updateCustomerValues($values){
        return $this->findBy(['id' => $values->id])->update([
            'ticket_count' => $values->ticket_count,
            'note' => $values->note,
            'hash' => $values->hash,
            'name' => $values->name,
            'addressing' => $values->addressing,
            'company' => $values->company,
            'email' => $values->email,
            'invitation_count' => $values->invitation_count,
            'is_sent' => $values->is_sent,
            'is_answered' => $values->is_answered,
            'is_woman' => $values->is_woman,
            'language' => $values->language,
            'user_note' => $values->user_note,
            'reply_deadline' => DateTime::createFromFormat('d.m.Y', $values->reply_deadline),
        ]);
    }
    */

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
        $this->findBy(['id' => $id])->update(['reply_deadline' => DateTime::createFromFormat('d.m.Y', $date)]);
    }

    // update customer user note in database
    public function updateCustomerUserNote($id, $user_note)
    {
        $this->findBy(['id' => $id])->update(['user_note' => $user_note,]);
    }

    // update customer invitation sent log in database
    public function updateCustomerInvitationSentLog($id)
    {
        $this->findBy(['id' => $id])->update(['invitation_sent_log' => new \DateTime()]);
    }

    // update customer answer log in database
    public function updateCustomerAnswerLog($id)
    {
        $this->findBy(['id' => $id])->update(['answer_log' => new \DateTime()]);
    }

    // update customer reminder sent log in database
    public function updateCustomerReminderSentLog($id)
    {
        $this->findBy(['id' => $id])->update(['reminder_sent_log' => new \DateTime()]);
    }

    // update customer confirmation sent log in database
    public function updateCustomerConfirmationSentLog($id)
    {
        $this->findBy(['id' => $id])->update(['confirmation_sent_log' => new \DateTime()]);
    }

    // update customer confirmation sent log in database
    public function updateCustomerTicketSentLog($id)
    {
        $this->findBy(['id' => $id])->update(['ticket_sent_log' => new \DateTime()]);
    }

    // delete customer in database
    public function deleteCustomer($id){

        return $this->findBy(['id' => $id])->delete();
    }

    // add invitation to database
    public function createCustomer($values)
    {
        $values->hash = $this->generateHash();
        return $this->getTable()->insert($values);

    }

    public function generateHash(){
        $hash = Nette\Utils\Random::generate(6);
        while ($this->checkKeyDuplicity($hash)) {
            $hash = Nette\Utils\Random::generate(6);
        }
        return $hash;
    }

    //sum is_sent invitation_count
    public function sumOfSentInvitations()
    {
        $customers = $this->findAll();
        $tmp = 0;
        foreach ($customers as $customer) {
            if ($customer->is_sent)
                $tmp = $tmp + $customer->invitation_count;
        }
        return $tmp;
    }

    public function findDuplicity($name, $company, $email)
    {
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