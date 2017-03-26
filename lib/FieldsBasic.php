<?php
namespace bxincludes;

class FieldsBasic {
    protected $_data;
    protected $_debug;
    protected $_link;
    function __construct($item, $debug = false, $link){
        $this->_data = $item;
        $this->_debug = $debug;
        $this->_link = $link?:"";
        if($this->_link){
            $this->_link = str_replace("#ID#", $this->_data['ID'], $this->_link);
            $this->_link = " onclick=\"".$this->_link."\"";
        }
    }

    protected function debug($data){
        if($this->_debug){

            return  "<span class='iop'".$this->_link.">".$data."</span>";
        } else
            return $data;
    }
    public function get(){
        return $this->getText();
    }

    public function getText(){
        $return = $this->_data['~PREVIEW_TEXT'];
        return $this->debug($return);
    }

    public function getFull(){
        $return = $this->_data['~DETAIL_TEXT']?:$this->_data['~PREVIEW_TEXT'];
        return $this->debug($return);
    }
    public function getImage($arr = false){
        if($arr){
            $resize = \CFile::ResizeImageGet($this->_data['PREVIEW_PICTURE'],$arr);
            return $resize['src'];
        } else
            return \CFile::GetFileArray($this->_data['PREVIEW_PICTURE']);;
    }
    public function getName(){
        $return = $this->_data['~NAME'];
        return $this->debug($return);
    }
}