<?php

namespace Fwc\Api\Type;


interface TypeInterface 
{   
    /**
     * HTTP Request GET
     * @param array $args
     */
    public function get(array $params): array;
    
    public function post(array $params): array;
    
    public function delete(string $id, $params): array;
    
    public function put(string $id, $params): array;
    
    public function createSqlTable($type = null);
}
