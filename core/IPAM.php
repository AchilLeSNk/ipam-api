<?php

namespace core;

class IPAM
{

    public function __construct()
    {
        $this->readEnvironmentVar();
        $this->url = getenv('IPAM_URL');
        $this->login = getenv('IPAM_USER');
        $this->password = getenv('IPAM_PASSWORD');
        $this->app_id = getenv('IPAM_APP_ID');

        if (!getenv('IPAM_TOKEN_FILE')) {
            $this->token = $this->get_token();
        }
    }

    /**
     * Get ipam token
     *
     * @return mixed
     */
    public function get_token()
    {
        $curl = curl_init($this->url . "api/{$this->app_id}/user/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        if ($data->success) {
            return $data->data->token;
        }
    }

    /**
     * Send Request
     *
     * @param $method
     * @param $endpoint
     * @param bool $data
     * @return mixed
     */
    protected function call($method, $endpoint, $data = false)
    {
        $curl = curl_init($this->url . "api/{$this->app_id}/" . $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["token: " . $this->token]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    /**
     * Create New Subnet
     *
     * @param $data
     * @return mixed
     */
    public function createNewSubnet($data)
    {
        return $this->call("POST", "subnets", $data);
    }


    /**
     * Create New Address
     *
     * @param array $data
     * @return mixed
     */
    public function createNewAddress($data = array())
    {
        return $this->call("POST", "addresses", $data);
    }

    /**
     * Creates new address in subnets – first available (subnetId can be provided with parameters)
     *
     * @param $subnetid
     * @return mixed
     */
    public function createNewFirstAddress($subnetid)
    {
        return $this->call("POST", "addresses/first_free", $subnetid);
    }

    /**
     * Create new VLAN
     *
     * @param array $data
     * @return mixed
     */
    public function createNewVLAN($data = array())
    {
        return $this->call("POST", "vlan", $data);
    }

    /**
     * Get subnet by cidr
     *
     * @param $cidr
     * @return mixed
     */
    public function getSubnetByCidr($cidr)
    {
        return $this->call("GET", "subnets/search/$cidr");
    }

    /**
     * Get subnet by id
     *
     * @param $id
     * @return mixed
     */
    public function getSubnetById($id)
    {
        return $this->call("GET", "subnets/$id")->data;
    }

    /**
     * Get subnet slaves
     *
     * @param $id
     * @return mixed
     */
    public function getSubnetSlaves($id)
    {
        return $this->call("GET", "subnets/$id/slaves")->data;
    }

    /**
     * Get IP address
     *
     * @param $ip
     * @return mixed
     */
    public function getAddress($ip)
    {
        return $this->call("GET", "addresses/search/$ip")->data['0'];
    }

    /**
     * Get VLAN list
     *
     * @return mixed
     */
    public function getListVLAN()
    {
        return $this->call("GET", "vlan");
    }

    /**
     * get VLAN
     *
     * @param $id
     * @return mixed
     */
    public function getVLAN($id)
    {
        return $this->call("GET", "vlan/$id")->data;
    }


    // PATCH
    /**
     * Update VLAN
     *
     * @param $data
     * @return mixed
     */
    public function updateVLAN($data)
    {
        return $this->call("PATCH", "vlan", $data);
    }

    /**
     * Update Address
     *
     * @param $id - Address ID
     * @param $data
     * @return mixed
     */
    public function updateAddress($id, $data)
    {
        return $this->call("PATCH", "addresses/$id", $data);
    }

    /**
     * Delete Subnet
     *
     * @param $subnetid
     * @return mixed
     */
    public function deleteSubnet($subnetid)
    {
        return $this->call("DELETE", "subnets/$subnetid");
    }

    /**
     * Delete address in subnet
     *
     * @param $ip
     * @param $subnetid
     * @return mixed
     */
    public function deleteAddressInSubnet($ip, $subnetid)
    {
        return $this->call("DELETE", "addresses/$ip/$subnetid");
    }

    /**
     * Delete VLAN
     *
     * @param $id
     * @return mixed
     */
    public function deleteVLAN($id)
    {
        return $this->call("DELETE", "vlan/$id");
    }

    /**
     * Get subnet id by cidr
     *
     * @param $cidr
     * @return mixed
     */
    public function getSubnetIdByCidr($cidr)
    {
        return $this->getSubnetInCidrFormat($cidr)['0']->id;
    }

    /**
     * Set octet in ip address
     *
     * @param $ip
     * @param $pos
     * @param $override
     * @return array|string
     */
    public function setOctet($ip, $pos, $override)
    {
        $network_name = explode('.', $ip);
        $network_name[$pos - 1] = $override;
        $network_name = implode('.', $network_name);
        return $network_name;
    }

    /**
     * Read environment variables
     *
     * @return void
     */
    private function readEnvironmentVar(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
        $dotenv->load();
    }
}
