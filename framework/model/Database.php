<?php

class Database
{
    protected static $_conn;

    public static function conn()
    {
        if (!self::$_conn) {
            self::$_conn = new PDO('sqlite:' . BASE_PATH . '/db.sqlite');
        }
        return self::$_conn;
    }

    public static function build($table, $specs)
    {
        $def = array();
        foreach ($specs as $key => $val) {
            $def[] = $val == 'auto' ? "\"{$key}\"" : "\"{$key}\" $val";
        }
        Database::conn()->query("CREATE TABLE IF NOT EXISTS \"{$table}\" (" . implode(', ', $def) . ")");
    }

    public static function select($table, $filter = array())
    {
        $params = array();
        $where = count($filter) ? array() : array('1');
        foreach ($filter as $key => $val) {
            $where[] = "\"{$key}\" = :{$key}";
            $params[':' . $key] = $val;
        }
        $sql = 'SELECT * FROM "' . $table . '" WHERE ' . implode(' AND ', $where);
        $sth = self::conn()->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if (!$sth) {
            throw new Exception(self::conn()->errorInfo()[2]);
        } else {
            $sth->execute($params);
            return $sth->fetchAll();
        }
    }

    public static function replace($table, $values)
    {
        $params = $keys = array();
        foreach ($values as $key => $val) {
            $keys["\"{$key}\""] = ":{$key}";
            $params[':' . $key] = $val;
        }
        $sql = 'REPLACE INTO "' . $table . '" (' . implode(', ', array_keys($keys)) . ') VALUES (' . implode(', ', array_values($keys)) . ')';
        $sth = self::conn()->prepare($sql);
        if (!$sth) {
            throw new Exception(self::conn()->errorInfo()[2] . ' (' . $sql . ')');
        } else {
            $sth->execute($params);
            if(self::conn()->errorInfo()[2]) {
                throw new Exception(self::conn()->errorInfo()[2] . ' (' . $sql . ')');
            }
            return self::conn()->lastInsertId() ?: $values['id'];
        }
    }
}