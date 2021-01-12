<?php

namespace Knp\Invoice\Model;

class Seller extends Client
{
    protected $logotype;
    protected $account_number;

    public function getAccountNumber()
    {
        return $this->account_number;
    }

    public function getLogo()
    {
        return $this->logotype;
    }

    public function setAccountNumber($accountNumber)
    {
        $this->account_number = $accountNumber;
    }

    public function setLogo($logotype)
    {
        $this->logotype = $logotype;
    }
}
