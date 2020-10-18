<?php namespace codesaur\Base;

use codesaur\Http\Header;
use codesaur\Http\Router;
use codesaur\Http\Request;
use codesaur\Http\Response;

use codesaur\DataObject\MySQL;

use PHPMailer\PHPMailer\PHPMailer;

class Application extends Base implements ApplicationInterface
{
    private $_namespace;

    public $request;
    public $router;
    public $header;
    public $response;

    public $route;
    public $controller;
    
    function __construct(array $config)
    {
        if (empty($config)) {
            return $this->error('Invalid application configuration!');
        }
        
        $this->initComponents();
        
        $url_segments = $this->request->getUrlSegments();
        if ( ! empty($url_segments)) {
            $uri = '/' . $url_segments[0];
            
            if (isset($config[$uri])) {
                $request_url = $this->request->getCleanUrl();
                $shifted = \substr($request_url, \strlen($uri));

                $this->request->setApp($uri);
                $this->request->shiftUrlSegments();
                $this->request->forceCleanUrl($shifted);

                $this->_namespace = $config[$uri];
            }
        }

        if ( ! isset($this->_namespace)) {
            if (isset($config['/'])) {
                $this->_namespace = $config['/'];
            } else {
                return $this->error('Default application not found!');
            }
        }
    }
    
    public function initComponents()
    {
        $this->request = new Request();
        $this->request->initFromGlobal();
        
        $this->router = new Router();
        $this->header = new Header();
        
        $this->response = new Response();
    }
    
    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function launch()
    {   
        $routing = $this->getNamespace() . 'Routing';

        if ( ! \class_exists($routing)) {
            return $this->error("$routing not found! [URL: " . ($this->request->getUrl() ?? '') . ']');
        }

        $this->route = (new $routing())->match($this->router, $this->request);
        
        if ( ! isset($this->route)) {
            return $this->error('Unknown route!');
        }

        $controller = $this->route->getController();

        if ( ! \class_exists($controller)) {
            return $this->error("$controller is not available!");
        }

        $action = $this->route->getAction();
        
        $this->controller = new $controller();
        if (! $this->controller->hasMethod($action)) {
            return $this->error("Action named $action is not part of $controller!");
        }

        $this->execute($this->controller, $action, $this->route->getParameters()); exit;
    }
    
    public function execute($class, string $action, array $args)
    {
        if (\getenv('OUTPUT_COMPRESS') == 'true') {
            $this->response->start(array($this->response->ob, 'compress'), 0, PHP_OUTPUT_HANDLER_STDFLAGS);
        } else {
            $this->response->start();
        }
        
        $this->callFuncArray(array($class, $action), $args);
        
        $this->response->send();
    }
    
    public function error(string $message, int $status = 404)
    {
        if ( ! \headers_sent()) {
            \http_response_code($status);
        }
        
        \error_log("Error[$status]: $message");
        
        try {
            $controller = $this->getNamespace() . 'ErrorController';
            $this->execute(new $controller(), 'error', ['error' => $message, 'status' => $status]);
        } catch (\Throwable $t) {
            $host = 'https://';
            $host .= $_SERVER['HTTP_HOST'] ?? 'localhost';

            if (DEBUG) {
                echo "<pre>$t</pre>";
                $notice = "<hr><strong>Output: </strong><br/>" . \ob_get_contents();
                \ob_end_clean();
            }

            echo    '<!doctype html><html lang="en"><head><meta charset="utf-8"/><title>' . 'Error ' .
                    $status . '</title></head><body><h1>Error ' . $status . '</h1><p>' . $message .
                    '</p><hr><a href="' . $host . '">' . $host . '</a>' . ($notice ?? null) . '</body></html>';
        }
        
        exit;
    }

    public function getWebUrl(bool $relative) : string
    {
        if ($relative) {
            return $this->request->getPath();
        }
        
        return $this->request->getHttpHost() . $this->request->getPath();
    }
    
    public function getPDOconnection() : MySQL
    {
        $configuration = array(
            'driver'    => \getenv('DB_DRIVER') ?: 'mysql',
            'host'      => \getenv('DB_HOST') ?: 'localhost',
            'username'  => \getenv('DB_USERNAME') ?: 'root',
            'password'  => \getenv('DB_PASSWORD') ?: '',
            'name'      => \getenv('DB_NAME') ?: 'indoraptor',
            'engine'    => \getenv('DB_ENGINE') ?: 'InnoDB',
            'charset'   => \getenv('DB_CHARSET') ?: 'utf8',
            'collation' => \getenv('DB_COLLATION') ?: 'utf8_unicode_ci',
            'options'   => array(
                \PDO::ATTR_ERRMODE     => DEBUG ?
                \PDO::ERRMODE_EXCEPTION : \PDO::ERRMODE_WARNING,
                \PDO::ATTR_PERSISTENT  => \getenv('DB_PERSISTENT') == 'true'
            )
        );
        
        $conn = new MySQL($configuration);
        
        if ($conn->alive()) {
            if (\getenv('TIME_ZONE_UTC')) {
                $conn->exec('SET time_zone = ' . $conn->quote(\getenv('TIME_ZONE_UTC')));
            }
        }
        
        return $conn;
    }
    
    public function getPHPMailer($record, array $options = array(
        'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true)) : ?PHPMailer
    {
        if (empty($record) || ! isset($record['charset']) || ! isset($record['host']) || ! isset($record['port'])
                || ! isset($record['is_smtp']) || ! isset($record['smtp_auth']) || ! isset($record['smtp_secure'])
                || ! isset($record['username']) || ! isset($record['password']) || ! isset($record['email']) || ! isset($record['name'])) {
            return null;
        }

        $mailer = new PHPMailer(false);
        if (((int) $record['is_smtp']) == 1) {
           $mailer->IsSMTP(); 
        }
        $mailer->CharSet = $record['charset'];
        $mailer->SMTPAuth = (bool)((int) $record['smtp_auth']);
        $mailer->SMTPSecure = $record['smtp_secure'];
        $mailer->Host = $record['host'];
        $mailer->Port = $record['port'];            
        $mailer->Username = $record['username'];
        $mailer->Password = $record['password'];
        $mailer->SetFrom($record['email'], $record['name']);
        $mailer->AddReplyTo($record['email'], $record['name']);
        $mailer->SMTPOptions = array('ssl' => $options);

        return $mailer;
    }
}
