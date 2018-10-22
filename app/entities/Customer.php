<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.4.17
 * Time: 19:08
 */

namespace app\entities;


use Nette\Database\Table\ActiveRow;

class Customer extends ActiveRow
{
    /* @var int id*/
    public $id;

    /* @var string name*/
    public $name;

    /* @var string company*/
    public $company;

    /* @var string email*/
    public $email;

    /* @var int invitation_count*/
    public $invitation_count;

    /* @var int ticket_count*/
    public $ticket_count;

    /* @var string note*/
    public $note;

    /* @var string hash*/
    public $hash;

    /* @var bool is_woman*/
    public $is_woman;

    /* @var string language*/
    public $language;

    /* @var bool is_sent*/
    public $is_sent;

    /* @var bool is_answered*/
    public $is_answered;

    /* @var string addressing*/
    public $addressing;

    /* @var \DateTime reply_deadline*/
    public $reply_deadline;

    /* @var string user_note*/
    public $user_note;

    /* @var \DateTime invitation_sent_log*/
    public $invitation_sent_log;

    /* @var \DateTime answer_log*/
    public $answer_log;

    /* @var \DateTime reminder_sent_log*/
    public $reminder_sent_log;

    /* @var \DateTime confirmation_sent_log*/
    public $confirmation_sent_log;
}
