<?php
namespace App\Http\Controllers\Server;


use Illuminate\Database\MySqlConnection;

class ConnectDatabaseController
{
    public function __construct()
    {

    }

    public function connect()
    {
        $mysqli = new \mysqli('localhost','homestead','secret','gzt',33060);
        $mysqli->query("set names 'utf8';");
        $mysqli->select_db('gzt');
        $sql="select name from users;";
        $ret=$mysqli->query($sql);
        dd($ret);
        dd(11);
    }

}











?>