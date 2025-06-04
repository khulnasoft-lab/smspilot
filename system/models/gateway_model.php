<?php

class Gateway_Model extends MVC_Model
{
	public function checkDevice($did, $hash)
    {
        $this->db->query("SELECT id FROM devices WHERE did = ? AND MD5(uid) = ?", [
            $did,
            $hash
        ]);
        
        return $this->db->num_rows();
    }

    public function checkUserKey($hash)
    {
        $this->db->query("SELECT id FROM users WHERE MD5(id) = ?", [
            $hash
        ]);

        return $this->db->num_rows();
    }

    public function checkSuspension($hash)
    {
        $this->db->query("SELECT id FROM users WHERE MD5(id) = ? AND suspended > 0", [
            $hash
        ]);

        return $this->db->num_rows();
    }

    public function checkReceived($rid, $uid, $did)
    {
        $this->db->query("SELECT id FROM received WHERE rid = ? AND uid = ? AND did = ?", [
            $rid,
            $uid,
            $did
        ]);

        return $this->db->num_rows();
    }

    public function getUserID($key)
    {
        return $this->db->query_one("SELECT id FROM users WHERE MD5(id) = ?", [
            $key
        ])["id"];
    }

    public function getUserLanguage($key)
    {
        return $this->db->query_one("SELECT language FROM users WHERE MD5(id) = ?", [
            $key
        ])["language"];
    }

    public function getPending($hash, $did)
    {
        $query = <<<SQL
SELECT s.id AS id, d.id AS device, s.sim AS sim, s.phone AS phone, s.message AS message, s.priority AS priority, s.api AS api, UNIX_TIMESTAMP(s.create_date) as `timestamp`
FROM sent s 
LEFT JOIN devices d ON s.did = d.did
WHERE MD5(s.uid) = ? AND s.did = ? AND s.status = 0
SQL;

        $this->db->query($query, [
            $hash,
            $did
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "api" => (boolean) $row["api"],
                    "sim" => $row["sim"],
                    "device" => (int) $row["device"],
                    "phone" => $row["phone"],
                    "message" => $row["message"],
                    "priority" => (boolean) $row["priority"],
                    "timestamp" => $row["timestamp"]
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getContacts($hash)
    {
        $query = <<<SQL
SELECT id, uid, gid, phone, name
FROM contacts
WHERE MD5(uid) = ?
SQL;

        $this->db->query($query, [
            $hash
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getDevices($hash)
    {
        $query = <<<SQL
SELECT id, uid, did, name, version, manufacturer, create_date
FROM devices
WHERE MD5(uid) = ?
SQL;

        $this->db->query($query, [
            $hash
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getWebhooks($hash)
    {
        $query = <<<SQL
SELECT secret, url, devices
FROM webhooks
WHERE MD5(uid) = ?
SQL;

        $this->db->query($query, [
            $hash
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = [
                    "secret" => $row["secret"],
                    "url" => $row["url"],
                    "devices" => explode(",", $row["devices"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getActions($hash)
    {
        $query = <<<SQL
SELECT type, event, devices, keywords, link, message
FROM actions
WHERE MD5(uid) = ?
SQL;

        $this->db->query($query, [
            $hash
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = [
                    "type" => $row["type"],
                    "event" => $row["event"],
                    "link" => $row["link"],
                    "message" => $row["message"],
                    "keywords" => explode(",", $row["keywords"]),
                    "devices" => explode(",", $row["devices"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function mark($id, $status)
    {
    	try {
    		$this->db->where("id", $id);
            return $this->db->update("sent", [
                "status" => $status
            ]);
        } catch(Exception $e){
            return false;
        }
    }

    public function received($data)
    {
        try {
            $this->db->insert("received", $data);
            return $this->db->last_insert_id();
        } catch(Exception $e){
            return false;
        }
    }

	public function register($data)
    {
        try {
            $this->db->insert("devices", $data);
            return $this->db->last_insert_id();
        } catch(Exception $e){
            return false;
        }
    }
}