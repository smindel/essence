<?php

class Database
{
    protected static $_conn;

    public static function conn($conn = false)
    {
        if ($conn) {
            self::$_conn = $conn;
            self::query('PRAGMA foreign_keys = ON');
        } else if (!self::$_conn) {
            self::$_conn = new PDO('sqlite:' . BASE_PATH . '/db.sqlite');
            self::query('PRAGMA foreign_keys = ON');
        }
        return self::$_conn;
    }

    public static function create_table($table, $specs)
    {
        $def = array();
        foreach ($specs as $col => $spec) {
            $spec = self::spec($spec);
            if ($spec === false) continue;
            $def[] = "\"{$col}\" $spec";
        }
        Database::conn()->query("CREATE TABLE IF NOT EXISTS \"{$table}\" (" . implode(', ', $def) . ")");
    }

    public static function add_column($table, $col, $spec)
    {
        $spec = self::spec($spec);
        if ($spec === false) continue;
        Database::query("ALTER TABLE \"{$table}\" ADD COLUMN \"{$col}\" {$spec}");
    }

    public static function spec($in)
    {
        $constraint = empty($in['default']) ? '' : ' DEFAULT ' . $in['default'];
        $constraint .= empty($in['null']) ? '' : ' NOT NULL';
        $constraint .= empty($in['unique']) ? '' : ' UNIQUE';
        switch ($in['type']) {
            case 'ID': return 'INTEGER PRIMARY KEY AUTOINCREMENT';
            case 'TEXT': return 'VARCHAR(' . (empty($in['size']) ? 255 : $in['size']) . ')' . $constraint;
            case 'INT': return 'INT(' . (empty($in['size']) ? 11 : $in['size']) . ')' . $constraint;
            case 'FLOAT': return 'FLOAT' . $constraint;
            case 'DATE': return 'DATE' . $constraint;
            case 'DATETIME': return 'DATETIME' . $constraint;
            case 'BOOL': return 'BOOLEAN' . $constraint;
            case 'FOREIGN': return "INTEGER REFERENCES \"{$in['remoteclass']}\"(\"id\") ON DELETE " . (empty($in['oninvalid']) ? 'SET NULL' : $in['oninvalid']);
            case 'LOOKUP': return false;
        }
    }

    public static function select($table, $filter = array(), $sort)
    {
        $params = array();
        $where = count($filter) ? array() : array('1');
        foreach ($filter as $key => $val) {
            list($key, $operator) = explode(':', $key . ':=');
            $where[] = "\"{$key}\" {$operator} :{$key}";
            $params[':' . $key] = $val;
        }
        $sql = 'SELECT * FROM "' . $table . '" WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $sort;
        $sth = self::conn()->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if (!$sth) {
            $error = self::conn()->errorInfo();
            throw new Exception($error[2]);
        } else {
            $sth->execute($params);
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function query($sql, $style = PDO::FETCH_ASSOC)
    {
        $sth = self::conn()->query($sql);
        $error = self::conn()->errorInfo();
        if($error[2]) {
            throw new Exception($error[2] . ' (' . $sql . ')');
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

    public static function insert($table, $values)
    {
        $params = $keys = array();
        foreach ($values as $key => $val) {
            if ($key == 'id') continue;
            $keys["\"{$key}\""] = ":{$key}";
            $params[':' . $key] = $val;
        }
        $sql = 'INSERT INTO "' . $table . '" (' . implode(', ', array_keys($keys)) . ') VALUES (' . implode(', ', array_values($keys)) . ')';
        $sth = self::conn()->prepare($sql);
        if (!$sth) {
            $error = self::conn()->errorInfo();
            throw new Exception($error[2] . ' (' . $sql . ')');
        } else {
            $sth->execute($params);
            $error = self::conn()->errorInfo();
            if($error[2]) {
                throw new Exception($error[2] . ' (' . $sql . ') , ' . print_r($params, true));
            }
            return self::conn()->lastInsertId() ?: $values['id'];
        }
    }

    public static function update($table, $values)
    {
        $params = $keys = array();
        foreach ($values as $key => $val) {
            $params[':' . $key] = $val;
            if ($key == 'id') continue;
            $keys[] = "\"{$key}\" = :{$key}";
        }
        $sql = 'UPDATE "' . $table . '" SET ' . implode(', ', $keys) . ' WHERE "id" = :id';
        $sth = self::conn()->prepare($sql);
        if (!$sth) {
            $error = self::conn()->errorInfo();
            throw new Exception($error[2] . ' (' . $sql . ')');
        } else {
            $sth->execute($params);
            $error = self::conn()->errorInfo();
            if($error[2]) {
                throw new Exception($error[2] . ' (' . $sql . ') , ' . print_r($params, true));
            }
            return self::conn()->lastInsertId() ?: $values['id'];
        }
    }

    public static function delete($table, $id)
    {
        $sql = 'DELETE FROM "' . $table . '" WHERE "id" = :id';
        $sth = self::conn()->prepare($sql);
        if (!$sth) {
            $error = self::conn()->errorInfo();
            throw new Exception($error[2] . ' (' . $sql . ')');
        } else {
            $sth->execute(array(':id' => $id));
            $error = self::conn()->errorInfo();
            if($error[2]) {
                throw new Exception($error[2] . ' (' . $sql . ')');
            }
        }
    }
}