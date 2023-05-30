<?php

namespace App\Services\Report;

/**
 * Oracle publisher report generate
 *
 * Class OraclePublisher
 * @package App\Services\Report
 */
class OraclePublisher
{
    private $xdo;
    private $params;
    private $type;
    protected $soapOptions = array();

    public function __construct()
    {
        $this->soapOptions = [
                'trace' => true,
                'keep_alive' => false,
                'connection_timeout' => 6000,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression'   => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            ];
    }

    /**
     * Generate report based on property set
     *
     * @return mixed
     * @throws \Exception
     */
    public function generate() {
        try {
            $wsdl = env('REPORT_EXECUTE_PATH',"http://192.168.78.25:9502/xmlpserver/services/ReportService?wsdl");
            $client = new \SoapClient($wsdl, $this->soapOptions);
            $parameters = [];

            if ($params = $this->getParams()) {
                foreach ($params as $k => $v) {
                    if ($k && $v)
                    $parameters[] = ['name' => $k, 'value' => $v, 'hasMultiValues' => 0];
                }
            }
            if (!$this->getXdo())
                throw new \Exception('XDO not set!');
            if (!$this->getType())
                throw new \Exception('Report type not set!');

            return $client->getReportData($this->getXdo(),
                $parameters,
                env('REPORT_USERNAME', 'weblogic'), // 'Guest' can be used with a null pwd
                env('REPORT_PASSWORD','cns1234321'),
                $this->getType()
            );
        }
        catch (\Exception $e) {
            echo $e->getMessage();
            throw new \Exception("Something went wrong, Please try again by refresh the page");
        }
    }

    /**
     * @return mixed
     */
    public function getXdo()
    {
        return $this->xdo;
    }

    /**
     * @param mixed $xdo
     * @return OraclePublisher
     */
    public function setXdo($xdo)
    {
        $this->xdo = $xdo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     * @return OraclePublisher
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Add parameter
     *
     * @param $param
     * @return $this
     */
    public function addParam($param) {
        $this->params[] = $param;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return OraclePublisher
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
