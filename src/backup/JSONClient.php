<?php namespace codesaur\Backup;

class JSONClient extends Client
{
    public function get(string $uri, $payload = null, bool $assoc = true, $headers = array())
    {
        return \json_decode($this->request($uri, 'GET', $payload, $headers), $assoc);
    }
    
    public function post(string $uri, $payload, bool $assoc = true, $headers = array())
    {
        return \json_decode($this->request($uri, 'POST', $payload, $headers), $assoc);
    }

    public function request(string $uri, $method, $payload, array $headers)
    {
        try {
            $header = array('Content-Type: application/json');
            
            if (isset($payload)) {
                $data = \json_encode($payload);
            } else {
                $data = $method != 'GET' ? '{}' : '';
            }
            
            foreach ($headers as $index => $field) {
                $header[] = "$index: $field";
            }
            
            $options = array(
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => $header
            );
            
            return parent::request($uri, $method, $data, $options);
        } catch (\Exception $e) {
            return \json_encode(array('error' => array(
                'code' => $e->getCode(), 'message' => $e->getMessage())));
        }
    }
}
