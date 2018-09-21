<?php

namespace App\Components;


use Nette;
use Nette\Application;
use Nette\Application\UI\Form;
use Nette\Forms\Controls;

/**
 * Class SignInComponent
 * @package App\Components
 */
class SignInComponent extends BaseComponent
{

    public function __construct()
    {
        parent::__construct();

    }

    protected function createComponentSignIn()
    {
        $form = new Form();

        $form->addText('key')
            ->setRequired("Zadejte heslo!")
            ->setAttribute("type","password");

        $form->addSubmit('send', "Přihlásit")
            ->setAttribute('class', 'btn');

        $form->onSuccess[] = [$this, "signInSubmitted"];
        return $form;
    }


    public function signInSubmitted(Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();

        $sesion = $this->presenter->session->getSection("key");
        $sesion['value'] = $values['key'];
        $this->presenter->redirect("default");

    }
}
