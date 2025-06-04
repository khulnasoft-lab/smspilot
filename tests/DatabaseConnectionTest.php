<?php
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testCanConnectToDatabase()
    {
        $mysqli = @new mysqli('127.0.0.1', 'zender_user', 'your_password', 'zender_db', 3306);
        $this->assertFalse($mysqli->connect_errno, 'Database connection failed: ' . $mysqli->connect_error);
        $mysqli->close();
    }
}
