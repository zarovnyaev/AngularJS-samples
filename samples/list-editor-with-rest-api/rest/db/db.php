<?php

class DB {

    protected $dbPath = __DIR__ . '/db.txt';
    protected static $instance = null;

    public static function &getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    protected $data = null;
    
    public function get() {
        if ($this->data === null) {
            $this->data = (array)json_decode(
                file_get_contents($this->dbPath), 
                true
            );
        }
        return $this->data;
    }

    public function set($data) {
        $this->data = $data;
    }
    
    public function save() {
        file_put_contents($this->dbPath, json_encode($this->data));
    }
    
    public function add($newElement) {
        $newElement['id'] = $this->getNextID();
        $data = $this->get();
        $data[] = $newElement;
        $this->set($data);
        $this->save();
        return $newElement['id'];
    }
    
    public function getNextID() {
        $max = 0;
        foreach ($this->get() as $element) {
            if ($element['id'] > $max) {
                $max = $element['id'];
            }
        }
        return $max + 1;
    }
    
    public function exist($id) {
        return ($this->getRow($id) !== null);
    }
    
    public function getRow($id) {
        foreach ($this->get() as $element) {
            if ($element['id'] == $id) {
                return $element;
            }
        }
        return null;
    }
    
    public function change($id, $newData) {
        $elements = $this->get();
        foreach ($elements as &$element) {
            if ($element['id'] == $id) {
                $element = array_merge($element, $newData);
                break;
            }
        }
        $this->set($elements);
        $this->save();
    }
    
    public function delete($id) {
        $elements = $this->get();
        foreach ($elements as $key => $element) {
            if ($element['id'] == $id) {
                array_splice($elements, $key, 1);
                break;
            }
        }
        $this->set($elements);
        $this->save();
    }
}