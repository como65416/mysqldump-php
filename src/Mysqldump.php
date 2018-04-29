<?php

namespace comoco\Mysqldump;

class Mysqldump
{
    protected $database = null;
    protected $options = [];
    protected $tables = [];

    public function __construct()
    {
        $this->options['port'] = 3306;
    }

    public function setHost($host)
    {
        $this->options['host'] = $host;
        return $this;
    }
    
    public function setPort($port)
    {
        $this->options['port'] = $port;
        return $this;
    }
    
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    public function setUser($user)
    {
        $this->options['user'] = $user;
        return $this;
    }

    public function setPassword($password)
    {
        $this->options['password']= $password;
        return $this;
    }

    public function addTable($table_name, $where_condition = null)
    {
        $this->tables[$table_name] = $where_condition;
        return $this;
    }

    public function hexBlob()
    {
        $this->options['hex-blob'] = null;
        return $this;
    }
    
    public function completeInsert()
    {
        $this->options['complete-insert']= null;
        return $this;
    }
    
    public function setGtidPurged($value)
    {
        $this->options['set-gtid-purged'] = $value;
        return $this;
    }

    public function disableExtendedInsert()
    {
        $this->options['skip-extended-insert'] = 'false';
        return $this;
    }

    public function disableLockTable()
    {
        $this->options['lock-tables'] = 'false';
        return $this;
    }

    public function withoutComments()
    {
        $this->options['skip-comments'] = null;
        return $this;
    }

    public function withoutAddLock()
    {
        $this->options['skip-add-locks'] = null;
        return $this;
    }

    public function withoudCreateDB()
    {
        $this->options['no-create-db'] = null;
        return $this;
    }

    public function withoutCreateTable()
    {
        $this->options['no-create-info'] = null;
        return $this;
    }

    public function withoutTableData()
    {
        $this->options['no-data'] = null;
        return $this;
    }

    public function withoutCreateInfo()
    {
        $this->options['no-create-info'] = null;
        return $this;
    }

    public function withoutSetCharset()
    {
        $this->options['skip-set-charset'] = null;
        return $this;
    }

    public function setOption($option_name, $value = null)
    {
        $this->options[$option_name] = $value;
        return $this;
    }

    public function dump()
    {
        $command = $this->generateExcuteCommand();
        return shell_exec($command . " 2>&1;");
    }

    protected function generateExcuteCommand()
    {
        $command = "mysqldump ";
        foreach ($this->options as $option => $value) {
            if ($value !== null) {
                $command .= "--" . $option . "=" . json_encode($value) . " ";
            } else {
                $command .= "--" . $option . " ";
            }
        }
        if (empty($this->database)) {
            $command .= "--all-databases";
        } else {
            $command .= json_encode($this->database) . " ";
        }
        foreach ($this->tables as $table_name => $where_condition) {
            $command .= $table_name . " ";
            if (!empty($where_condition)) {
                $command .= "--where=" . json_encode($where_conditions) . " ";
            }
        }
        return trim($command);
    }
}