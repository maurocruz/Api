<?php

namespace Fwc\Api\Type;


interface TypeInterface 
{   
    /**
     * HTTP Request GET
     * @param array $args
     */
    public function get(): array;
    
    public function post(array $params): array;
    
    public function delete(string $id): array;
    
    public function put(string $id, $params = null): array;
    
    public function createSqlTable($type = null);
}
