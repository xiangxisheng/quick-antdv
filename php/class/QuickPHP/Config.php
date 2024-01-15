<?php

namespace QuickPHP;

class Config
{
    private $c_dbs;
    public function __construct($mConf)
    {
        $this->c_dbs = $mConf['dbs'];
    }
    public function db($dbName)
    {
        $mDbconf = $this->c_dbs[$dbName];
        return new TableCrud($mDbconf);
    }
}
