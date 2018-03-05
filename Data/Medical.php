<?php
namespace Data;

use \Core\API as API;

class Medical extends API
{
    protected $medical;
    protected $db;
    protected $start;
    protected $limit;
    function __construct($request)
    {
        $this->db = new \mysqli('localhost','root','','belajar');
        parent::__construct($request);
    }

    function medical()
    {
        if($this->method=='GET'){
            return $this->getMedical();
        }else if($this->method=='POST'){
            return $this->saveMedical();
        }else if($this->method=='PUT'){
            return $this->updateMedical();
        }else if($this->method=='DELETE'){
            return $this->removeMedical();
        }
    }

    function getMedical()
    {
        $start = (!empty($_GET['start'])) ? $_GET['start'] : 0;
        $limit = (!empty($_GET['limit'])) ? $_GET['limit'] : 10;

        $where = "";
        if(!empty($_GET['provider_id'])){
            if($where!==""){
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }
            $where .= "provider_id = '".$_GET['provider_id']."'";
        }

        if(!empty($_GET['provider_name'])){
            if($where!==""){
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }
            $where .= "provider_name LIKE '".$_GET['provider_name']."%'";
        }

        if(!empty($_GET['provider_street_address'])){
            if($where!==""){
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }
            $where .= "provider_street_address LIKE '%".$_GET['provider_street_address']."%'";
        }

        if(!empty($_GET['provider_city'])){
            if($where!==""){
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }
            $where .= "provider_city LIKE '%".$_GET['provider_city']."%'";
        }

        if(!empty($_GET['provider_zipcode'])){
            if($where!==""){
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }
            $where .= "provider_zipcode LIKE '%".$_GET['provider_zipcode']."%'";
        }

        if(!empty($_GET['average_covered_charge'])){
            if($where!==""){
                $where .= " AND ";
            } else {
                $where = " WHERE ";
            }
            $where .= "average_covered_charge = ".$_GET['average_covered_charge'];
        }

        if(!empty($this->args[0])){
            $where = "WHERE api_medical_id = ".$this->args[0];
        }

        
        $query = "SELECT * FROM api_medical $where LIMIT $start, $limit";
        $result = $this->db->query($query);

        $data = array();
        while($i = $result->fetch_assoc()){
            $data[] = $i;
        }

        $query = "SELECT COUNT(*) FROM api_medical $where";
        $result = $this->db->query($query);
        $count = $result->fetch_array();
        return array("data" => $data, "count" => $count[0]);
    }

    function saveMedical()
    {
        $data = json_decode(file_get_contents('php://input'), true);
             
        foreach($data as $key=>$value){
            $$key = $value;
        }

        $query = "INSERT INTO 
            api_medical (
                `drg_definition`, 
                `provider_id`,
                `provider_name`,
                `provider_street_address`,
                `provider_city`,
                `provider_state`,
                `provider_zipcode`,
                `average_covered_charge`) 
            VALUES (
                '".$drg_definition."',
                '".$provider_id."',
                '".$provider_name."',
                '".$provider_street_address."',
                '".$provider_city."',
                '".$provider_state."',
                '".$provider_zipcode."',
                ".$average_covered_charge.")";
        $result = $this->db->query($query);
        if($result===false){
            return array("success" => false, "error" => $this->db->error);
        }
        return array("success" => true);
    }

    function updateMedical()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $cols_updated = "";
        
        foreach($data as $key=>$value){
            $value = (is_numeric($value)) ? $value : "'".$value."'";
            $cols_updated .= $key."=".$value.",";
            $$key = $value;
        }
        $cols_updated = substr($cols_updated, 0, -1);
        $query = "UPDATE  
            api_medical SET
                $cols_updated
                WHERE api_medical_id = ".$api_medical_id;
        $result = $this->db->query($query);
        if($result===false){
            return array("success" => false, "error" => $this->db->error);
        }
        return array("success" => true);
    }

    function removeMedical()
    {
        $id = $this->args[0];

        $query = "DELETE FROM api_medical WHERE api_medical_id = ".$id;
        $result = $this->db->query($query);
        if($result===false){
            return array("success" => false, "error" => $this->db->error);
        }
        return array("success" => true);
    }
}

?>