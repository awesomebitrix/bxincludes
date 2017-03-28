<?php

namespace bxincludes;

class Fields extends FieldsBasic{
    public function getTitle(){
        $return = $this->_data['~PROPERTY_TITLE_VALUE']?:$this->_data['~NAME'];
        return $this->debug($return);
    }

    public function getLink(){
        $return = $this->_data['~PROPERTY_LINK_VALUE'];
        return $this->debug($return);
    }
}