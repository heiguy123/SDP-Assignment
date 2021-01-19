<?php

use function PHPUnit\Framework\isEmpty;

class Customer
{
    private $id;
    private $num;
    /**
     * Static instance of self
     *
     * @var Customer
     */
    protected static $_instance;



    function __construct($id = null, $db = null, $num = null)
    {
        // $this->db          = $db;
        require_once('MysqliDb.php');
        $this->$id = $id;
        $this->$num = $num;
        // $this->db          = new MysqliDb("db4free.net", "pocketmoney", "m&nsuperdry", "pocketmoney", "3306"); //temporary
        $this->db          = new MysqliDb("localhost", "root", "", "pocketmoney", "3308"); //temporary
        //$this->validation  = $validation;
        self::$_instance = $this;
    }

    /**
     * A method of returning the static instance to allow access to the
     * instantiated object from within another class.
     * Inheriting this class would require reloading connection info.
     *
     * @uses $customer = Customer::getInstance();
     *
     * @return Customer Returns the current instance.
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /** 
     * Set customer id
     * 
     */
    function setId($id)
    {
        $this->id = strval($id);
    }

    /** 
     * Return the id if the id is set before
     * @return String|NULL
     * 
     */
    function getId()
    {
        if (!empty($this->id)) {
            return strval($this->id);
        }

        return NULL;
    }



