<?php

class SampleAPI extends API {
    
    /**
     * DELETE
     * @return string
     */
    public function _delete() {
        
        $id = (int)$this->id;
        if (!DB::getInstance()->exist($id)) {
            return $this->response(array('error' => "Product not exist"), 404);
        }
        
        DB::getInstance()->delete($id);
        
        return $this->response(array('deleted' => 1));
    }
    
    /**
     * CREATE
     * @return string
     */
    public function _post() {
        
        $newProduct = array(
            'name' => ($this->name ? $this->name : ''),
            'category' => ($this->category ? $this->category : ''),
            'delivered' => false,
        );
        
        $newProduct['id'] = DB::getInstance()->add($newProduct);
        
        return $this->response($newProduct);
    }
    
    /**
     * GET LIST
     * @return string
     */
    public function _get() {
        return $this->response( DB::getInstance()->get() );
    }
    
    /**
     * UPDATE
     * @return string
     */
    public function _put() {
        
        $id = (int)$this->id;
        if (!DB::getInstance()->exist($id)) {
            return $this->response(array('error' => "Product not exist"), 404);
        }
        
        $newData = array(
            'name' => ($this->name ? $this->name : ''),
            'category' => ($this->category ? $this->category : ''),
            'delivered' => (bool)($this->delivered ? $this->delivered : false),
        );
        
        DB::getInstance()->change($id, $newData);
        
        $newData['id'] = $id;
        
        return $this->response($newData);
    }
    
}

