<?php
namespace Luxifer\Leboncoin\Manager;

use Doctrine\DBAL\Connection;

class BidManager
{
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }
}
