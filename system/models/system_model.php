<?php

class System_Model extends MVC_Model
{
    /**
     * @coverage Dashboard
     * @desc Get recent sent/received
     */

    public function getRecentSent($id, int $limit)
    {
        $query = <<<SQL
SELECT s.id AS id, IF(c.name IS NULL, "Unknown", c.name) AS name, s.phone AS phone, IF(d.name IS NULL, 'Removed', d.name) AS device, CONCAT(DATE_FORMAT(s.create_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(s.create_date, '%l:%i %p')) AS create_date,
    CONCAT(
        '<a href=\"#\" data-message=\"', s.message, '\">',
            LENGTH(s.message),  ' bytes',
        '</a>'
      ) AS message
FROM sent s
LEFT JOIN contacts c ON s.phone = c.phone AND s.uid = c.uid
LEFT JOIN devices d ON s.did = d.did AND s.uid = d.uid
WHERE s.uid = ?
ORDER BY s.create_date DESC
LIMIT {$limit}
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getRecentReceived($id, int $limit)
    {
        $query = <<<SQL
SELECT r.id AS id, IF(c.name IS NULL, "Unknown", c.name) AS name, r.phone AS phone, IF(d.name IS NULL, 'Removed', d.name) AS device, CONCAT(DATE_FORMAT(r.receive_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(r.receive_date, '%l:%i %p')) AS receive_date,
    CONCAT(
        '<a href=\"#\" data-message=\"', r.message, '\">',
            LENGTH(r.message),  ' bytes',
        '</a>'
      ) AS message
FROM received r
LEFT JOIN contacts c ON r.phone = c.phone AND r.uid = c.uid
LEFT JOIN devices d ON r.did = d.did AND r.uid = d.uid
WHERE r.uid = ?
ORDER BY r.receive_date DESC
LIMIT {$limit}
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    /**
     * @coverage System
     * @desc Get package/subscription details
     */

    public function getMessageSent($uid, $id)
    {
        return $this->db->query_one("SELECT id, did FROM sent WHERE id = ? AND uid = ?", [
            $id,
            $uid
        ]);
    }

    public function getTotalSent($id)
    {
        $this->db->query("SELECT id FROM sent WHERE uid = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getTotalReceived($id)
    {
        $this->db->query("SELECT id FROM received WHERE uid = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getMessageQuota($id)
    {
        return $this->db->query_one("SELECT FORMAT(sent, 0) AS sent, FORMAT(received, 0) AS received FROM quota WHERE uid = ?", [
            $id
        ]);
    }

    public function getTotalContacts($id)
    {
        $this->db->query("SELECT id FROM contacts WHERE uid = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getTotalDevices($id)
    {
        $this->db->query("SELECT id FROM devices WHERE uid = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getTotalKeys($id)
    {
        $this->db->query("SELECT id FROM `keys` WHERE uid = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getTotalWebhooks($id)
    {
        $this->db->query("SELECT id FROM webhooks WHERE uid = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getSubscriptionByUserId($uid)
    {
        $query = <<<SQL
SELECT s.id AS id, FORMAT(p.send_limit, 0) AS send, FORMAT(p.receive_limit, 0) AS receive, FORMAT(p.contact_limit, 0) AS contact, FORMAT(p.device_limit, 0) AS device, FORMAT(p.key_limit, 0) AS `key`, FORMAT(p.webhook_limit, 0) AS webhook, p.name AS name, FORMAT(p.price, 0) AS price, DATE_FORMAT(DATE_ADD(DATE(s.date), INTERVAL 1 MONTH), '%M %e, %Y') AS expire_date 
FROM subscriptions s
LEFT JOIN packages p ON s.pid = p.id
WHERE s.uid = ?
SQL;

        return $this->db->query_one($query, [
            $uid
        ]);
    }

    public function getPackageDefault()
    {
        $query = <<<SQL
SELECT id, FORMAT(send_limit, 0) AS send, FORMAT(receive_limit, 0) AS receive, FORMAT(contact_limit, 0) AS contact, FORMAT(device_limit, 0) AS device, FORMAT(key_limit, 0) AS `key`, FORMAT(webhook_limit, 0) AS webhook, name, FORMAT(price, 0) AS price, "Indefinite" AS expire_date
FROM packages
WHERE id = 1
SQL;

        return $this->db->query_one($query);
    }

    /**
     * @coverage System
     * @desc Language translations
     */

    public function getTranslations($id)
    {
        return $this->db->query_one("SELECT translations FROM languages WHERE id = ?", [
            $id
        ])["translations"];
    }

    /**
     * @coverage System
     * @desc Check functions
     */

    public function checkUser($id)
    {
        $this->db->query("SELECT id FROM users WHERE id = ?", [
            $id
        ]);
        
        return $this->db->num_rows();
    }

    public function checkEmail($email)
    {
        $this->db->query("SELECT id FROM users WHERE email = ?", [
            $email
        ]);

        return $this->db->num_rows();
    }

    public function checkVoucher($code)
    {
        $this->db->query("SELECT id FROM vouchers WHERE code = ?", [
            $code
        ]);

        return $this->db->num_rows();
    }

    public function checkRole($id)
    {
        $this->db->query("SELECT id FROM roles WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function checkLanguage($id)
    {
        $this->db->query("SELECT id FROM languages WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function checkDevice($id)
    {
        $this->db->query("SELECT id FROM devices WHERE id = ?", [
            $id
        ]);
        
        return $this->db->num_rows();
    }

    public function checkDeviceByUnique($did)
    {
        $this->db->query("SELECT id FROM devices WHERE did = ?", [
            $did
        ]);
        
        return $this->db->num_rows();
    }

    public function checkNumber($uid, $number)
    {
        $this->db->query("SELECT id FROM contacts WHERE uid = ? AND phone = ?", [
            $uid,
            $number
        ]);
        
        return $this->db->num_rows();
    }

    public function checkGroup($id)
    {
        $this->db->query("SELECT id FROM groups WHERE id = ?", [
            $id
        ]);
        
        return $this->db->num_rows();
    }

    public function checkPackage($id)
    {
        $this->db->query("SELECT id FROM packages WHERE id = ?", [
            $id
        ]);
        
        return $this->db->num_rows();
    }

    public function checkSubscriptionByUserID($uid)
    {
        $this->db->query("SELECT id FROM subscriptions WHERE uid = ?", [
            $uid
        ]);
        
        return $this->db->num_rows();
    }

    /**
     * @coverage Subscription
     * @desc Count functions
     */

    public function countQuota($uid)
    {
        return $this->db->query_one("SELECT sent, received FROM quota WHERE uid = ?", [
            $uid
        ]);
    }

    public function countContacts($uid)
    {
        $this->db->query("SELECT id FROM contacts WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countDevices($uid)
    {
        $this->db->query("SELECT id FROM devices WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countKeys($uid)
    {
        $this->db->query("SELECT id FROM `keys` WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countWebhooks($uid)
    {
        $this->db->query("SELECT id FROM webhooks WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    /**
     * @coverage System
     * @desc Get functions
     */

     public function getPage($id)
    {
        return $this->db->query_one("SELECT id, roles, slug, logged, name, content FROM pages WHERE id = ?", [
            $id
        ]);
    }

    public function getVoucher($code)
    {
        return $this->db->query_one("SELECT id, package, code, name FROM vouchers WHERE code = ?", [
            $code
        ]);
    }

    public function getPackage($id)
    {
        return $this->db->query_one("SELECT id, send_limit, receive_limit, device_limit, key_limit, webhook_limit, name, price FROM packages WHERE id = ?", [
            $id
        ]);
    }

    public function getSubscription($id)
    {
        return $this->db->query_one("SELECT id, uid, pid FROM subscriptions WHERE id = ?", [
            $id
        ]);
    }

    public function getUser($id)
    {
        $query = <<<SQL
SELECT u.id AS id, IF(u.id < 2, 1, 0) AS admin, MD5(u.id) AS hash, u.email AS email, r.permissions AS permissions, u.name AS name, u.language AS language, u.suspended AS suspended
FROM users u
LEFT JOIN roles r ON u.role = r.id
WHERE u.id = ?
SQL;

        return $this->db->query_one($query, [
            $id
        ]);
    }

    public function getPassword($email)
    {
        return $this->db->query_one("SELECT id, password, suspended FROM users WHERE email = ?", [
            $email
        ]);
    }

    public function getPackageByUserID($uid)
    {
        $query = <<<SQL
SELECT p.id AS id, p.send_limit AS send, p.receive_limit AS receive, p.contact_limit AS contact, p.device_limit AS device, p.key_limit AS `key`, p.webhook_limit AS webhook
FROM subscriptions s
LEFT JOIN packages p ON s.pid = p.id
WHERE s.uid = ?
SQL;

        return $this->db->query_one($query, [
            $uid
        ]);
    }

    public function getDefaultPackage()
    {
        return $this->db->query_one("SELECT id AS id, send_limit AS send, receive_limit AS receive, contact_limit AS contact, device_limit AS device, key_limit AS `key`, webhook_limit AS webhook FROM packages WHERE id < 2");
    }

    public function getLanguages()
    {
       $query = <<<SQL
SELECT id, LOWER(iso) as iso, name FROM languages
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getUsers()
    {
        $query = <<<SQL
SELECT id, IF(id < 2, 1, 0) AS admin, MD5(id) AS hash, email, name, language
FROM users
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getContacts($id)
    {
        $query = <<<SQL
SELECT c.id AS id, c.phone AS phone, c.name AS name
FROM contacts c
WHERE c.uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getContactsByGroup($id, $gid)
    {
        $query = <<<SQL
SELECT c.id AS id, c.phone AS phone, c.name AS name, g.name AS `group`
FROM contacts c
LEFT JOIN `groups` g ON c.gid = g.id
WHERE c.uid = ? AND c.gid = ?
SQL;

        $this->db->query($query, [
            $id,
            $gid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getDevices($id)
    {
        $query = <<<SQL
SELECT id, uid, did, name, version, manufacturer, create_date
FROM devices
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getDevicesUnique($id)
    {
        $query = <<<SQL
SELECT id, did
FROM devices
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = $row["id"];

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getBlocks()
    {
        $query = <<<SQL
SELECT MD5(id) AS hash, content
FROM widgets WHERE type = 1
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["hash"]] = $row["content"];

            return $rows; 
        else:
            return [
                "none" => "none"
            ];
        endif;
    }

    public function getSystemSettings()
    {
        $query = <<<SQL
SELECT name, value FROM settings
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["name"]] = $row["value"];

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getPackages()
    {
        $query = <<<SQL
SELECT id, send_limit AS send, receive_limit AS receive, device_limit AS devices, key_limit AS `keys`, webhook_limit AS webhooks, name, price
FROM packages
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getDefaultPackages()
    {
        $query = <<<SQL
SELECT id, FORMAT(send_limit, 0) AS send, FORMAT(receive_limit, 0) AS receive, FORMAT(contact_limit, 0) AS contacts, FORMAT(device_limit, 0) AS devices, FORMAT(key_limit, 0) AS `keys`, FORMAT(webhook_limit, 0) AS webhooks, name, price
FROM packages
LIMIT 3
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getPages()
    {
        $query = <<<SQL
SELECT id, slug, roles, name, content FROM pages
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }


    /**
     * @coverage History
     * @desc History checking functions
     */

    public function checkHistorySent($params)
    {
        $phone = ($params["phone"] ? " AND phone = \"{$params["phone"]}\"" : false);
        $sim = ($params["sim"] ? " AND sim = {$params["sim"]}" : false);
        $priority = ($params["priority"] ? " AND priority = {$params["priority"]}" : false);
        $api = ($params["api"] ? " AND api = {$params["api"]}" : false);
        $device = ($params["device"] ? " AND did = \"{$params["device"]}\"" : false);

        $query = <<<SQL
SELECT id FROM sent 
WHERE uid = ? AND DATE(create_date) >= ? AND DATE(create_date) <= ? {$phone} {$sim} {$priority} {$api} {$device}
SQL;

        $this->db->query($query, [
            $params["uid"],
            $params["start"],
            $params["end"]
        ]);

        return $this->db->num_rows();
    }

    public function checkHistoryReceived($params)
    {
        $phone = ($params["phone"] ? " AND phone = \"{$params["phone"]}\"" : false);
        $device = ($params["device"] ? " AND did = \"{$params["device"]}\"" : false);

        $query = <<<SQL
SELECT id FROM received 
WHERE uid = ? AND DATE(receive_date) >= ? AND DATE(receive_date) <= ? {$phone} {$device}
SQL;

        $this->db->query($query, [
            $params["uid"],
            $params["start"],
            $params["end"]
        ]);

        return $this->db->num_rows();
    }

    public function checkQuota($uid)
    {
        $this->db->query("SELECT id FROM quota WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    /**
     * @coverage Charts
     * @desc Get chart stats
     */

    public function getStatsSent($id)
    {
        $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM sent
WHERE DATE(create_date) > (NOW() - INTERVAL 30 DAY) AND uid = ?
ORDER BY create_date DESC
SQL;
        $this->db->query($query, [
            $id
        ]);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getStatsReceived($id)
    {
        $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(receive_date)) AS receive_date
FROM received
WHERE DATE(receive_date) > (NOW() - INTERVAL 30 DAY) AND uid = ?
ORDER BY receive_date DESC
SQL;
        $this->db->query($query, [
            $id
        ]);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["receive_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getSystemTransactions()
    {
        $query = <<<SQL
SELECT t.id AS id, IF(p.name IS NULL, 'Removed', p.name) AS package_name, t.price AS total, UNIX_TIMESTAMP(DATE(t.create_date)) AS create_date
FROM transactions t
LEFT JOIN packages p ON t.pid = p.id
WHERE DATE(t.create_date) > (NOW() - INTERVAL 90 DAY)
ORDER BY t.create_date DESC
SQL;
        $this->db->query($query);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["package_name"]][$row["create_date"]][] = $row["total"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getSystemSent()
    {
        $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM sent
WHERE DATE(create_date) > (NOW() - INTERVAL 30 DAY)
ORDER BY create_date DESC
SQL;
        $this->db->query($query);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getSystemReceived()
    {
        $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(receive_date)) AS receive_date
FROM received
WHERE DATE(receive_date) > (NOW() - INTERVAL 30 DAY)
ORDER BY receive_date DESC
SQL;
        $this->db->query($query);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["receive_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getSystemUsers()
    {
        $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM users
WHERE DATE(create_date) > (NOW() - INTERVAL 30 DAY)
ORDER BY create_date DESC
SQL;
        $this->db->query($query);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    /**
     * @coverage System
     * @desc Create, update & delete functions
     */

    public function increment($uid, $column)
    {   
        $query = <<<SQL
UPDATE quota SET {$column} = {$column} + 1 WHERE uid = ? LIMIT 1
SQL;

        return $this->db->query($query, [
            $uid
        ]);
    }

    public function settings($name, $value)
    {
        try {
            $this->db->where("name", $name);
            return $this->db->update("settings", [
                "value" => $value
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($table, $data)
    {
        try {
            $this->db->insert($table, $data);
            return $this->db->last_insert_id();
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($id, $uid, $table, $data)
    {
        try {
            if($id)
                $this->db->where("id", $id);

            if($uid)
                $this->db->where("uid", $uid);
            return $this->db->update($table, $data);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($uid, $id, $table)
    {
        try {
            if($id)
                $this->db->where("id", $id);
            
            if($uid)
                $this->db->where("uid", $uid);
            return $this->db->delete($table);
        } catch (Exception $e) {
            return false;
        }
    }
}