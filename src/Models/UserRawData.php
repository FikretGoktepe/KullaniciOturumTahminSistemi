<?php

namespace Fikretgoktepe\KullaniciOturumTahminSistemi\Models;

class UserRawData
{
    private $id;
    private $name;
    private $logins;

    public function __construct($id, $name, $logins)
    {
        $this->id = $id;
        $this->name = $name;
        $this->logins = $logins;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetName()
    {
        return $this->name;
    }

    public function GetLogins()
    {
        return $this->logins;
    }
}
