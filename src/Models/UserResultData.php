<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Models;

class UserResultData
{
    public $id;
    public $name;
    public $result1;
    public $result2;
    public $result3;
    public $result4;
    public $dataSufficiency;

    public function __construct($_id, $_name, $_result1, $_result2, $_result3, $_dataSufficiency)
    {
        $this->id = $_id;
        $this->name = $_name;
        $this->result1 = $_result1;
        $this->result2 = $_result2;
        $this->result3 = $_result3;
        $this->dataSufficiency = $_dataSufficiency;
    }
}
