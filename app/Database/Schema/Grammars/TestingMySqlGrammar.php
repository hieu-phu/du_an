<?php

namespace App\Database\Schema\Grammars;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;

class TestingMySqlGrammar extends MySqlGrammar
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    public function compileDropAllTables($tables)
    {
        return 'drop table if exists '.implode(', ', $this->escapeNames($tables));
    }

    public function compileDropAllViews($views)
    {
        return 'drop view if exists '.implode(', ', $this->escapeNames($views));
    }
}
