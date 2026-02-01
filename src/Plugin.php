<?php

namespace Botble\EdnElection;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        // Drop tables on plugin removal
        Schema::dropIfExists('ee_results');
        Schema::dropIfExists('ee_candidates');
        Schema::dropIfExists('ee_parties');
        Schema::dropIfExists('ee_woredas');
        Schema::dropIfExists('ee_zones');
        Schema::dropIfExists('ee_regions');
        Schema::dropIfExists('ee_elections');
    }
}