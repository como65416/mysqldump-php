<?php

namespace comoco\Mysqldump;

use comoco\Mysqldump\Exception\MysqldumpException;
use Symfony\Component\Process\Process;

class Mysqldump
{
    protected $database = null;
    protected $options = [];
    protected $tables = [];

    public function __construct()
    {
        $this->options['port'] = 3306;
    }

    /**
     * set mysql server
     * @param string $host server host
     * @return $this
     */
    public function setHost($host)
    {
        $this->options['host'] = $host;
        return $this;
    }

    /**
     * set mysql server port
     * @param int $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->options['port'] = $port;
        return $this;
    }

    /**
     * set target database name
     * @param string $database database name
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * set username for login mysql
     * @param string $user username
     * @return $this
     */
    public function setUser($user)
    {
        $this->options['user'] = $user;
        return $this;
    }

    /**
     * set password for login mysql
     * @param string $password password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->options['password']= $password;
        return $this;
    }

    /**
     * add target table which want to be dump
     * @param string $table_name      table name
     * @param string|null $where_condition dump only rows selected by the given condition
     * @return $this
     */
    public function addTable($table_name, $where_condition = null)
    {
        $this->tables[$table_name] = $where_condition;
        return $this;
    }

    /**
     * Dump binary columns using hexadecimal notation (same as --hex-blob option)
     * @return $this
     */
    public function hexBlob()
    {
        $this->options['hex-blob'] = null;
        return $this;
    }

    /**
     * Use complete INSERT statements that include column names (same as --complete-insert option)
     * @return $this
     */
    public function completeInsert()
    {
        $this->options['complete-insert']= null;
        return $this;
    }

    /**
     * Whether to add SET @@GLOBAL.GTID_PURGED to output (same as --set-gtid-purged option)
     * @param string $value 'OFF'|'ON'|'AUTO'
     * @return $this
     */
    public function setGtidPurged($value)
    {
        $this->options['set-gtid-purged'] = $value;
        return $this;
    }

    /**
     * Turn off extended-insert (same as --skip-extended-insert options)
     * @return $this
     */
    public function disableExtendedInsert()
    {
        $this->options['skip-extended-insert'] = 'false';
        return $this;
    }

    /**
     * not lock all tables before dumping them
     * @return $this
     */
    public function disableLockTable()
    {
        $this->options['lock-tables'] = 'false';
        return $this;
    }

    /**
     * do not add comments to dump file (same as --skip-comments option)
     * @return $this
     */
    public function withoutComments()
    {
        $this->options['skip-comments'] = null;
        return $this;
    }

    /**
     * do not add locks (same as --skip-add-locks option)
     * @return $this
     */
    public function withoutAddLock()
    {
        $this->options['skip-add-locks'] = null;
        return $this;
    }

    /**
     * do not write CREATE DATABASE statements (same as --no-create-db option)
     * @return $this
     */
    public function withoudCreateDB()
    {
        $this->options['no-create-db'] = null;
        return $this;
    }

    /**
     * do not write CREATE TABLE statements that re-create each dumped table (same as --no-create-info option)
     * @return $this
     */
    public function withoutCreateTable()
    {
        $this->options['no-create-info'] = null;
        return $this;
    }

    /**
     * do not dump table contents (same as --no-data option)
     * @return $this
     */
    public function withoutTableData()
    {
        $this->options['no-data'] = null;
        return $this;
    }

    /**
     * dump data without create info (same as --no-create-info option)
     */
    public function withoutCreateInfo()
    {
        $this->options['no-create-info'] = null;
        return $this;
    }

    /**
     * dump data without set charset (same as --skip-set-charset option)
     */
    public function withoutSetCharset()
    {
        $this->options['skip-set-charset'] = null;
        return $this;
    }

    /**
     * set mysqldump command option
     * @param string $option_name option name
     * @param string|null $value option value
     */
    public function setOption($option_name, $value = null)
    {
        $this->options[$option_name] = $value;
        return $this;
    }

    /**
     * start dump
     * @return string dump data
     * @throws MysqldumpException if dump command excute fail
     */
    public function dump()
    {
        $params = $this->generateExcuteCommandParams();
        return $this->executeDump("mysqldump", $params);
    }

    /**
     * excute mysqldump command and return dump data
     * @param  string mysqldump application path or command
     * @param  array  string of mysqldump options
     * @return string dump data
     * @throws MysqldumpException if dump command excute fail
     */
    protected function executeDump($command, array $params)
    {
        $process = new Process(array_merge([$command], $params));
        $process->start();
        $process->wait();
        $output = $process->getOutput();
        $outputError = $process->getErrorOutput();
        $outputError = trim(str_replace('mysqldump: [Warning] Using a password on the command line interface can be insecure.', '', $outputError));
        if (!empty($outputError)) {
            throw new MysqldumpException($outputError);
        }
        return $output;
    }

    /**
     * generate mysqldump command options
     * @return array array of option string
     */
    protected function generateExcuteCommandParams()
    {
        $params = [];
        foreach ($this->options as $option => $value) {
            if ($value !== null) {
                $params[] = "--" . $option . "=" . $value;
            } else {
                $params[] = "--" . $option;
            }
        }
        if (empty($this->database)) {
            $params[] = "--all-databases";
        } else {
            $params[] = $this->database;
        }
        foreach ($this->tables as $table_name => $where_condition) {
            $params[] = $table_name;
            if (!empty($where_condition)) {
                $params[] = "--where=" . $where_condition;
            }
        }
        return $params;
    }
}