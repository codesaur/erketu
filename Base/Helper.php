<?php namespace codesaur\Base;

use codesaur\DataObject\MySQL;

use PHPMailer\PHPMailer\PHPMailer;

class Helper extends Base
{
    public function getPDO() : MySQL
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
