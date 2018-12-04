<?php

namespace App\Components;


use DB\InvitationsRepository;
use Nette;
use Nette\Application\UI\Form;
use Nette\Caching\Storages\FileStorage;
use ondrs\Hi\Hi;
use Tracy\Debugger;

/**
 * Class ParticipantsListImportComponent
 * @package App\Components
 */
class ParticipantsListImportComponent extends BaseComponent
{
    /**
     * @var \DB\InvitationsRepository
     */
    protected $invitationsRepository;

    public function __construct(InvitationsRepository $invitationsRepository)
    {
        parent::__construct();
        $this->invitationsRepository = $invitationsRepository;
    }

    protected function createComponentParticipantsListImport()
    {
        $form = new Form();

        $form->addUpload('csv_file', 'Seznam zúčastněných zákazníků (načtené vstupenky):')
            ->addRule(Form::MIME_TYPE,"Soubor musí být formátu CSV",['text/plain','text/csv','text/tsv'])
            ->setRequired(true);

        $form->addSubmit('send', 'Nahrát seznam')
            ->getControlPrototype()
            ->setAttribute("class", 'btn');

        $form->onSuccess[] = [$this, "participantsListImportSubmitted"];
        return $form;
    }


    public function participantsListImportSubmitted(Nette\Application\UI\Form $form)
    {
        $hashIndex = 1;
        $values = $form->getValues();

        $file = $values['csv_file'];
        $csv = $this->csv_to_array($file);

        Debugger::log('Nahrávání seznamu zúčastněných...; ID ', 'debug');
        foreach ($csv as $row){
            $row = array_values($row);
            $customer = $this->invitationsRepository->getIdByHash($row[$hashIndex]);
            $id = $customer->id;
            $this->invitationsRepository->update($id, ['participated' => 1]);
        }

        Debugger::log('Nahrávání seznamu DOKONČENO; ID ', 'debug');
        $this->presenter->flashMessage("Úspěšně nahráno.", "success");
        $this->presenter->redirect("this");
    }

    function csv_to_array($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = [];
        $fc = $this->utf8FopenRead($filename);
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($fc, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                    foreach ($header as &$value) {
                        $value = trim($value);
                    }
                } else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * @param $fileName
     * @return resource
     */
    public function utf8FopenRead($fileName)
    {
        $fc = iconv('UTF-8', 'utf-8//IGNORE', file_get_contents($fileName));
        $handle = fopen("php://memory", "rw");
        fwrite($handle, $fc);
        fseek($handle, 0);
        return $handle;
    }
}
