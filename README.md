# mysqldump-php
use dumpmysql command as a php class

Usage:

```php
<?php

use comoco\Mysqldump\Mysqldump;

$mysqldump = new Mysqldump;
$content = $mysqldump->setHost('localhost')
    ->setPort(3306)
    ->setUser('username')
    ->setPassword('password')
    ->setDatabase('database')
    ->addTable('table1', 'id < 2')
    ->addTable('table2', 'id > 6')
    ->disableExtendedInsert()
    ->disableLockTable()
    ->hexBlob()
    ->completeInsert()
    ->withoutComments()
    ->withoutAddLock()
    ->setGtidPurged('OFF')
    ->dump();
```



