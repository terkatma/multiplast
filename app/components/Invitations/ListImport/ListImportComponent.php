<?php

namespace App\Components;


use DB\InvitationsRepository;
use Nette;
use Nette\Application\UI\Form;
use Nette\Caching\Storages\FileStorage;
use ondrs\Hi\Hi;
use Tracy\Debugger;

/**
 * Class ListImportComponent
 * @package App\Components
 */
class ListImportComponent extends BaseComponent
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

    protected function createComponentListImport()
    {
        $form = new Form();

        $form->addUpload('csv_file', 'Seznam zákazníků:')
            ->addRule(Form::MIME_TYPE,"Soubor musí být formátu CSV",['text/plain','text/csv','text/tsv'])
            ->setRequired(true);

        $form->addSubmit('send', 'Nahrát seznam')
            ->getControlPrototype()
            ->setAttribute("class", 'btn');

        $form->onSuccess[] = [$this, "listImportSubmitted"];
        return $form;
    }


    public function listImportSubmitted(Nette\Application\UI\Form $form)
    {
        $companyIndex = 0;
        $nameIndex = 2;
        $emailIndex = 3;
        $isWomanIndex = 5;
        $replyDeadlineIndex = 6;
        $languageIndex = 7;
        $values = $form->getValues();

        $file = $values['csv_file'];
        $csv = $this->csv_to_array($file);

        $duplicityCount = 0;

        $hi = new Hi(new FileStorage(__CACHE_DIR__));
        $hi->setType(Hi::TYPE_SURNAME);

        Debugger::log('Nahrávání seznamu...; ID ', 'debug');
        foreach ($csv as $row){
            $row = array_values($row);
            $row[$nameIndex] = str_replace("Ing.", "", $row[$nameIndex]);
            $row[$nameIndex] = trim($row[$nameIndex]);
            $duplicity = $this->invitationsRepository->findDuplicity($row[$nameIndex], $row[$companyIndex], $row[$emailIndex]);
            if ($duplicity) {
                Debugger::log('Duplicitní řádek:  ' . $row[$nameIndex] . ' ; ' . $row[$companyIndex] . ' ; ' . $row[$emailIndex] . ' ; ID ', 'debug');
                $duplicityCount++;
            } else {
                $name = str_replace("ml.","",$row[$nameIndex]);
                $name = trim($name);
                $addressing = $hi->to($name);
                $hash = $this->invitationsRepository->generateHash();
                //TODO otestovat
                $reply_deadline = (new \DateTime($row[$replyDeadlineIndex]))->format('d.m.Y');
                $this->invitationsRepository->insert([
                    "name" => $row[$nameIndex],
                    "company" => $row[$companyIndex],
                    "email" => $row[$emailIndex],
                    "addressing" => $addressing!=null?$addressing->vocativ:$name,
                    //"is_woman" => $addressing!=null?!strcmp($addressing->gender,"female"):0,
                    "is_woman" => $row[$isWomanIndex],
                    "hash" => $hash,
                    "invitation_count" => 2,
                    "reply_deadline" => $reply_deadline,
                    "language" => $row[$languageIndex],
                ]);
            }
        }
        Debugger::log('Nahrávání seznamu DOKONČENO; ID ', 'debug');
        $this->presenter->flashMessage("Úspěšně nahráno " . (count($csv) - $duplicityCount) ." 
            řádků. $duplicityCount řádků již v databázi bylo.", "success");
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
