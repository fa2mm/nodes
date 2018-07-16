Install via [Vagrant](https://www.vagrantup.com)
====================
1. Install Vagrant image
    ```bash
    $ vagrant up
    ```
2. Wait...
3. Launch application
    ```bash
    $ vagrant ssh
    
    $ cd /var/www/category
    
    $ ./node add [NODE_TITLE:mandatory] [NODE_PARENT_ID:optional]
    $ ./node remove [NODE_ID:mandatory]
    $ ./node up [NODE_ID:mandatory]
    $ ./node down [NODE_ID:mandatory]
    $ ./node rename [NODE_ID:mandatory] [NEW_NODE_TITLE:mandatory]
    ```
