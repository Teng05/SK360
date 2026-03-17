<?php

class database {

    function opencon() {
        return new PDO(
            'mysql:host=localhost;
            dbname=sk360', 
            username: 'root',
            password: ''
        );
    }
}