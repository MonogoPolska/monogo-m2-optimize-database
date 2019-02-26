#### Magento 2 module for optimization of database fragmentation.

# **Install**

### Git
- Locate the **/app/code** directory which should be under the magento root installation.
- If the **code** folder is not there, create it.
- Create a folder **Monogo** inside the **code** folder. 
- Change to the **Monogo** folder and clone the Git repository (https://github.com/MonogoPolska/monogo-m2-optimize-database.git) into **Monogo** specifying the local repository folder to be **OptimizeDatabase** 
e.g. 

``` git clone https://github.com/MonogoPolska/monogo-m2-optimize-database.git OptimizeDatabase```

### Composer
```composer require monogo/optimizedatabase```

### Magento Setup
- Run Magento commands

```php bin/magento setup:upgrade```

```php bin/magento setup:di:compile```

```php bin/magento setup:static-content:deploy```

# **App Configuration Options**

Go to Stores->Configuration->Monogo->Optimize database

- Enable module **Default value is 1 (Yes)**
- Minimal fragmentation ratio - Optimize tables for which fragmentation ratio is higher than this value. **Default value is 1**
- Use Magento Cron - You can disable Magento cron and run Optimization from shell **Default value is 0 (No)**
- Cron schedule - Use Crontab Format (Eg. "05 1 * * *" every day at 01:05)


### Default values:

# **Shell**

```
Usage:  php bin/magento monogo:optimize:database [--mode MODE]
   
     print                             Print all tables
     optimize                          Optimize tables       
     help                              This help
```

for example:

```php bin/magento monogo:optimize:database --mode print```

Possible output:

```
Module is disabled in Stores->Configuration->Monogo->Optimize database
```