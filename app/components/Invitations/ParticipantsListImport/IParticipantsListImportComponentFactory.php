<?php

namespace App\Components;

/**
 * Interface IParticipantsListImportComponentFactory
 * @package App\Components
 */
interface IParticipantsListImportComponentFactory
{

    /** @return ParticipantsListImportComponent */
    function create();
}