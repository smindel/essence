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

    public static function create($table, $specs)
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
            list($key, $operator) = explode(':', $key . ':=');
            $where[] = "\"{$key}\" {$operator} :{$key}";
            $params[':' . $key] = $val;
        }
        $sql = 'SELECT * FROM "' . $table . '" WHERE ' . implode(' AND ', $where);
        $sth = self::conn()->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if (!$sth) {
            throw new Exception(self::conn()->errorInfo()[2]);
        } else {
            $sth->execute($params);
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function query($sql, $style = PDO::FETCH_ASSOC)
    {
        $sth = self::conn()->query($sql);
        if(self::conn()->errorInfo()[2]) {
            throw new Exception(self::conn()->errorInfo()[2] . ' (' . $sql . ')');
        }
        return $sth->fetchAll($style);
    }

    public static function tables()
    {
        return self::query("SELECT \"name\" FROM \"sqlite_master\" WHERE \"type\" = 'table' AND \"name\" NOT LIKE 'sqlite_%'", PDO::FETCH_COLUMN);
    }

    public static function table($table)
    {
        $results = self::query("PRAGMA table_info(\"{$table}\")");
        $cols = array();
        foreach ($results as $result) {
            $cols[$result['name']] = $result;
        }
        return $cols;
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

    public static function delete($table, $id)
    {
        $sql = 'DELETE FROM "' . $table . '" WHERE "id" = :id';
        $sth = self::conn()->prepare($sql);
        if (!$sth) {
            throw new Exception(self::conn()->errorInfo()[2] . ' (' . $sql . ')');
        } else {
            $sth->execute(array(':id' => $id));
            if(self::conn()->errorInfo()[2]) {
                throw new Exception(self::conn()->errorInfo()[2] . ' (' . $sql . ')');
            }
        }
    }
}