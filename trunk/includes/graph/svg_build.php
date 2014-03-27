<?php 
class Svg_Buider
{ 
    public $file;
    public $region_contur;
    public $image;
    
    public function __construct($f) 
    {
        $this->file = $f;
        $this->region_contur = new DOMdocument();
        $this->region_contur->load($this->file);
    }
    
    public function render() 
    {
        $this->image = $this->region_contur->saveXML();
    }

}

?>