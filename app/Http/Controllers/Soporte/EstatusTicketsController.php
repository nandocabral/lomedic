<?php
namespace App\Http\Controllers\Soporte;

use App\Http\Controllers\ControllerBase;
use App\Http\Models\Soporte\EstatusTickets;

class EstatusTicketsController extends ControllerBase
{

    public function __construct(EstatusTickets $entity)
    {
        $this->entity = $entity;
    }
}
