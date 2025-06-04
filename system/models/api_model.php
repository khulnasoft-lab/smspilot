<?php

class Api_Model extends MVC_Model
{
	public function getDeviceSentTotal($uid, $did)
	{
		$this->db->query("SELECT id FROM sent WHERE uid = ? AND did = ?", [
            $uid,
			$did
		]);

		return $this->db->num_rows();
	}

	public function getDeviceReceivedTotal($uid, $did)
	{
		$this->db->query("SELECT id FROM received WHERE uid = ? AND did = ?", [
            $uid,
			$did
		]);

		return $this->db->num_rows();
	}

	public function getKeys()
	{
		$query = <<<SQL
SELECT uid, MD5(uid) AS hash, `key`, devices, permissions FROM `keys`
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["key"]] = [
                	"hash" => $row["hash"],
                	"uid" => $row["uid"],
                	"key" => $row["key"],
                	"devices" => explode(",", $row["devices"]),
                	"permissions" => explode(",", $row["permissions"])
                ];

            return $rows;
        else:
            return [];
        endif;
	}

	public function getContacts($uid)
	{
		$query = <<<SQL
SELECT c.gid AS gid, g.name AS `group`, c.phone AS phone, c.name AS name
FROM contacts c
LEFT JOIN `groups` g ON c.gid = g.id 
WHERE c.uid = ?
SQL;

        $this->db->query($query, [
        	$uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["phone"]] = [
                	"gid" => (int) $row["gid"],
                	"group" => $row["group"],
                	"phone" => $row["phone"],
                	"name" => $row["name"]
                ];

            return $rows;
        else:
            return [];
        endif;
	}

	public function getGroups($uid)
	{
		$query = <<<SQL
SELECT id, name
FROM `groups`
WHERE uid = ?
SQL;

        $this->db->query($query, [
        	$uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                	"id" => (int) $row["id"],
                	"name" => $row["name"]
                ];

            return $rows;
        else:
            return [];
        endif;
	}

	public function getDevices($uid)
	{
		$query = <<<SQL
SELECT id, uid, did, name, version, manufacturer, create_date, UNIX_TIMESTAMP(create_date) AS `timestamp`,
	CASE
        WHEN ROUND(version, 0) = 4 THEN "Android KitKat"
        WHEN ROUND(version, 0) = 5 THEN "Android Lollipop"
        WHEN ROUND(version, 0) = 6 THEN "Android Marshmallow"
        WHEN ROUND(version, 0) = 7 THEN "Android Nougat"
        WHEN ROUND(version, 0) = 8 THEN "Android Oreo"
        WHEN ROUND(version, 0) = 9 THEN "Android Pie"
        WHEN ROUND(version, 0) = 10 THEN "Android 10"
        ELSE "Unknown"
    END AS version_name 
FROM devices 
WHERE uid = ?
SQL;

        $this->db->query($query, [
        	$uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
	}

	public function getSent($uid, int $limit, int $page, $device, $api, $priority)
	{
		$pagination = false; $device_flag = false; $api_flag = false; $priority_flag = false;

		if($page)
			$pagination = ($page > 1 ? ($page < 3 ? $limit : ($page - 1) * $limit) . ", " : false);

		if($device)
			$device_flag = " AND s.did = '{$device}'";

		if($api)
			$api_flag = " AND s.api = 1";

		if($priority)
			$priority_flag = " AND s.priority = 1";

		$query = <<<SQL
SELECT d.id AS device, s.sim AS sim, s.api AS api, s.phone AS phone, s.message AS message, s.priority AS priority, UNIX_TIMESTAMP(s.create_date) AS `timestamp`
FROM sent s
LEFT JOIN devices d ON s.did = d.did
WHERE s.status = 1 AND s.uid = ? {$device_flag}{$api_flag}{$priority_flag}
ORDER BY s.create_date DESC
LIMIT {$pagination}{$limit}
SQL;

        $this->db->query($query, [
        	$uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = [
                	"sim" => (int) ($row["sim"] < 1 ? 1 : $row["sim"]++),
                	"api" => ($row["api"] > 0 ? true : false),
                	"device" => (int) $row["device"],
                	"phone" => $row["phone"],
                	"message" => $row["message"],
                	"priority" => ($row["priority"] > 0 ? true : false),
                	"timestamp" => (int) $row["timestamp"]
                ];

            return $rows;
        else:
            return [];
        endif;
	}

	public function getReceived($uid, int $limit, int $page, $device)
	{
		$pagination = false; $device_flag = false;

		if($page)
			$pagination = ($page > 1 ? ($page < 3 ? $limit : ($page - 1) * $limit) . ", " : false);

		if($device)
			$device_flag = " AND r.did = '{$device}'";

		$query = <<<SQL
SELECT d.id AS device, r.phone AS phone, r.message AS message, UNIX_TIMESTAMP(r.receive_date) AS `timestamp`
FROM received r
LEFT JOIN devices d ON r.did = d.did
WHERE r.uid = ? {$device_flag}
ORDER BY r.receive_date DESC
LIMIT {$pagination}{$limit}
SQL;

        $this->db->query($query, [
        	$uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = [
                	"device" => (int) $row["device"],
                	"phone" => $row["phone"],
                	"message" => $row["message"],
                	"timestamp" => (int) $row["timestamp"]
                ];

            return $rows;
        else:
            return [];
        endif;
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

    public function delete($uid, $id, $table)
    {
        try {
            $this->db->where("id", $id);
            $this->db->where("uid", $uid);
            return $this->db->delete($table);
        } catch (Exception $e) {
            return false;
        }
    }
} 