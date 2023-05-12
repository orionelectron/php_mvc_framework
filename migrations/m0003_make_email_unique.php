<?php

class m0003_make_email_unique{
    public function up(){
        return "
        ALTER TABLE user
        ADD CONSTRAINT email_unique UNIQUE (email);
        ";
    }
    public function down(){
        echo "Migration up m0003".PHP_EOL;
    }
}