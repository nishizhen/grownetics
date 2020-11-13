<?php

namespace App\Lib;

use App\Lib\Integrations\InfisenseApi;

class IntegrationsManager
{

  # Poll all our integrations that we need to pull data from.
  public function poll()
  {
    $infisenseApi = new InfisenseApi();
    $infisenseApi->poll();
  }
}