    /** 
     * Return the array of table data if the id is set before, by default the cusID is put in the where clause
     * @param String $tablename
     * the name of the table
     * 
     * @param String $columnName
     * the name of the specific column
     * 
     * @param Array $whereAnd
     * '<whereProp>'=>'<whereValue>';
     * 
     * @param Array|String $orderBy
     * '<orderProp>' => '<order>','asc' or 'desc'; 
     * 
     * @param String $groupBy
     * group by clause
     * 
     * @return array|NULL
     * 
     */
    function getData($tablename, $columnName = "*", $whereAnd = NULL, $orderby = NULL, $groupBy = NULL)
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            if (!is_null($whereAnd)) { //if the groupBy clause is not null
                foreach ($whereAnd as $prop => $value) {
                    $db->where($prop, $value);
                }
            }
            if (!is_null($orderby)) {
                foreach ($orderby as $prop => $order) {
                    $db->orderBy($prop, $order);
                }
            }
            if (!is_null($groupBy)) { //if the groupBy clause is not null
                $db->groupBy($groupBy);
            }
            $result = $db->get($tablename, null, $columnName);
            return $result;
        }
        return NULL;
    }


    /** 
     * Return the array of table data if the id is set before
     * * @param String $query
     * select query statement
     * 
     * @return array|NULL
     * 
     */
    function getDataByQuery($query)
    {
        $db = MysqliDb::getInstance();
        $data = $db->rawQuery($query);
        return $data;
    }

    /**
     *
     * @param array $params
     * 'tableName'-> String
     * 'idName'-> String: id name;
     * 'id'-> String:id of the record;
     * 'data'-> array: column name TO actual data;
     * 
     * 
     * @return array 
     * 'status'-> String: 'ok' or 'error';
     * 'statusMsg'-> String: the status msg;
     *
     */
    public function customerUpdate($params)
    {
        $db = MysqliDb::getInstance();
        $tablename = $params['tableName'];
        $idName = $params['idName'];
        $id = $params['id'];
        $data = $params['data'];

        if ($id == "") {
            return array('status' => 'error', 'statusMsg' => 'data id is not defined');
        }

        if ($idName == "") {
            return array('status' => 'error', 'statusMsg' => 'data property is not defined');
        }

        $db->where($idName, $id);

        if ($db->update($tablename, $data)) {
            return array('status' => 'ok', 'statusMsg' => $db->count . ' records were updated');
        } else {
            return array('status' => 'error', 'statusMsg' => 'update failed: ' . $db->getLastError());
        }
    }

    /**
     *
     * @param array $params
     * 'tableName'-> String
     * 'data'-> array: column name TO actual data;
     * 
     * 
     * @return array 
     * 'status'-> String: 'ok' or 'error';
     * 'statusMsg'-> String: the status msg;
     *
     */
    public function customerInsert($params)
    {
        $db = MysqliDb::getInstance();
        $tablename = $params['tableName'];
        $data = $params['data'];

        $id = $db->insert($tablename, $data);
        if ($id)
            return array('status' => 'ok', 'statusMsg' => 'Record is added.');
        else
            return array('status' => 'error', 'statusMsg' => 'Add failed: ' . $db->getLastError());
    }

    /**
     *
     * @param array $params
     * 'tableName'-> String
     * 'idName'-> String: id name;
     * 'id'-> String:id of the record;
     * 
     * 
     * @return array 
     * 'status'-> String: 'ok' or 'error';
     * 'statusMsg'-> String: the status msg;
     *
     */
    public function customerDelete($params)
    {
        $db = MysqliDb::getInstance();
        $tablename = $params['tableName'];
        $idName = $params['idName'];
        $id = $params['id'];

        if ($id == "") {
            return array('status' => 'error', 'statusMsg' => 'data id is not defined');
        }

        if ($idName == "") {
            return array('status' => 'error', 'statusMsg' => 'data property is not defined');
        }

        $db->where($idName, $id);
        if ($db->delete($tablename)) {
            return array('status' => 'ok', 'statusMsg' => 'Deleted successfully.');
        } else {
            return array('status' => 'error', 'statusMsg' => 'Delete failed');
        }
    }

    /**
     *go to the page using js
     * 
     * @param String $url
     * 
     * 
     *
     */
    public function goTo($url)
    {
        echo ('<script>
            window.location.href = "' . $url . '"
            </script>');
    }

    /**
     * show alert box
     *
     * @param String $msg
     * the msg to show in alert box
     * 
     */
    public function showAlert($msg)
    {
        echo ('<script>alert("' . $msg . '");
        </script>');
    }


    /**
     * Login via email/username and password. Session and cookie are not set.
     *
     * @param array $params
     * 'loginInfo'-> String: username or email of the customer;
     * 'password'-> String: password of the customer;
     * 
     * @return array 
     * 'status'-> String: 'ok' or 'error';
     * 'statusMsg'-> String: the status msg;
     * 'data'-> array: array containing the customer data
     *
     */
    public function customerLogin($params)
    {
        $db = MysqliDb::getInstance();

        $loginInfo = $params['loginInfo'];
        $password = $params['password'];

        $db->where('username', $loginInfo);
        $db->orWhere('email', $loginInfo);

        $userData = $db->getOne('Customer');

        if (empty($userData)) { //if the $userData return nothing meaning wrong email or username
            return array('status' => 'error', 'statusMsg' => 'Wrong Username or Email', 'data' => '');
        }

        $correctPassword = password_verify($password, $userData['password']);
        if ($correctPassword == false) {
            return array('status' => 'error', 'statusMsg' => 'Wrong Password', 'data' => '');
        }

        return array('status' => 'ok', 'statusMsg' => '', 'data' => $userData);
    }

    /**
     * Check if the session / cookie is set
     * 
     * @return boolean
     * True if the session/ cookie is set
     *
     */
    public function havesession()
    {
        $db = MysqliDb::getInstance();
        session_start();

        if (!isset($_SESSION['customerData'])) // if no session
        {

            //check for cookie
            if (empty($_COOKIE['customer_email']) || empty($_COOKIE['customer_password'])) {

                //if cookie is empty return false
                return false;
            } else {
                //if cookie is not empty, check if the cookie is valid,return true
                $params = array('loginInfo' => $_COOKIE['customer_email'], 'password' => $_COOKIE['customer_password']);
                $result = $this->customerLogin($params);
                if ($result['status'] == 'error') { //cannot login
                    return false;
                }
                //successfully login
                $this->setSession('customerData', $result['data']);
                return true;
            }
        }
        //if have session, return true
        return true;
    }

    public function checksession()
    {
        if ($this->havesession() == false) {
            echo '<script>
        alert("Please login!");
        window.location.href="login.php?role=customer";//if the cookie or session is empty, go to login
        </script>';
        }
    }

    /**
     * Login via email/username and password. Session and cookie are not set.
     *
     * @param String $sessionName
     * name of the session
     * 
     * @param array $userData
     * data array of the user;
     * 
     * @return void
     */
    public function setSession($sessionName, $userData)
    {
        session_start();
        $_SESSION[$sessionName] = $userData;
        session_write_close();
    }

    /*************************************************************************\
     *                   Below Part are show investment page                  *
     *                           For extra functions                          *
     *                                                                        *
     *    **********   **     **  **           **  ***********   **********   *
     *        **       ***    **   **         **   **            **           *
     *        **       ****   **    **       **    **            **           *
     *        **       ** **  **     **     **     ***********   **           *
     *        **       **  ** **      **   **      **            **********   *
     *        **       **   ****       ** **       **                    **   *
     *        **       **    ***        ***        **                    **   *
     *    **********   **     **         *         ***********   **********   *
     *                                                                        *
     *************************************************************************/

    /** 
     * Return the total invested amount if the id is set before
     * @return int|NULL
     * 
     */
    function getTotalInvestedAmount()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $result = $db->getOne('Investment', "SUM(amountInvested) AS SUM");
            return intval($result['SUM']);
        }
        return NULL;
    }

    /** 
     * Return the top holding name if the id is set before
     * @return String|NULL
     * 
     */
    function getTopHolding()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $db->orderBy("total", "Desc");
            $db->groupBy("investmentName");
            $result = $db->getOne('Investment', "SUM(amountInvested) AS total, investmentName");
            return strval($result['investmentName']);
        }
        return NULL;
    }

    /** 
     * Return the total count of holding if the id is set before
     * @return int|NULL
     * 
     */
    function getHoldingCount()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $result = $db->getOne('Investment', "COUNT(DISTINCT investmentName) AS count");
            return intval($result['count']);
        }
        return NULL;
    }

    /** 
     * Return the array of distinct investment NAME and amount invested if the id is set before
     * @return array|NULL
     * 'investmentName' -> array: array of investmentName
     * 'amount' -> array: an array
     */
    function getInvestNameAndAmount()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $db->groupBy('investmentName');
            $investmentNameToValue = array('investmentName' => array(), 'amount' => array());
            $result = $db->get('Investment', null, "SUM(amountInvested) AS amount, investmentName");
            foreach ($result as $row => $data) {
                array_push($investmentNameToValue['amount'], $data['amount']);
                array_push($investmentNameToValue['investmentName'], $data['investmentName']);
            }
            return $investmentNameToValue;
        }

        return NULL;
    }


    /** 
     * Return JSON format of investment Name only
     * @return JSON|NULL
     * 
     * 
     */
    function getInvestNameJSON()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $data = $this->getInvestNameAndAmount();
            $investNameArr = $data['investmentName'];
            $investNameJSON = json_encode($investNameArr);
            return $investNameJSON;
        }
        return NULL;
    }

    /** 
     * Return JSON format of amount(investment Name) only
     * @return JSON |NULL
     * 
     * 
     */
    function getNameAmountsJSON()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $data = $this->getInvestNameAndAmount();
            $investAmountArr = array_map('floatval', $data['amount']);
            $investAmountJSON = json_encode($investAmountArr);
            return $investAmountJSON;
        }
        return NULL;
    }



    /** 
     * Return the array of distinct investment types and amount invested if the id is set before
     * @return array|NULL
     * 'investmentType' -> array: array of investmentTypes
     * 'amount' -> array: an array
     */
    function getInvestTypesAndAmount()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $db->groupBy('investmentType');
            $investmentTypesToValue = array('investmentType' => array(), 'amount' => array());
            $result = $db->get('Investment', null, "SUM(amountInvested) AS amount, investmentType");
            foreach ($result as $row => $data) {
                array_push($investmentTypesToValue['amount'], $data['amount']);
                array_push($investmentTypesToValue['investmentType'], $data['investmentType']);
            }
            //print_r($investmentTypesToValue['investmentType']);
            return $investmentTypesToValue;
        }

        return NULL;
    }

    /** 
     * Return ONE row of inventment table data for the given investment ID
     * @param String $columnName
     * the name of the specific column
     * 
     * @param String $investmentID
     * the investment id
     * 
     * @return array|NULL
     * 
     */
    function getOneInvestmentData($columnName = "*", $investmentID)
    {
        $db = MysqliDb::getInstance();
        $db->where('investmentID', $investmentID);
        $result = $db->getOne('Investment', $columnName);
        return $result;
    }


    /** 
     * Return JSON format of investment Type only
     * @return JSON|NULL
     * 
     * 
     */
    function getInvestTypesJSON()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $data = $this->getInvestTypesAndAmount();
            $investTypesArr = $data['investmentType'];
            $investTypesJSON = json_encode($investTypesArr);
            return $investTypesJSON;
        }
        return NULL;
    }

    /** 
     * Return JSON format of amount(investment TYPE) only
     * @return JSON |NULL
     * 
     * 
     */
    function getTypeAmountsJSON()
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $data = $this->getInvestTypesAndAmount();
            $investAmountArr = array_map('floatval', $data['amount']);
            $investAmountJSON = json_encode($investAmountArr);
            return $investAmountJSON;
        }
        return NULL;
    }

    /*********************************************************************\
     *               Below Part are show transaction page                 *
     *                       For extra functions                          *
     *                                                                    *
     *    **********   ********        ****      **      **   **********  *
     *        **       **     **    ***    ***   ****    **   **          *
     *        **       **      **   **      **   ** **   **   **          *
     *        **       **     **    **      **   **  **  **   **          *
     *        **       ********     **********   **   ** **   **********  *
     *        **       **     **    **      **   **    ****           **  *
     *        **       **      **   **      **   **     ***           **  *
     *        **       **      **   **      **   **      **   **********  *
     *                                                                    *
     *********************************************************************/

    /** 
     * Set flag in 0 or 1
     * @param int $bool
     * 0 -> Monthly
     * 1 -> Yearly
     * 
     */
    function setFlag($bool)
    {
        if ($bool == 0) {
            // initialise
            $this->flag = 0;
        } else {
            $this->flag = 1;
        }
    }

    /** 
     * Return the flag value
     * @return int|NULL
     * 
     */
    function getFlag()
    {
        if (!empty($this->flag)) {
            return $this->flag;
        }

        return NULL;
    }

    /** 
     * Set current or specify date
     * @param int $number
     * -2 -> set date before a year
     * -1 -> set date before a month
     * 0 -> set current date
     * 1 -> set date after a month
     * 2 -> set date after a year
     * 
     * @param String $cd
     * specific date string
     * 
     */
    function setCurDate($number = 0, $cd = "")
    {
        if ($number == 0) {
            // initialise
            $this->date = date("Y-m-d");
        } else {
            if ($number == 1) {
                $var = strtotime("first day of +1 month", strtotime($cd));
                $this->date = date("Y-m-d", $var);
            } elseif ($number == -1) {
                $var = strtotime("first day of -1 month", strtotime($cd));
                $this->date = date("Y-m-d", $var);
            } elseif ($number == 2) {
                $var = strtotime("first day of +1 year", strtotime($cd));
                $this->date = date("Y-m-d", $var);
            } elseif ($number == -2) {
                $var = strtotime("first day of -1 year", strtotime($cd));
                $this->date = date("Y-m-d", $var);
            }
        }
    }

    /** 
     * Return the date value
     * @return String|NULL
     * 
     */
    function getCurDate()
    {
        if (!empty($this->date)) {
            return $this->date;
        }

        return NULL;
    }


    /** 
     * Return String format of date
     * 
     * @param int $purpose
     * 0 -> used for display purpose
     * 1 -> used for query purpose
     * 
     * @param int $conditionFlag
     * 0 -> no condition
     * 1 -> condition
     * 2 -> condition
     * 
     * @param int $yearlyFlag
     * 0 -> monthly
     * 1 -> yearly
     * 
     * @return String |NULL
     * 
     */
    function getCurrentFilterTime($purpose = 0, $conditionFlag = 0, $yearlyFlag = 0)
    {
        // for display (December 2020)
        if (($purpose == 0 && $conditionFlag == 0 && $yearlyFlag == 0) or ($purpose == 0 && $conditionFlag == 1 && $yearlyFlag == 0)) {
            $d = strtotime($this->getCurDate());
            $systemMonth = date('F', $d);
            $systemYear = date("Y", $d);
            $systemDate = $systemMonth . " " . $systemYear;
            return $systemDate;
        }
        // for display (01 December 2020)
        if ($purpose == 0 && $conditionFlag == 1 && $yearlyFlag == 0) {
            $d = strtotime($this->getCurDate());
            $systemMonth = date('m', $d);
            $systemYear = date("Y", $d);
            $systemDate = "02 " . $systemMonth . " " . $systemYear;
            return $systemDate;
        }
        // for display (01-12-2020)
        if ($purpose == 0 && $conditionFlag == 1 && $yearlyFlag == 1) {
            $d = strtotime($this->getCurDate());
            $systemMonth = date('m', $d);
            $systemYear = date("Y", $d);
            $systemDate = "01-" . $systemMonth . "-" . $systemYear;
            return $systemDate;
        }
        // for query (2020)
        if (($purpose == 0 && $conditionFlag == 0 && $yearlyFlag == 1) or ($purpose == 1 && $conditionFlag == 1 && $yearlyFlag == 0) or ($purpose == 1 && $conditionFlag == 1 && $yearlyFlag == 1)) {
            $d = strtotime($this->getCurDate());
            $systemYear = date("Y", $d);
            return $systemYear;
        }
        // for query (12 -> Month)
        if ($purpose == 1 && $conditionFlag == 0 && $yearlyFlag == 0) {
            $d = strtotime($this->getCurDate());
            $systemMonth = date('m', $d);
            return $systemMonth;
        }
        // for query (0 -> Month)
        if ($purpose == 1 && $conditionFlag == 0 && $yearlyFlag == 1) {
            return 0;
        }
        // for query (A string contain sql query -> monthly)
        if ($purpose == 1 && $conditionFlag == 2 && $yearlyFlag == 0) {
            $d = strtotime($this->getCurDate());
            $systemMonth = date("m", $d);
            $systemYear = date("Y", $d);
            $output = " AND MONTH(t.date) = " . $systemMonth . " AND YEAR(t.date) = " . $systemYear . " ";
            return $output;
        }
        // for query (A string contain sql query -> yearly)
        if ($purpose == 1 && $conditionFlag == 2 && $yearlyFlag == 1) {
            $d = strtotime($this->getCurDate());
            $systemYear = date("Y", $d);
            $output = " AND YEAR(t.date) = " . $systemYear . " ";
            return $output;
        }
        return NULL;
    }

    /** 
     * Return Array format of time (XX:XX PM)
     * 
     * @param int $transactionId 
     * 
     * @param int $cusID 
     * 
     * @return Array |NULL
     * 
     */
    function getTime($transactionId, $cusID = "")
    {

        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $db->where('transactionID', $transactionId);
            $result = $db->getOne('Transaction', "date");
            $format = 'Y-m-d H:i:s';
            $formatedTime = DateTime::createFromFormat($format, $result['date']);
            $formatedTime = $formatedTime->format('H:i A');
            return $formatedTime;
        } elseif (!empty($cusID)) {
            $db->where('cusID', $cusID);
            $db->where('transactionID', $transactionId);
            $result = $db->getOne('Transaction', "date");
            $format = 'Y-m-d H:i:s';
            $formatedTime = DateTime::createFromFormat($format, $result['date']);
            $formatedTime = $formatedTime->format('H:i A');
            return $formatedTime;
        }
        return NULL;
    }

    /** 
     * Return Array format of date (YYYY-MM-DD)
     * 
     * @param int$transactionId 
     * 
     * @param int$cusID
     * 
     * @return Array|NULL
     * 
     */
    function getDate($transactionId, $cusID = "")
    {

        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->where('cusID', $id);
            $db->where('transactionID', $transactionId);
            $result = $db->getOne('Transaction', "date");
            $format = 'Y-m-d H:i:s';
            $formatedDate = DateTime::createFromFormat($format, $result['date']);
            $formatedDate = $formatedDate->format('Y-m-d');
            return $formatedDate;
        } elseif (!empty($cusID)) {
            $db->where('cusID', $cusID);
            $db->where('transactionID', $transactionId);
            $result = $db->getOne('Transaction', "date");
            $format = 'Y-m-d H:i:s';
            $formatedDate = DateTime::createFromFormat($format, $result['date']);
            $formatedDate = $formatedDate->format('Y-m-d');
            return $formatedDate;
        }
        return NULL;
    }

    /** 
     * Return JSON format of chart data 
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     * 
     * @param int $isExpense
     * is income or expense
     * 
     * @return JSON|NULL
     * s
     */
    function getTypesAndAmount($month, $year, $isExpense = 0)
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->join('transaction t', 'c.categoryID=t.categoryID', 'LEFT');
            $db->where('t.cusID', $id);
            if (empty($isExpense)) {
                $db->where('c.categoryType', 'income');
            } else {
                $db->where('c.categoryType', 'expenses');
            }

            if (!empty($month)) {
                $db->where('MONTH(t.date)', $month);
                $db->where('YEAR(t.date)', $year);
            } else {
                $db->where('YEAR(t.date)', $year);
            }
            $db->groupBy('c.categoryName');
            $db->orderBy('amount', 'DESC');
            $incomeTypesToValue = array();
            $result = $db->get('category c', null, 'c.categoryName, SUM(t.amount) AS amount');
            foreach ($result as $row => $data) {
                $tempValue = (float) $data['amount'];
                array_push($incomeTypesToValue, ['label' => $data['categoryName'], 'value' => $tempValue]);
            }
            $data = json_encode($incomeTypesToValue);

            if (empty($isExpense)) {
                // Ploting chart
                $chartJSON = '{
                    "chart": {
                    "caption": "Income By Category",
                    "plottooltext": "<b>$percentValue</b> of income are from $label",
                    "showlegend": "0",
                    "showpercentvalues": "1",
                    "legendNumRows": "3",
                    "legendNumColumns": "4",
                    "legendposition": "bottom",
                    "usedataplotcolorforlabels": "1",
                    "theme": "fusion",
                    palettecolors: "FE6E63,FE9850,FFD042,FEE801,BEE647,74D072,68E8DB,68E8DB"
                    },
                    "data": ' . $data . '
                }';
            } else {
                // Ploting chart
                $chartJSON = '{
                    "chart": {
                    "caption": "Expenses By Category",
                    "plottooltext": "<b>$percentValue</b> of expense are from $label",
                    "showlegend": "0",
                    "showpercentvalues": "1",
                    "legendNumRows": "3",
                    "legendNumColumns": "4",
                    "legendposition": "bottom",
                    "usedataplotcolorforlabels": "1",
                    "theme": "fusion",
                    palettecolors: "FE6E63,FE9850,FFD042,FEE801,BEE647,74D072,68E8DB,68E8DB"
                    },
                    "data": ' . $data . '
                }';
            }

            return $chartJSON;
        }

        return NULL;
    }

    /** 
     * Return Array format of income categoryName + totalAmount + percentage
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     * 
     * @param int $isExpense
     * is income or expense
     *  
     * @return Array|NULL
     * 
     */
    function getPercentage($month, $year, $isExpense = 0)
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $id = $this->id;
            $db->join('transaction t', 'c.categoryID=t.categoryID', 'LEFT');
            $db->where('t.cusID', $id);
            if (!empty($month)) {
                $db->where('MONTH(t.date)', $month);
                $db->where('YEAR(t.date)', $year);
            } else {
                $db->where('YEAR(t.date)', $year);
            }
            if (empty($isExpense)) {
                $db->where('c.categoryType', 'income');
            } else {
                $db->where('c.categoryType', 'expenses');
            }

            $db->groupBy('c.categoryName');
            $db->orderBy('amount', 'DESC');
            $incomeTypesToValue = array();
            $total = 0;
            $result = $db->get('category c', null, 'c.categoryName, SUM(t.amount) AS amount');
            foreach ($result as $row => $data) {
                $tempValue = (float) $data['amount'];
                $total += $tempValue;
                array_push($incomeTypesToValue, ['label' => $data['categoryName'], 'value' => $tempValue, 'percentage' => '']);
            }

            for ($i = 0; $i < sizeof($incomeTypesToValue); $i++) {
                $percentage = $incomeTypesToValue[$i]['value'] / $total * 100.00;
                $incomeTypesToValue[$i]['percentage'] = strval(round($percentage)) . '%';
            }

            return $incomeTypesToValue;
        }

        return NULL;
    }

    /** 
     * Return total number of days for given month and year
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     * 
     * @return int|NULL
     * 
     */
    function getDateNumByMonth($month, $year)
    {
        if (!empty($month)) {
            switch ($month) {
                case 1:
                case 3:
                case 5:
                case 7:
                case 8:
                case 10:
                case 12:
                    return 31;
                    break;
                case 4:
                case 6:
                case 9:
                case 11:
                    return 30;
                    break;
                case 2:
                    if ($year % 4 == 0) {
                        return 29;
                    } else {
                        return 28;
                    }
                    break;
            }
        }
        return NULL;
    }

    /** 
     * Return the array of amount and month for given month and year
     * 
     * @param String $cate
     * category name
     * 
     * @param int $cusID
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     * 
     * @return array|NULL
     * 'value' -> array: array of category value
     * 'month' -> array: an array of months
     */
    function getCategoryAmountByMonth($cate, $cusID, $month, $year)
    {
        $db = MysqliDb::getInstance();
        $id = $cusID;
        if (!empty($id)) {
            $categoryValueByMonth = array('value' => array(), 'month' => array());
            $monthArr = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            if (!empty($month)) {
                $numOfDays = $this->getDateNumByMonth($month, $year);
                for ($i = 1; $i <= $numOfDays; $i++) {
                    strlen($i) == 1 ? $days = "0" . $i : $days = strval($i);
                    strlen($month) == 1 ? $months = "0" . $month : $months = $month;
                    $date = "%" . $year . "-" . $months . "-" . $days . "%";
                    $db->join('transaction t', 'c.categoryID=t.categoryID', 'RIGHT');
                    $db->where('t.cusID', $id);
                    $db->where('c.categoryName', $cate);
                    $db->where('t.date', $date, 'LIKE');
                    $db->groupBy("t.date");
                    $result = $db->get('category c', null, 'SUM(t.amount) AS amount');
                    if (!empty($result[0]['amount'])) {
                        array_push($categoryValueByMonth['value'], $result[0]['amount']);
                    } else {
                        array_push($categoryValueByMonth['value'], 0);
                    }
                    array_push($categoryValueByMonth['month'], $days);
                }
            } else {
                for ($j = 1; $j <= 12; $j++) {
                    $db->join('transaction t', 'c.categoryID=t.categoryID', 'RIGHT');
                    $db->where('t.cusID', $id);
                    $db->where('c.categoryName', $cate);
                    $db->where('MONTH(t.date)', $j);
                    $db->where('YEAR(t.date)', $year);
                    $db->groupBy("MONTH(t.date)");
                    $result = $db->get('category c', null, 'SUM(t.amount) AS amount');
                    if (!empty($result[0]['amount'])) {
                        array_push($categoryValueByMonth['value'], $result[0]['amount']);
                    } else {
                        array_push($categoryValueByMonth['value'], 0);
                    }
                    array_push($categoryValueByMonth['month'], $monthArr[$j - 1]);
                }
            }
            return $categoryValueByMonth;
        }

        return NULL;
    }

    /** 
     * Return JSON format of category value only
     *  
     * @param String $cate
     * category name
     * 
     * @param int $cusID
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     *
     *  @return JSON|NULL
     * 
     * 
     */
    function getCategoryAmountJSON($cate, $cusID, $month, $year)
    {
        $db = MysqliDb::getInstance();
        $id = $cusID;
        if (!empty($id)) {
            $data = $this->getCategoryAmountByMonth($cate, $cusID, $month, $year);
            $amountArr = array_map('floatval', $data['value']);
            $amountJSON = json_encode($amountArr);
            return $amountJSON;
        }
        return NULL;
    }

    /** 
     * Return JSON format of month only
     * 
     * @param String $cate
     * category name
     * 
     * @param int $cusID
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     * 
     * @return JSON |NULL
     * 
     */
    function getCategoryMonthJSON($cate, $cusID, $month, $year)
    {
        $db = MysqliDb::getInstance();
        $id = $cusID;
        if (!empty($id)) {
            $data = $this->getCategoryAmountByMonth($cate, $cusID, $month, $year);
            $monthArr = $data['month'];
            $monthJSON = json_encode($monthArr);
            return $monthJSON;
        }
        return NULL;
    }

    /** 
     * Return Array of table row count for transaction table only
     * 
     * @param int $month
     * (12)
     * 
     * @param int $year
     * (2020)
     * 
     * @return Array|NULL
     * 
     */
    function getTableRowCount($month, $year)
    {
        $db = MysqliDb::getInstance();

        if (!empty($this->id)) {
            $id = $this->id;
            $db->join('category c', 'c.categoryID=t.categoryID', 'LEFT');
            $db->where('t.cusID', $id);
            if (!empty($month)) {
                $db->where('MONTH(t.date)', $month);
                $db->where('YEAR(t.date)', $year);
            } else {
                $db->where('YEAR(t.date)', $year);
            }
            $result = $db->get('transaction t');
            return $result;
        }
        return NULL;
    }

    /*********************************************************************\
     *               Below Part are show liability page                   *
     *                       For extra functions                          *
     *                                                                    *
     *    **           **********      ****      *********    **********  *
     *    **               **       ***    ***   **      **       **      *
     *    **               **       **      **   **      **       **      *
     *    **               **       **      **   *********        **      *
     *    **               **       **********   **      **       **      *
     *    **               **       **      **   **       **      **      *
     *    **               **       **      **   **      **       **      *
     *    **********   **********   **      **   *********    **********  *
     *                                                                    *
     *********************************************************************/


    /** 
     * Return JSON format of THE SPECIFIC FIELD groupby query
     * @param String $tablename
     * table name
     * 
     * @param String $columnName
     * the column name after SELECT
     * 
     * 
     * @param String $fieldToGetInJSON
     * the name of field to get in JSON
     * 
     * @param boolean $isNumeric
     * is the field to get a numeric value
     * 
     * @param String $groupBy
     * the amount group by which field
     * 
     * @param String $where
     * the where clause
     * 
     * 
     * @return JSON|NULL
     * 
     * 
     */
    function getJSON($tablename, $columnName, $fieldToGetInJSON, $isNumeric = false, $groupBy = NULL, $where = NULL)
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $result = $this->getData($tablename, $columnName, $groupBy, $where);
            $finalResult = array();
            foreach ($result as $row => $data) {
                array_push($finalResult, $data[$fieldToGetInJSON]);
            }
            if ($isNumeric) { #convert the value to float data type if it is amount
                $finalResult = array_map('floatval', $finalResult);
            }
            $resultJSON = json_encode($finalResult);
            return $resultJSON;
        }
        return NULL;
    }

    /** 
     * Return JSON format of either amount / field for a sum + groupby query
     * @param String $rawquery
     * the query
     * 
     * @param String $fieldToGetInJSON
     * the name of field to get in JSON
     * 
     * @param boolean $isNumeric
     * is the field to get a numeric value
     * 
     * @return JSON|NULL
     * 
     * 
     */
    function getJSONbyRawQuery($rawquery, $fieldToGetInJSON, $isNumeric = false)
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $result = $this->getDataByQuery($rawquery);
            $finalResult = array();
            foreach ($result as $row => $data) {
                array_push($finalResult, $data[$fieldToGetInJSON]);
            }
            if ($isNumeric) { #convert the value to float data type if it is amount
                $finalResult = array_map('floatval', $finalResult);
            }
            $resultJSON = json_encode($finalResult);
            return $resultJSON;
        }
        return NULL;
    }

    /** 
     * @param String $frequency
     * 
     * @param String $date
     * 
     * @return String date
     * 
     * @uses $dateString = $customer->getDateByFrequency($frequency,$date);
     * 
     * 
     */
    function getDateByFrequency($frequency, $date)
    {
        $now = date('Y-m-d');
        if ($now < $date) { //the paymentdate havent due yet
            return $date;
        }
        if ($frequency == "") { //if there is no frequency then just go ahead, get the scheduled 
            return $date;
        }
        $paymentDateStamp = strtotime($date);
        $paymentDay = date('d', $paymentDateStamp);
        if ($frequency == "M") {
            $lastdayofnextmonth = date('t', strtotime('+1 month')); //get the last day of month
            if ($paymentDay > $lastdayofnextmonth) { //if the day exceed the last day
                $paymentDay = $lastdayofnextmonth;
            }
            $newdatestamp = strtotime(date('Y') . '-' . date('m', strtotime('+1 month')) . '-' . $paymentDay);
            return date("Y-m-d", $newdatestamp);
        } elseif ($frequency == "Y") {
            $paymentMonth = date('m', $paymentDateStamp);
            $newdatestamp = strtotime(date('Y', strtotime('+1 year')) . '-' . $paymentMonth . '-1'); //get the month in the next year
            $lastdayofthemonth = date('t', $newdatestamp); //get the last day of month in next year
            if ($paymentDay > $lastdayofthemonth) { //if the day exceed the last day
                $paymentDay = $lastdayofthemonth;
            }
            $newdatestamp = strtotime(date('Y', strtotime('+1 year', $paymentDateStamp)) . '-' . $paymentMonth . '-' . $paymentDay);

            return date("Y-m-d", $newdatestamp);
        }
        return NULL;
    }

    /** 
     * get category id by category name and type
     * @param String $categoryName
     * 
     * @param String $categoryType
     * 
     * 
     * @uses $dateString = $customer->getDateByFrequency($frequency,$date);
     * 
     * @return String|NULL 
     * the category ID
     * 
     */
    function getCategoryIDByNameType($categoryName, $categoryType)
    {
        $db = MysqliDb::getInstance();
        if (!empty($this->id)) {
            $db->where('categoryName', $categoryName);
            $db->where('categoryType', $categoryType);
            $result = $db->getOne("Category");
            return $result['categoryID'];
        }
        return NULL;
    }
}
