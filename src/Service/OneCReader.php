<?php
namespace AlexNzr\BitUmcIntegration\Service;

use AlexNzr\BitUmcIntegration\Config\Variables;
use AlexNzr\BitUmcIntegration\Utils\Utils;
use Exception;

class OneCReader extends AbstractOneCService
{
    /** get all appointment data in json
     * @return string
     */
    public function getAppointmentData(): string
    {
        if (Variables::DEMO_MODE === "Y"){
            sleep(3);
            try {
                return json_encode($this->demoData);
            }catch (Exception $e){
                return Utils::addError("Demo mode error: " . $e->getMessage());
            }
        }

        $this->endpoint .= "GetAppointmentData";
        $period = Utils::getDateInterval(Variables::SCHEDULE_PERIOD_IN_DAYS);

        $data = json_decode($this->post($period), true);
        $result = [];
        $errors = '';
        if (is_array($data))
        {
            foreach ($data as $key => $item) {
                if (!empty($item["error"])){
                    $errors .= $item["error"]." | ";
                }
                else{
                    if ($key === 'schedule'){
                        $schedule = Utils::prepareScheduleData($item);
                        if (!empty($schedule['error'])){
                            $errors .= $schedule['error']." | ";
                        }
                        else{
                            $result[$key] = $schedule[$key];
                        }
                    }
                    else{
                        $result[$key] = $item[$key];
                    }
                }
            }
        }
        if (!strlen($errors) > 0){
            return json_encode($result);
        }
        else{
            return Utils::addError($errors);
        }
    }

    /** get list of clinics in json
     * @return string
     */
    public function getClinicsList(): string
    {
        if (Variables::DEMO_MODE === "Y"){
            sleep(3);
            try {
                return json_encode($this->demoData['clinics']);
            }catch (Exception $e){
                return Utils::addError("Demo mode error: " . $e->getMessage());
            }
        }

        $this->endpoint .= "GetListClinics";
        $data = json_decode($this->post(), true);
        if (empty($data["error"]) && is_array($data["clinics"]))
        {
            $data = $data["clinics"];
        }
        return json_encode($data);
    }

    /** get list of clients in json
     * @param array $params
     * @return string
     */
    public function getEmployeesList(array $params =[]): string
    {
        if (Variables::DEMO_MODE === "Y"){
            try {
                return json_encode($this->demoData['employees']);
            }catch (Exception $e){
                return Utils::addError("Demo mode error: " . $e->getMessage());
            }
        }

        $this->endpoint .= "GetListEmployees";
        $data = json_decode($this->post($params), true);
        if (empty($data["error"]) && is_array($data["employees"]))
        {
            $data = $data["employees"];
        }
        return json_encode($data);
    }

    /** get list of nomenclature in json
     * @param array $params
     * @return string
     */
    public function getNomenclatureList(array $params =[]): string
    {
        if (Variables::DEMO_MODE === "Y"){
            try {
                return json_encode($this->demoData['nomenclature']);
            }catch (Exception $e){
                return Utils::addError("Demo mode error: " . $e->getMessage());
            }
        }

        $this->endpoint .= "GetListNomenclature";
        $data = json_decode($this->post($params), true);
        if (empty($data["error"]) && is_array($data["nomenclature"]))
        {
            $data = $data["nomenclature"];
        }
        return json_encode($data);
    }

    /** get doctors schedule in json
     * @return string
     */
    public function getSchedule(): string
    {
        if (Variables::DEMO_MODE === "Y"){
            try {
                return json_encode(['schedule' => $this->demoData['schedule']]);
            }catch (Exception $e){
                return Utils::addError("Demo mode error: " . $e->getMessage());
            }
        }

        $this->endpoint .= "GetSchedule";

        $period = Utils::getDateInterval(Variables::SCHEDULE_PERIOD_IN_DAYS);

        $data = json_decode($this->post($period), true);
        $schedule = Utils::prepareScheduleData($data);
        if (empty($schedule['error'])){
            return json_encode($schedule);
        }
        else{
            return Utils::addError($schedule['error']);
        }
    }

    /** get list of clients in json
     * @param array $params
     * @return string
     */
    public function getClientsList(array $params = []): string
    {
        $this->endpoint .= "GetListClients";
        $data = json_decode($this->post($params), true);
        $clients = $data["clients"];
        if (empty($data["error"]) && is_array($clients))
        {
            foreach ($clients as $key => $client)
            {
                if (!empty($client["birthday"]))
                {
                    $clients[$key]["displayBirthday"] = date("d-m-Y", strtotime($client["birthday"]));
                }

                if (is_array($client["contacts"]))
                {
                    foreach($client['contacts'] as $contactType => $contactValue){
                        if ($contactType === "phone")
                        {
                            $clients[$key]["contacts"]["phone"] = Utils::formatPhone($contactValue);
                        }
                        else
                        {
                            $clients[$key]["contacts"][$contactType] = trim($contactValue);
                        }
                    }
                }

                foreach ($clients[$key] as $param => $value){
                    if (is_string($value)){
                        $clients[$key][$param] = trim($value);
                    }
                }
            }
            $data = $clients;
        }

        return json_encode($data);
    }

    /** get list of orders in json
     * @param array $params
     * @return string
     */
    public function getOrdersList(array $params): string
    {
        if (!empty($params["clientUid"])){
            $this->endpoint .= "GetListOrders";
            $data = json_decode($this->post($params), true);
            $orders = $data["orders"];
            if (empty($data["error"]) && is_array($orders))
            {
                foreach ($orders as $key => $order)
                {
                    if (!empty($order["orderDate"])){
                        $orders[$key]["displayOrderDate"] = date("d-m-Y", strtotime($order["orderDate"]));
                    }
                    if (!empty($order["timeBegin"])){
                        $orders[$key]["displayTimeBegin"] = date("H:i", strtotime($order["timeBegin"]));
                    }
                    if (!empty($order["timeEnd"])){
                        $orders[$key]["displayTimeEnd"] = date("H:i", strtotime($order["timeEnd"]));
                    }
                    if (!empty($order["clientBirthday"])){
                        $orders[$key]["displayClientBirthday"] = date("d-m-Y", strtotime($order["clientBirthday"]));
                    }

                    $data = $orders;
                }
            }

            return json_encode($data);
        }
        return Utils::addError('ClientUid is empty');
    }
}
