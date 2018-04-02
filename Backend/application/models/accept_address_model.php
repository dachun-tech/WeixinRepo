<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class accept_address_model extends CI_Model
{

    /**
    *This is function to add addresses where user can accept the goods paid
    */
    function addAddressByUser($info)
    {
        $this->db->insert("accept_address", $info);
        return true;
    }

    /**
    *This is function to get addresses where user can accept the goods paid
    */
    function getAddressByUser($userId)
    {
        $this->db->select("accept_address.name, accept_address.phone, accept_address.email, accept_address.no, accept_address.state, accept_address.detail_address");
        $this->db->select("provinces.province, cities.city, areas.area");
        $this->db->from("accept_address");
        $this->db->join("provinces","provinces.id = accept_address.province");
        $this->db->join("cities","cities.id = accept_address.city");
        $this->db->join("areas","areas.id = accept_address.area");
        $this->db->where("user_id", $userId);
        $query = $this->db->get();
        return $query->result();
    }

    /**
    *This is function to change addresses by address_id
    */
    function changeAddressById($addressId, $info)
    {
        $this->db->where("no", $addressId);
        $this->db->update("accept_address", $info);
        return true;
    }

    /**
    *This is function to mark address as actual address by id
    */
    function checkAddressById($addressId, $userId)
    {
        $info['state'] = 1;
        $this->db->where("no", $addressId);
        $this->db->update("accept_address", $info);
        $info['state'] = 0;
        $this->db->where_not_in("no", $addressId);
        $this->db->where("user_id", $userId);
        $this->db->update("accept_address", $info);
        return $this->db->affected_rows();
    }

    /**
    *This is function to delete address by id
    */
    function deleteAddressById($addressId, $userId)
    {
        $query = $this->db->query("select state from accept_address where no=".$addressId);
        $result = $query->result();
        $checked = $result[0]->state;
        $this->db->where("no", $addressId);
        $this->db->delete("accept_address");
        if($checked == 1){
            $this->db->query("update accept_address set state=1 where user_id=".$userId." limit 1");
        }
        $this->db->select("*");
        $this->db->from("accept_address");
        $this->db->where('user_id', $userId);
        $query = $this->db->get();
        return $query->result();
    }

    /**
    *This is function to get main receiver address by user_id
    */
    function getMainAddress($userId)
    {
        $this->db->select("name, phone, address");
        $this->db->from("accept_address");
        $this->db->where('user_id', $userId);
        $this->db->where('state', 1);
        $query = $this->db->get();
        return $query->result();
    }

}

/* End of file accept_address_model.php */
/* Location: .application/models/accept_address_model.php */
