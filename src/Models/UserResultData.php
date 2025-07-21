<?php 
namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Models;

class UserResultData {
    public $id;
    public $name;
    public $result1;
    public $result2;
    public $dataSufficiency;

    public function __construct($_id, $_name, $_result1, $_result2, $_dataSufficiency) {
        $this->id = $_id;
        $this->name = $_name;
        $this->result1 = $_result1;
        $this->result2 = $_result2;
        $this->dataSufficiency = $_dataSufficiency;
    }

    // İstersen getter'lar da ekleyebilirim.
}
