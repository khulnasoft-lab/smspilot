<?php

class Cron_Model extends MVC_Model
{
	public function resetQuota()
	{
		return $this->db->query("TRUNCATE TABLE quota");
	}

    public function getScheduled()
    {
        $query = <<<SQL
SELECT id, uid, MD5(uid) AS hash, did, sim, groups, name, numbers, message, `repeat`, send_date
FROM scheduled
SQL;

        $this->db->query($query);

        if($this->db->num_rows() > 0):
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
SELECT s.id AS id, u.email AS email, UNIX_TIMESTAMP(DATE_ADD(DATE(s.date), INTERVAL 1 MONTH)) AS expire
FROM subscriptions s 
LEFT JOIN users u ON s.uid = u.id
SQL;

		$this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
	}

	public function delete($id, $table)
    {
        try {
            $this->db->where("id", $id);
            return $this->db->delete($table);
        } catch (Exception $e) {
            return false;
        }
    }
}