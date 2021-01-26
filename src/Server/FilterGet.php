<?php

namespace Plinct\Api\Server;

class FilterGet 
{   
    // properties not exists
    private $noWhere = [ "orderBy", "ordering", "limit", "groupBy", "offset", "id", "properties", "where", "format", "count", "fields", "tableHasPart", "idHasPart" ];
        
    // conditions sql
    private $fields = "*";
    private $where;
    private $groupBy;
    private $orderBy;
    private $limit;
    private $offset;

    private $table;
    
    private $properties;
            
    public function __construct($queryParams, $table, $properties) 
    {        
        $this->table = $table;
        $this->properties = $properties;
        
        if (!empty($queryParams)) {
            $this->setQueries($queryParams);
        }
    }
    
    private function setQueries($queryParams)
    {   
        // fieds
        $this->fields = $queryParams['fields'] ?? $this->fields;
                
        // query params        
        foreach ($queryParams as $key => $value) {
            
            $idname = "id".$this->table;
            if ($value == "id") {
                $value = $idname;
            }            
            
            // ORDER BY
            if (stripos($key, "orderBy") !== false) {
                $ordering = $queryParams['ordering'] ?? 'ASC';
                $this->orderBy = stripos($ordering, 'rand') !== false ? "rand()" : $value." ". $ordering;
            }
            
            // WHERE
              // like
            $like = stristr($key,"like", true) !== false ? stristr($key,"like", true) : ($key == "q" || $key == "search" ? "name" : null);
            if ($like) {
                $whereArray[] = "`$like` LIKE '%$value%'";

            }
            //
            elseif (!in_array($key, $this->noWhere)) {
                if (strpos($value, "|") !== false) {
                    $whereArray[] = "(`$key`='".str_replace("|","' OR `$key`='",addslashes($value))."')";
                } else {
                    $whereArray[] = "`$key`='".addslashes($value)."'";
                }
            }
            
            if ($key == "id") {
                $whereArray[] = "`$idname`=$value";
            }
            
            if (stripos($key, "where") !== false) {
                $whereArray[] = "$value";
            }
        }
        
        // WHERE
        $this->where = isset($whereArray) ? implode(" AND ", $whereArray) : null;
        
        // groupBy
        $this->groupBy = $queryParams['groupBy'] ?? null;
                
        // limit
        $this->limit = isset($queryParams['limit']) ? ($queryParams['limit'] !== "none" ? $queryParams['limit'] : null) : $this->limit; 
        
        // offset        
        $this->offset = $queryParams['offset'] ?? null;
        
        // properties
        if (isset($queryParams['properties'])) {
            $this->propertiesMerge($queryParams['properties']);
        }
    }
    
    /*public function field(): string
    {
        return $this->fields;
    }*/

    public function where()
    {
        return $this->where;
    }
    
    /*public function groupBy()
    {
        return $this->groupBy;
    }*/
    
    public function orderBy() 
    {
        return $this->orderBy;
    }
    
    /*public function ordering(): string
    {
        return stripos($this->ordering,'asc') !== false ? "Ascending" : 
                ( stripos($this->ordering,"desc") !== false ? "Descending" : 
                (stripos($this->ordering, 'rand') !== false ? "Randomly" : "Unordering") );     
    }*/
    
    public function limit(): ?int
    {    
        return $this->limit;
    }
/*
    public function offset() {
        return $this->offset;
    }*/
    
    public function getProperties(): ?array
    {
        return $this->properties;
    }
    
    private function propertiesMerge(string $propertiesIncrement) 
    {
        $propArray = explode(",", $propertiesIncrement);
        foreach ($propArray as $value) {
            $array[] = trim($value);
        }

        $this->properties = $array ? array_merge($this->properties, $array) : $this->properties;
    }

    public function getSqlStatement(): string
    {
        return "SELECT $this->fields FROM $this->table" . $this->stmtWhere() . $this->stmtGroupBy() . $this->stmtOrderBy(). $this->stmtLimit() . $this->stmtOffset();
    }

    private function stmtWhere(): ?string {
        return $this->where ? " WHERE $this->where" : null;
    }

    private function stmtGroupBy(): ?string {
        return $this->groupBy ? " GROUP BY $this->groupBy" : null;
    }

    private function stmtOrderBy(): ?string {
        return $this->orderBy ? " ORDER BY $this->orderBy" : null;
    }

    private function stmtLimit(): ?string {
        return $this->limit ? " LIMIT $this->limit" : null;
    }

    private function stmtOffset(): ?string {
        return $this->offset ? " OFFSET $this->offset" : null;
    }


}
