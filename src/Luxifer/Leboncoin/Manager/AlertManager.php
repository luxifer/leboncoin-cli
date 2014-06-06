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

    /**
     * Insert alert in database
     *
     * @param string $key    key alert name
     * @param array  $config alert configuration
     * @param string $url    alert url
     * @return integer inserter alert id
     */
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

    /**
     * Find alert in database based on config
     *
     * @param  array  $config alert configuration
     * @return array|boolean         alert|false
     */
    public function find(array $config)
    {
        return $this->conn->fetchAssoc('SELECT * FROM alert WHERE config_hash = ?', array(self::hash($config)));
    }

    /**
     * Find bids to send in database
     *
     * @param  integer $alertId alert id
     * @return array          alert list
     */
    public function fetchBidsToSend($alertId)
    {
        return $this->conn->fetchAll('SELECT b.* FROM bid b LEFT JOIN alert_bid ab ON b.id = ab.bid_id WHERE ab.alert_id = ? AND b.is_sent = 0', array($alertId));
    }

    /**
     * Check if an alert exist in the database
     *
     * @param  string $key  alertkey name
     * @param  string $hash alert config hash
     * @return array|boolean       alert|false
     */
    protected function fetch($key, $hash)
    {
        return $this->conn->fetchAssoc('SELECT * FROM alert WHERE key = ? AND config_hash = ?', array($key, $hash));
    }

    /**
     * Transform an alert config into a hash
     *
     * @param  array  $config alert configuration
     * @return string         alert config hash
     */
    protected static function hash(array $config)
    {
        return md5(json_encode($config));
    }
}
