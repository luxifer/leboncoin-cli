<?php
namespace Luxifer\Leboncoin\Manager;

use Doctrine\DBAL\Connection;

class AlertManager
{
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function add($key, array $config, $url)
    {
        $hash = self::hash($config);
        $alert = $this->fetch($key, $hash);

        if (!$alert) {
            $this->conn->insert('alert', array(
                'key'         => $key,
                'config_hash' => $hash,
                'title'       => $config['title'],
                'url'         => $url
            ));

            return $this->conn->lastInsertId();
        }

        return $alert['id'];
    }

    public function find(array $config)
    {
        return $this->conn->fetchAssoc('SELECT * FROM alert WHERE config_hash = ?', array(self::hash($config)));
    }

    public function fetchBidsToSend($alertId)
    {
        return $this->conn->fetchAll('SELECT b.* FROM bid b LEFT JOIN alert_bid ab ON b.id = ab.bid_id WHERE ab.alert_id = ? AND b.is_sent = 0', array($alertId));
    }

    protected function fetch($key, $hash)
    {
        return $this->conn->fetchAssoc('SELECT * FROM alert WHERE key = ? AND config_hash = ?', array($key, $hash));
    }

    protected static function hash(array $config)
    {
        return md5(json_encode($config));
    }
}
