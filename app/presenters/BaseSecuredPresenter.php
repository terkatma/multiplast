<?php


namespace App\Presenters;

use Nette;
use Tracy\Debugger;


abstract class BaseSecuredPresenter extends Nette\Application\UI\Presenter {

    private $key = "marpo";

    protected function startup() {
        parent::startup();
        $this->checkLoggedUser();
    }

    protected function checkLoggedUser() {
	    $session = $this->session->getSection("key");
        if (($this->key != $session['value']) && (($this->action != "sign"))) {
        	if (!($session['value'] === null || $session['value'] === "")) {
		        $this->flashMessage(_('Nesprávné heslo!'), "error");
	        }
            $this->redirect('sign');
        }
    }

    protected function createComponentSignOutForm()
    {
        $form = new Nette\Application\UI\Form();

        $form->addSubmit('send', "Odhlásit")
            ->setAttribute('class', 'btn');

        $form->onSuccess[] = [$this, "signOutFormSubmitted"];
        return $form;
    }


    public function signOutFormSubmitted()
    {

        $sesion = $this->session->getSection("key");
        $sesion['value'] ="";
        $this->redirect("Homepage:default");

    }
}
