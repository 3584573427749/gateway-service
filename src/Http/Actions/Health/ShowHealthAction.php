<?php

namespace App\Http\Actions\Health;

use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class ShowHealthAction extends Action {
    /**
     * @inheritDoc
     */
    protected function action(): Response {
        return $this->respondWithData(['status' => 'healthy']);
    }
}