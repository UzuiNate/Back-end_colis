<?php

class HomeController{
    public function ListColis() {
        return "Hello";
    }

    public function List() {
        $result = [
            [
                "id" => 1,
            ],
            [
                "id" => 1,
            ],
            [
                "id" => 1,
            ]
        ];

        return json_encode($result);
    }
}