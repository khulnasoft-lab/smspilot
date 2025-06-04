<?php

class Update_Model extends MVC_Model
{
	public function execute($sql)
	{
		return $this->db->query($sql);
	}
}