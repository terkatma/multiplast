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

    // update customer ticket_count and note in database
    public function updateCustomer($id, $ticket_count, $note)
    {
        return $this->findBy(['id' => $id])->update([
            'ticket_count' => $ticket_count,
            'note' => $note,
        ]);
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

    public function findHash($hash)
    {
        return $this
            ->findBy(["hash" => $hash])
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