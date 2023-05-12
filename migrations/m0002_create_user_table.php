<?php

use orion\core\Application;

class m0002_create_user_table{
    public function up(){
       return 
            "
            CREATE TABLE IF NOT EXISTS user  (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL
            );
            
            ";
        
       
        
    }

    public function down(){
        echo "Migration up m0002".PHP_EOL;
    }
}