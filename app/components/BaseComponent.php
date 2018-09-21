<?php

namespace App\Components;

use App\Models;
use Nette;
use Utils\Helpers\DatabaseArray;
use VojtechDobes\NetteForms\GpsPoint;

/**
 * Class BaseComponent
 * @package App\Components
 */
abstract class BaseComponent extends \Nette\Application\UI\Control
{
    /**
     * @var bool
     */
    protected $autoSetupTemplateFile = TRUE;


    /**
     * BaseComponent constructor.
     * @param null $parent
     * @param null $name
     */
    public function __construct($parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
    }

    /**
     * default render
     */
    public function render()
    {
        $this->template->render();
    }


    /**
     * @param null $class
     * @return \Nette\Application\UI\ITemplate
     */
    protected function createTemplate($class = NULL)
    {
        $template = parent::createTemplate();

        if ($this->autoSetupTemplateFile) {
            $template->setFile($this->getTemplateFilePath());
        }

        return $template;
    }

    /**
     * @return string
     */
    protected function getTemplateFilePath()
    {
        $reflection = $this->getReflection();
        $dir = dirname($reflection->getFileName());
        $filename = $reflection->getShortName() . '.latte';
        return $dir . \DIRECTORY_SEPARATOR . $filename;
    }

}
