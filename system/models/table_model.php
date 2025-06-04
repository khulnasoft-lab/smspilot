<?php

class Table_Model extends MVC_Model
{
    /**
     * @coverage System
     * @desc System table models
     */

    public function getSent($id, $params = false)
    {
        if($params):
            $phone = ($params["phone"] ? " AND s.phone = \"{$params["phone"]}\"" : false);
            $sim = ($params["sim"] ? " AND s.sim = {$params["sim"]}" : false);
            $priority = ($params["priority"] ? " AND s.priority = {$params["priority"]}" : false);
            $api = ($params["api"] ? " AND s.api = \"{$params["api"]}\"" : false);
            $device = ($params["device"] ? " AND s.did = \"{$params["device"]}\"" : false);
            $history = " AND DATE(s.create_date) >= '{$params["start"]}' AND DATE(s.create_date) <= '{$params["end"]}' {$phone} {$sim} {$priority} {$api} {$device}";
            $limit = false;
        else:
            $history = false;
            $limit = "LIMIT 500";
        endif;

        $query = <<<SQL
SELECT s.id AS id, s.did AS did, CONCAT(DATE_FORMAT(s.create_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(s.create_date, '%l:%i %p')) AS create_date, UNIX_TIMESTAMP(s.create_date) AS sorting, 
    IF(d.name IS NULL, 'Removed', CONCAT(
        CASE
            WHEN ROUND(d.version, 0) = 4 THEN "KitKat"
            WHEN ROUND(d.version, 0) = 5 THEN "Lollipop"
            WHEN ROUND(d.version, 0) = 6 THEN "Marshmallow"
            WHEN ROUND(d.version, 0) = 7 THEN "Nougat"
            WHEN ROUND(d.version, 0) = 8 THEN "Oreo"
            WHEN ROUND(d.version, 0) = 9 THEN "Pie"
            WHEN ROUND(d.version, 0) = 10 THEN "Android 10"
            ELSE "Unknown"
        END,
        "<br>",
        d.name
      )) AS device,
    CONCAT(
        IF(c.name IS NULL, "Unknown", c.name),
        '<br>',
        s.phone
      ) AS phone,
    CONCAT(
        '<div class=\"table-icons\">',
            '<span class=\"badge badge-', IF(s.status < 1, "primary", IF(s.status = 1, "success", "danger")), ' p-2 mr-1\">',
                '<i class=\"la la-telegram\"></i>',
            '</span>',
            '<span class=\"badge badge-', IF(s.priority > 0, "success", "primary"), ' p-2 mr-1\">',
                '<i class=\"la la-thumbtack\"></i>',
            '</span>',
            '<span class=\"badge badge-', IF(s.api > 0, "success", "primary"), ' p-2\">',
                '<i class=\"la la-terminal\"></i>',
            '</span>',
        '</div>'
      ) AS details,
    CONCAT(
        IF(s.sim < 1, "SIM1", "SIM2"),
        '<br>',
        '<a href=\"#\" zender-toggle=\"zender.view/sent-', s.id, '\">',
            '<i class=\"la la-eye la-lg\"></i>&nbsp;',
            LENGTH(s.message),  ' bytes',
        '</a>'
      ) AS message,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"sent/', s.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM sent s
LEFT JOIN contacts c ON s.phone = c.phone AND s.uid = c.uid
LEFT JOIN devices d ON s.did = d.did AND s.uid = d.uid
WHERE s.uid = ? {$history}
{$limit}
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

    public function getReceived($id, $params = false)
    {
        if($params):
            $phone = ($params["phone"] ? " AND r.phone = \"{$params["phone"]}\"" : false);
            $device = ($params["device"] ? " AND r.did = \"{$params["device"]}\"" : false);
            $history = " AND DATE(r.receive_date) >= '{$params["start"]}' AND DATE(r.receive_date) <= '{$params["end"]}' {$phone} {$device}";
            $limit = false;
        else:
            $history = false;
            $limit = "LIMIT 500";
        endif;

        $query = <<<SQL
SELECT r.id AS id, r.did AS did, CONCAT(DATE_FORMAT(r.receive_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(r.receive_date, '%l:%i %p')) AS receive_date, UNIX_TIMESTAMP(r.receive_date) AS sorting,
    IF(d.name IS NULL, 'Removed', CONCAT(
        CASE
            WHEN ROUND(d.version, 0) = 4 THEN "KitKat"
            WHEN ROUND(d.version, 0) = 5 THEN "Lollipop"
            WHEN ROUND(d.version, 0) = 6 THEN "Marshmallow"
            WHEN ROUND(d.version, 0) = 7 THEN "Nougat"
            WHEN ROUND(d.version, 0) = 8 THEN "Oreo"
            WHEN ROUND(d.version, 0) = 9 THEN "Pie"
            WHEN ROUND(d.version, 0) = 10 THEN "Android 10"
            ELSE "Unknown"
        END,
        "<br>",
        d.name
      )) AS device,
    CONCAT(
        IF(c.name IS NULL, "Unknown", c.name),
        '<br>',
        r.phone
      ) AS phone,
    CONCAT(
        '<a href=\"#\" zender-toggle=\"zender.view/received-', r.id, '\">',
            '<i class=\"la la-eye la-lg\"></i>&nbsp;',
            LENGTH(r.message),  ' bytes',
        '</a>'
      ) AS message,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"received/', r.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM received r
LEFT JOIN contacts c ON r.phone = c.phone AND r.uid = c.uid
LEFT JOIN devices d ON r.did = d.did AND r.uid = d.uid
WHERE r.uid = ? {$history}
{$limit}
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

    public function getScheduled($id)
    {
        $query = <<<SQL
SELECT s.id AS id, s.did AS did, s.name AS name, IF(s.repeat < 2, "Yes", "No") AS `repeat`, CONCAT(FROM_UNIXTIME(s.send_date, '%c/%e/%Y'), '<br>', FROM_UNIXTIME(s.send_date, '%l:%i %p')) AS send_date, s.send_date AS sorting, 
    IF(d.name IS NULL, 'Automatic', CONCAT(
        CASE
            WHEN ROUND(d.version, 0) = 4 THEN "KitKat"
            WHEN ROUND(d.version, 0) = 5 THEN "Lollipop"
            WHEN ROUND(d.version, 0) = 6 THEN "Marshmallow"
            WHEN ROUND(d.version, 0) = 7 THEN "Nougat"
            WHEN ROUND(d.version, 0) = 8 THEN "Oreo"
            WHEN ROUND(d.version, 0) = 9 THEN "Pie"
            WHEN ROUND(d.version, 0) = 10 THEN "Android 10"
            ELSE "Unknown"
        END,
        "<br>",
        d.name
      )) AS device,
    CONCAT(
        '<a href=\"#\" zender-toggle=\"zender.view/scheduledrecipients-', s.id, '\">',
            '<i class=\"la la-address-book la-lg\"></i>&nbsp; View',
        '</a>'
      ) AS recipients,
    CONCAT(
        IF(s.sim < 1, "SIM1", "SIM2"),
        '<br>',
        '<a href=\"#\" zender-toggle=\"zender.view/scheduled-', s.id, '\">',
            '<i class=\"la la-eye la-lg\"></i>&nbsp;',
            LENGTH(s.message),  ' bytes',
        '</a>'
      ) AS message,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"scheduled/', s.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM scheduled s
LEFT JOIN devices d ON s.did = d.did AND s.uid = d.uid
WHERE s.uid = ?
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

    public function getTemplates($id)
    {
        $query = <<<SQL
SELECT id, uid, name,
    CONCAT(
        '<a href=\"#\" zender-toggle=\"zender.view/templates-', id, '\">',
            '<i class=\"la la-eye la-lg\"></i>&nbsp;',
            LENGTH(format),  ' bytes',
        '</a>'
      ) AS format,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.template/', id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"templates/', id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM templates
WHERE uid = ?
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

	public function getContacts($id)
    {
        $query = <<<SQL
SELECT c.id AS id, g.name AS "group", c.phone AS phone, c.name AS name, 
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.contact/', c.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"contacts/', c.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM contacts c
LEFT JOIN `groups` g ON c.gid = g.id
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

	public function getGroups($id)
    {
        $query = <<<SQL
SELECT g.id AS id, g.uid AS uid, g.name AS name, (SELECT COUNT(id) FROM contacts WHERE gid = g.id) AS total, 
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.group/', g.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"groups/', g.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM `groups` g
WHERE g.uid = ?
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

    public function getDevices($id)
    {
        $query = <<<SQL
SELECT id, uid, did, name, manufacturer, DATE_FORMAT(create_date, '%c/%e/%Y %l:%i %p') AS create_date, UNIX_TIMESTAMP(create_date) AS sorting,
    CASE
        WHEN ROUND(version, 0) = 4 THEN "Android KitKat"
        WHEN ROUND(version, 0) = 5 THEN "Android Lollipop"
        WHEN ROUND(version, 0) = 6 THEN "Android Marshmallow"
        WHEN ROUND(version, 0) = 7 THEN "Android Nougat"
        WHEN ROUND(version, 0) = 8 THEN "Android Oreo"
        WHEN ROUND(version, 0) = 9 THEN "Android Pie"
        WHEN ROUND(version, 0) = 10 THEN "Android 10"
        ELSE "Unknown"
    END AS version,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"devices/', id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM devices
WHERE uid = ?
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

    public function getKeys($id)
    {
        $query = <<<SQL
SELECT k.id AS id, k.name AS name, IF(k.devices = "0", "Automatic", IF(group_concat(d.name ORDER BY FIND_IN_SET(d.did, k.devices) ASC SEPARATOR ', ') IS NULL, 'Removed', group_concat(d.name ORDER BY FIND_IN_SET(d.did, k.devices) ASC SEPARATOR ', '))) AS devices, DATE_FORMAT(k.create_date, '%c/%e/%Y %l:%i %p') AS create_date, UNIX_TIMESTAMP(k.create_date) AS sorting,
    CONCAT(
        '<a href=\"#\" zender-view=\"keys/', k.id, '\">',
            '<i class=\"la la-eye la-lg\"></i>&nbsp;',
           (CHAR_LENGTH(k.permissions) - CHAR_LENGTH(REPLACE(k.permissions, ',', '')) + 1), ' selected',
        '</a>'
      ) AS permissions,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-secondary\" data-clipboard-text=\"', k.key, '\" zender-clipboard>',
                    '<i class=\"la la-key\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.apikey/', k.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"keys/', k.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM `keys` k
LEFT JOIN devices d ON FIND_IN_SET(d.did, k.devices) > 0 AND k.uid = d.uid
WHERE k.uid = ?
GROUP BY k.id
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


    public function getWebhooks($id)
    {
        $query = <<<SQL
SELECT w.id AS id, w.name AS name, CONCAT(MID(w.url, 1, 20), '...') AS url, IF(w.devices = "0", "Automatic", IF(w.devices = "0", "Automatic", IF(group_concat(d.name ORDER BY FIND_IN_SET(d.did, w.devices) ASC SEPARATOR ', ') IS NULL, 'Removed', group_concat(d.name ORDER BY FIND_IN_SET(d.did, w.devices) ASC SEPARATOR ', ')))) AS devices, DATE_FORMAT(w.create_date, '%c/%e/%Y %l:%i %p') AS create_date, UNIX_TIMESTAMP(w.create_date) AS sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-secondary\" data-clipboard-text=\"', w.secret, '\" zender-clipboard>',
                    '<i class=\"la la-user-secret\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.webhook/', w.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"webhooks/', w.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM webhooks w
LEFT JOIN devices d ON FIND_IN_SET(d.did, w.devices) > 0 AND w.uid = d.uid
WHERE w.uid = ?
GROUP BY w.id
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

    public function getActions($id)
    {
        $query = <<<SQL
SELECT a.id AS id, a.name AS name, IF(a.devices = "0", "Automatic", IF(a.devices = "0", "Automatic", IF(group_concat(d.name ORDER BY FIND_IN_SET(d.did, a.devices) ASC SEPARATOR ', ') IS NULL, 'Removed', group_concat(d.name ORDER BY FIND_IN_SET(d.did, a.devices) ASC SEPARATOR ', ')))) AS devices, DATE_FORMAT(a.create_date, '%c/%e/%Y %l:%i %p') AS create_date, UNIX_TIMESTAMP(a.create_date) AS sorting,
    CASE
        WHEN a.type < 2 THEN 'Hook'
        ELSE 'Autoreply'
    END AS type,
    CASE
        WHEN a.event = 1 THEN 'On Send'
        WHEN a.event = 2 THEN 'On Receive'
        ELSE 'None'
    END AS event,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.',
                    CASE
                        WHEN a.type < 2 THEN 'hook'
                        ELSE 'autoreply'
                    END
                    ,'/', a.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"actions/', a.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM actions a
LEFT JOIN devices d ON FIND_IN_SET(d.did, a.devices) > 0 AND a.uid = d.uid
WHERE a.uid = ?
GROUP BY a.id
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
     * @coverage Administration
     * @desc Admin models
     */

    public function getUsers()
    {
        $query = <<<SQL
SELECT u.id AS id, u.email AS email, u.name AS name, l.name AS language, CONCAT(DATE_FORMAT(u.create_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(u.create_date, '%l:%i %p')) AS create_date, UNIX_TIMESTAMP(u.create_date) AS sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-',
                    IF(u.suspended > 0, "success", "danger")
                ,'\" user-id=\"', u.id ,'\" zender-action=\"', 
                    IF(u.suspended > 0, "unsuspend", "suspend")
                ,'\"', 
                    IF(u.id < 2, " disabled", false)
                ,'>',
                    '<i class=\"la la-', 
                    IF(u.suspended > 0, "check-circle", "ban")
                    ,'\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-warning\" user-id=\"', u.id ,'\" zender-action=\"impersonate\"', 
                    IF(u.id < 2, " disabled", false)
                ,'>',
                    '<i class=\"la la-sign-in\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.user/', u.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"users/', u.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM users u
LEFT JOIN languages l ON u.language = l.id
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

    public function getRoles()
    {
        $query = <<<SQL
SELECT id, name,
    CASE
        WHEN id < 2 THEN CONCAT(
            '<i class=\"la la-ban la-lg\"></i>&nbsp;',
            'No Permissions'
        )
        ELSE CONCAT(
            '<a href=\"#\" zender-view=\"roles/', id ,'\">',
                '<i class=\"la la-eye la-lg\"></i>&nbsp;',
                (CHAR_LENGTH(permissions) - CHAR_LENGTH(REPLACE(permissions, ',', '')) + 1), ' selected',
            '</a>'
        )
    END AS permissions,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.role/', id ,'\"', IF(id < 2, " disabled", ""), '>',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"roles/', id ,'\"', IF(id < 2, " disabled", ""), '>',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM roles
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

    public function getPackages()
    {
        $query = <<<SQL
SELECT p.id AS id, FORMAT(p.send_limit, 0) AS send, FORMAT(p.receive_limit, 0) AS receive, FORMAT(p.contact_limit, 0) AS contacts, FORMAT(p.device_limit, 0) AS devices, FORMAT(p.key_limit, 0) AS `keys`, FORMAT(p.webhook_limit, 0) AS webhooks, p.name AS name, IF(p.price < 1, 'Free', CONCAT('$', FORMAT(p.price, 0))) AS price, CONCAT(DATE_FORMAT(p.create_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(p.create_date, '%l:%i %p')) AS create_date, UNIX_TIMESTAMP(p.create_date) AS sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.package/', p.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"packages/', p.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM packages p
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

    public function getVouchers()
    {
        $query = <<<SQL
SELECT v.id AS id, v.name AS name, p.name AS package, CONCAT(DATE_FORMAT(v.create_date, '%c/%e/%Y')) AS create_date, UNIX_TIMESTAMP(v.create_date) AS create_sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-secondary\" data-clipboard-text=\"', v.code, '\" zender-clipboard>',
                    '<i class=\"la la-wallet\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"vouchers/', v.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM vouchers v
LEFT JOIN packages p ON v.package = p.id
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

    public function getSubscriptions()
    {
        $query = <<<SQL
SELECT s.id AS id, u.name AS name, p.name AS package, IF(p.price < 1, 'Free', CONCAT('$', FORMAT(p.price, 0))) AS price, DATE_FORMAT(s.date, '%M %e, %Y') AS start_date, DATE_FORMAT(DATE_ADD(DATE(s.date), INTERVAL 1 MONTH), '%M %e, %Y') AS expire_date, UNIX_TIMESTAMP(s.date) AS start_sorting, UNIX_TIMESTAMP(DATE_ADD(DATE(s.date), INTERVAL 1 MONTH)) AS expire_sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"subscriptions/', s.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM subscriptions s
LEFT JOIN users u ON s.uid = u.id
LEFT JOIN packages p ON s.pid = p.id
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

    public function getTransactions()
    {
        $query = <<<SQL
SELECT t.id AS id, u.name AS name, IF(p.name IS NULL, 'Removed', p.name) AS package, CONCAT('$', FORMAT(t.price, 0)) AS price, UCASE(t.provider) AS provider, DATE_FORMAT(t.create_date, '%c/%e/%Y %l:%i %p') AS create_date, UNIX_TIMESTAMP(t.create_date) AS sorting
FROM transactions t
LEFT JOIN users u ON t.uid = u.id
LEFT JOIN packages p ON t.pid = p.id
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

    public function getWidgets()
    {
        $query = <<<SQL
SELECT id, name, CONCAT(DATE_FORMAT(create_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(create_date, '%l:%i %p')) AS create_date, UNIX_TIMESTAMP(create_date) AS sorting,
    CASE
        WHEN type = 1 THEN "Block"
        ELSE "Modal"
    END AS type,
    CASE
        WHEN size = "sm" THEN "Small"
        WHEN size = "md" THEN "Medium"
        WHEN size = "lg" THEN "Large"
        WHEN size = "xl" THEN "Extra Large"
        ELSE "Extra Large"
    END AS size,
    CASE
        WHEN position = "center" THEN "Center"
        WHEN position = "left" THEN "Left"
        WHEN position = "right" THEN "Right"
        ELSE "Right"
    END AS position,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-secondary\" data-clipboard-text=\'',
                    CASE
                        WHEN type = 1 THEN CONCAT('{_block(\"', MD5(id), '\")}')
                        WHEN type = 2 THEN CONCAT('zender-toggle=\"', MD5(id), '\"')
                        ELSE CONCAT('zender-toggle=\"', MD5(id), '\"')
                    END,
                    '\' zender-clipboard>',
                    '<i class=\"la la-clipboard\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.widget/', id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"widgets/', id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM widgets
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
SELECT p.id AS id, IF(p.logged < 1, 'No', 'Yes') AS logged, p.name AS name, p.content AS content, IF(group_concat(r.name ORDER BY FIND_IN_SET(r.id, p.roles) ASC SEPARATOR ', ') IS NULL, 'Removed', group_concat(r.name ORDER BY FIND_IN_SET(r.id, p.roles) ASC SEPARATOR ', ')) AS roles, CONCAT(DATE_FORMAT(p.create_date, '%c/%e/%Y'), '<br>', DATE_FORMAT(p.create_date, '%l:%i %p')) AS create_date, UNIX_TIMESTAMP(p.create_date) AS sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-secondary\" data-clipboard-text=\'',
                    CONCAT('zender-page=\"', p.id, '/', p.slug, '\"'),
                    '\' zender-clipboard>',
                    '<i class=\"la la-clipboard\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.page/', p.id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"pages/', p.id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM pages p 
LEFT JOIN roles r ON FIND_IN_SET(r.id, p.roles) > 0
GROUP BY p.id
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

    public function getLanguages()
    {
        $query = <<<SQL
SELECT id, iso, name, CONCAT(LENGTH(translations), ' bytes') AS size, CONCAT(DATE_FORMAT(create_date, '%c/%e/%Y %l:%i %p')) AS create_date, UNIX_TIMESTAMP(create_date) AS sorting,
    CONCAT(
        '<div class=\"table-buttons\">',
            '<div class=\"btn-group\">',
                '<button class=\"btn btn-md btn-primary\" zender-toggle=\"zender.edit.language/', id ,'\">',
                    '<i class=\"la la-edit\"></i>',
                '</button>',
                '<button class=\"btn btn-md btn-danger\" zender-delete=\"languages/', id ,'\">',
                    '<i class=\"la la-trash\"></i>',
                '</button>',
            '</div>',
        '</div>'
      ) AS options
FROM languages
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
}