<?php

namespace Modules\PluginManage\Http\Helpers;

use Illuminate\Support\Facades\Cache;

class PluginJsonFileHelper
{
    //todo only work with single json file

    protected array|string|object|null $fileContents = null;  //put null (or another default) cz PHP forces to initialize typed properties before using
    // protected array|string|object $fileContents;  -> old varsion
    protected string $pluginDirName;
    protected object $moduleList;

    public function __construct($pluginDirName)
    {
        $this->pluginDirName = $pluginDirName;
        $this->setModuleLists();
        if ($this->checkModuleFileExists()){
            $this->setFileContent();
        }
    }

    public function metaInfo(){
        $data = $this->getFileContent();

        if ($data === null) {
            return null;
        }

        $metaObject  = new \stdClass;
        $metaObject->name = $this->deliciousCamelcase($data->name);
        $metaObject->alias = $data->alias;
        $metaObject->description = $data->description;
        $metaObject->version = property_exists($data,"version") ? $data->version : "1.0.0";
        $metaObject->category = property_exists($data,"nazmartMetaData") ? __("External Plugin") : __("Core Plugin");

        $metaObject->external = false;
        if (property_exists($data,"nazmartMetaData") && !empty($data->nazmartMetaData))
        {
            if (property_exists($data->nazmartMetaData,"plugin_type") && !empty($data->nazmartMetaData->plugin_type))
                $metaObject->external = (bool)$data->nazmartMetaData->plugin_type;
        }

        $metaObject->status = $this->isPluginActive(); //check status using a private method;

        // Determine management scope from admin_settings
        $showLandlord = false;
        $showTenant   = false;
        if (property_exists($data, 'nazmartMetaData') && !empty($data->nazmartMetaData)) {
            $adminSettings = $data->nazmartMetaData->admin_settings ?? null;
            if ($adminSettings) {
                $showLandlord = (bool)($adminSettings->show_admin_landlord ?? false);
                $showTenant   = (bool)($adminSettings->show_admin_tenant ?? false);
            }
        }
        if ($showLandlord && $showTenant) {
            $metaObject->scope = 'both';
        } elseif ($showLandlord) {
            $metaObject->scope = 'landlord';
        } elseif ($showTenant) {
            $metaObject->scope = 'tenant';
        } else {
            $metaObject->scope = null;
        }

        return $metaObject;
    }

    public function isPluginActive(){
        $moduleList = json_decode(file_get_contents(base_path("modules_statuses.json")),true);
        return array_key_exists($this->pluginDirName,$moduleList) && $moduleList["$this->pluginDirName"];
    }
    public function deliciousCamelcase($str)
    {
        $formattedStr = '';
        $re = '/
          (?<=[a-z])
          (?=[A-Z])
        | (?<=[A-Z])
          (?=[A-Z][a-z])
        /x';
        $a = preg_split($re, $str);
        $formattedStr = implode(' ', $a);
        return $formattedStr;
    }
    public function overrideData(array $data){
        $existingData  = $this->fileContents;
        foreach($data as $col => $value){
            if (property_exists($existingData,$col)){
                $existingData->$col = $value;
            }
        }
        $this->fileContents = $existingData;
        return $this;
    }
    public function saveFile(){
        file_put_contents($this->getModuleMetaFilePath(),$this->fileContents);
    }
    private function getJsonData(){

    }
    private function decodeData(){
        return json_decode($this->fileContents);
    }
    private function encodeData(){
        return json_encode($this->fileContents);
    }
    private function pluginName(){
        return $this->pluginDirName;
    }

    private function setFileContent(){
        $path = $this->getModuleMetaFilePath();
        if (! file_exists($path)) {
            return;
        }
        return $this->fileContents = json_decode(file_get_contents($path));
    }

    private function getFileContent()
    {
        if ($this->fileContents === null) {
            $this->setFileContent();
        } // put default value

        return $this->fileContents;
    }

    private function getModuleMetaFilePath()
    {
        return base_path('Modules/' . implode('', explode(' ', $this->pluginDirName))) . '/module.json';
    }

    private function checkModuleFileExists()
    {
        $pluginDirName = $this->pluginDirName;
        // check the folder is exists otherwise return false
        if(!is_dir(base_path('/Modules/'.$pluginDirName))){
            return false;
        }

        // return if it's status is fales in the module moduleList
        if(is_object($this->moduleList) && property_exists($this->moduleList,$pluginDirName)){
            return $this->moduleList->$pluginDirName;
        }elseif(is_array($this->moduleList) && isset($this->moduleList[$pluginDirName])){
            return $this->moduleList[$pluginDirName];
        }
        return file_exists($this->getModuleMetaFilePath()) && !is_dir($this->getModuleMetaFilePath());
    }
    public function saveModuleListFile(){
        file_put_contents(base_path("modules_statuses.json"),json_encode($this->moduleList));
    }
    private function setModuleLists(){
        $this->moduleList = Cache::remember("allModuleStatus",3600,function (){
            return json_decode(file_get_contents(base_path("modules_statuses.json")));
        });

        return $this;
    }
    public function changePluginStatus($status){
        Cache::forget("allModuleStatus");
        $pluginName = $this->pluginDirName;
//        $moduleList = $this->moduleList;
//        if (property_exists($moduleList,$pluginName)){
        $this->moduleList->$pluginName = $status;
//        }
        return $this;
    }
    public function removePlugin(){
        Cache::forever("allModuleStatus");
        $pluginName = $this->pluginDirName;
        $moduleList = $this->moduleList;
        if (property_exists($moduleList,$pluginName)){
            unset($this->moduleList->$pluginName);
        }
        return $this;
    }
}
