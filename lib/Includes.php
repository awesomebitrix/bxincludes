<?php

namespace bxincludes;

if(BX_INCLUDES_CLASS_PATH)
    require BX_INCLUDES_CLASS_PATH;
else
    require __DIR__.'/BXIncludeFieldsExample.php';

class Includes{
    private $_data = null;
    private $_link = false;

    function __construct($section_code=false, $debug= false){
        if(empty(BX_INCLUDES_IBLOCK_CODE)){
            throw new \Exception('Не указан код инфоблока');
        }
        if(BX_INCLUDES_EDITABLE){
            global $USER;
            if ($USER->IsAdmin() && $this->checkIncludeAreaBack()) {
                $debug = true;
            } else {
                $debug = false;
            }
        }
        $obCache = new \CPHPCache();
        if ($section_code) {
            $section_str = is_array($section_code) ? implode(',', $section_code) : $section_code;
        }
        if ($obCache->InitCache($debug?0:86400, md5("includes_".($section_str?:"")), '/')) {
            $data = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            \CModule::IncludeModule("iblock");
            $properties = [];
            $req = \CIBlockProperty::GetList(["sort"=>"asc"], ["ACTIVE"=>"Y", "IBLOCK_CODE"=>BX_INCLUDES_IBLOCK_CODE]);
            while ($prop_fields = $req->GetNext())
            {
                $properties[] = "PROPERTY_".$prop_fields['CODE'];
            }

            $arSelect = ["ID", "IBLOCK_ID", "NAME","PREVIEW_PICTURE","DETAIL_TEXT","PREVIEW_TEXT", "CODE"];
            $arSelect = array_merge($arSelect,$properties);
            $arFilter = ["IBLOCK_CODE"=>BX_INCLUDES_IBLOCK_CODE, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"];
            if($section_code)
                $arFilter['SECTION_CODE'] = $section_code;
            else
                $arFilter['SECTION_CODE'] = false;

            $res = \CIBlockElement::GetList([], $arFilter, false, ["nPageSize"=>50], $arSelect);
            while($arFields = $res->GetNext()){
                $data[$arFields['CODE']] = $arFields;
            }
            $obCache->EndDataCache($data);
        }
        $this->_data = $data;

        if($debug){
            echo "<style>.iop{outline: 1px solid rgba(51,255,0,0.51);display:inline-block;transition: .5s;}
.iop:hover{outline: 1px solid rgba(51,255,0,1);box-shadow: 0 0 10px 0px rgb(51,255,0);cursor: pointer;}</style>";
            $res = \CIBlock::GetList([], ['CODE'=>BX_INCLUDES_IBLOCK_CODE]);
            if($ar_res = $res->Fetch())
            {
                $this->_link = "(new BX.CAdminDialog({'content_url':'/bitrix/admin/iblock_element_edit.php?type=".
                    $ar_res['IBLOCK_TYPE_ID'].
                    "&IBLOCK_ID=".$ar_res["ID"]."&ID=#ID#&lang=ru&bxpublic=Y&from_module=ytu&siteTemplateId=main'".
                    ",'width':'700','height':'400'})).Show()";
            }
        }


        foreach ($this->_data as $code=>$item){
            $this->$code = new Fields($item, $debug, $this->_link);
        }


        return $this;
    }

    public function getAll(){
        return $this->_data;
    }

    private function checkIncludeAreaBack()
    {
        session_start();
        return $_SESSION['SESS_INCLUDE_AREAS'] || $_GET['bitrix_include_areas'] == "Y";
    }
}