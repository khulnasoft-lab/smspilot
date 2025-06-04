<?php

class Widget_Model extends MVC_Model
{   
    /**
     * @type Check functions
     */

    public function checkModal($hash)
    {
        $this->db->query("SELECT id FROM widgets WHERE type = 2 AND MD5(id) = ?", [
            $hash
        ]);

        return $this->db->num_rows();
    }

    /**
     * @type Single Row
     */

    public function getContent($id, $table, $column)
    {
        return $this->db->query_one("SELECT {$column} FROM {$table} WHERE id = ? AND uid = ?", [
            $id,
            logged_id
        ])[$column];
    }

    public function getModal($hash)
    {
        return $this->db->query_one("SELECT id, size, position, icon, name, content FROM widgets WHERE type = 2 AND MD5(id) = ?", [
            $hash
        ]);
    }

    public function getTemplate($id)
    {
        return $this->db->query_one("SELECT id, name, format FROM templates WHERE id = ?", [
            $id
        ]);
    }

    public function getContact($id)
    {
        return $this->db->query_one("SELECT id, name, phone, gid FROM contacts WHERE id = ?", [
            $id
        ]);
    }

    public function getGroup($id)
    {
        return $this->db->query_one("SELECT id, name FROM groups WHERE id = ?", [
            $id
        ]);
    }

    public function getKey($id)
    {
        return $this->db->query_one("SELECT id, uid, `key`, name, devices, permissions FROM `keys` WHERE id = ?", [
            $id
        ]);
    }

    public function getWebhook($id)
    {
        return $this->db->query_one("SELECT id, uid, secret, name, url, devices FROM webhooks WHERE id = ?", [
            $id
        ]);
    }

    public function getAction($id)
    {
        return $this->db->query_one("SELECT id, uid, type, event, name, devices, keywords, link, message, create_date FROM actions WHERE id = ?", [
            $id
        ]);
    }

    public function getUser($id)
    {
        return $this->db->query_one("SELECT id, role, email, name, language FROM users WHERE id = ?", [
            $id
        ]);
    }

    public function getRole($id)
    {
        return $this->db->query_one("SELECT id, name, permissions FROM roles WHERE id = ?", [
            $id
        ]);
    }

    public function getPackage($id)
    {
        return $this->db->query_one("SELECT id, name, price, send_limit, receive_limit, contact_limit, device_limit, key_limit, webhook_limit FROM packages WHERE id = ?", [
            $id
        ]);
    }

    public function getWidget($id)
    {
        return $this->db->query_one("SELECT id, type, size, position, icon, name, content FROM widgets WHERE id = ?", [
            $id
        ]);
    }

    public function getPage($id)
    {
        return $this->db->query_one("SELECT id, roles, logged, slug, name, content FROM pages WHERE id = ?", [
            $id
        ]);
    }

    public function getLanguage($id)
    {
        return $this->db->query_one("SELECT id, iso, name, translations FROM languages WHERE id = ?", [
            $id
        ]);
    }

    /**
     * @type Multiple Rows
     */

    public function getTemplates($uid)
    {
        $query = <<<SQL
SELECT id, uid, name, format FROM templates WHERE uid = ?
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "uid" => $row["uid"],
                    "name" => $row["name"],
                    "format" => $row["format"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

	public function getGroups($uid)
	{
		$query = <<<SQL
SELECT id, uid, name FROM groups WHERE uid = ?
SQL;

		$this->db->query($query, [
			$uid
		]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                	"name" => $row["name"],
                	"token" => strtolower($row["name"]),
                	"default" => (strtolower($row["name"]) == "default" ? true : false)
                ];

            return $rows;
        else:
            return [];
        endif;
	}

    public function getDevices($uid)
    {
        $query = <<<SQL
SELECT id, uid, did, name FROM devices WHERE uid = ?
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = [
                    "name" => $row["name"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getLanguages()
    {
        $query = <<<SQL
SELECT id, iso, name FROM languages
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "name" => $row["name"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getRoles()
    {
        $query = <<<SQL
SELECT id, name, permissions FROM roles
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

    public function getSystemSettings()
    {
        $query = <<<SQL
SELECT id, name, value FROM settings
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
SELECT id, FORMAT(send_limit, 0) AS send, FORMAT(receive_limit, 0) AS receive, FORMAT(contact_limit, 0) AS contacts, FORMAT(device_limit, 0) AS devices, FORMAT(key_limit, 0) AS `keys`, FORMAT(webhook_limit, 0) AS webhooks, name, price
FROM packages
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
SELECT id, role, name, email FROM users
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "id" => $row["id"],
                    "role" => $row["role"],
                    "name" => $row["name"],
                    "email" => $row["email"],
                    "token" => strtolower("{$row["name"]} {$row["email"]}")
                ];

            return $rows;
        else:
            return [];
        endif;
    }
}