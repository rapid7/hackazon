<?php

namespace PHPixie;

/**
 * Email Module for PHPixie
 *
 * This module is not included by default, install it using Composer
 * by adding
 * <code>
 * 		"phpixie/email": "2.*@dev"
 * </code>
 * to your requirement definition. Or download it from
 * https://github.com/dracony/PHPixie-swift-mailer
 * 
 * To enable it add it to your Pixie class' modules array:
 * <code>
 * 		protected $modules = array(
 * 			//Other modules ...
 * 			'email' => '\PHPixie\Email',
 * 		);
 * </code>
 *
 * For information on configuring your email transport check out
 * /assets/config/cache.php config file inside this module.
 *
 * @link https://github.com/dracony/PHPixie-Cache Download this module from Github
 * @package    Cache
 */
class Email {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * An array of Swift_Mailer instances, one for each driver
	 * @var    array
	 * @access protected
	 */
	protected $_instances;

	/**
	 * Initializes the Email module
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie)	{
		$this->pixie = $pixie;
	}

	/**
	 * Gets a Swift_Mailer instance for the specified driver.
	 *
	 * @param   string $config Configuration name of the connection. Defaults to 'default'.
	 * @return  \Swift_Mailer  Initialized mailer
	 */
	public function mailer($config) {
	
		//Create instance of the connection if it wasn't created yet
		if (!isset($this->_instances[$config]))
			$this->instances[$config] = $this->build_mailer($config);
			
		return $this->instances[$config];
	}
	
	/**
	 * Sends an email message.
	 *
	 * <code>
	 * //$to and $from parameters can be one of these
	 * 'user@server.com'
	 * array('user@server.com' => 'User Name')
	 *
	 * //$to accepts multiple recepients
	 * array(
	 *     'user@server.com',
	 *     array('user2@server.com' => 'User Name')
	 * )
	 *
	 * //You can specify To, Cc and Bcc like this
	 * array(
	 *     'to' => array(
	 *         'user@server.com',
	 *         array('user2@server.com' => 'User Name')
	 *      ),
	 *      'cc' => array(
	 *         'user3@server.com',
	 *         array('user4@server.com' => 'User Name')
	 *      ),
	 *      'bcc' => array(
	 *         'user5@server.com',
	 *         array('user6@server.com' => 'User Name')
	 *      )
	 * );
 	 * </code>
	 *
	 * @param   string|array $to        Recipient email (and name), or an array of To, Cc, Bcc names
	 * @param   string|array $from      Sender email (and name)
	 * @param   string       $subject   Message subject
	 * @param   string       $message   Message body
	 * @param   boolean      $html      Send email as HTML
	 * @param   string 		 $config    Configuration name of the connection. Defaults to 'default'.
	 * @return  integer      Number of emails sent
	 */
	public function send($to, $from, $subject, $message, $html = false, $config = 'default') {
		
		// Create the message
		$message = \Swift_Message::newInstance($subject, $message, $html?'text/html':'text/plain', 'utf-8');
		
		//Normalize the input array
		if (is_string($to)) {
		
			//No name specified
			$to = array('to' => array($to));
			
		} elseif(is_array($to) && is_string(key($to)) && is_string(current($to))) {
		
			//Single recepient with name
		    $to = array('to' => array($to));
			
		} elseif(is_array($to) && is_numeric(key($to))) {
		
			//Multiple recepients
			$to = array('to' => $to);
			
		}
		
		foreach ($to as $type => $set) {
			$type=strtolower($type);
			if (!in_array($type, array('to', 'cc', 'bcc'), true))
				throw new Exception("You can only specify 'To', 'Cc' or 'Bcc' recepients. You attempted to specify {$type}.");
				
			// Get method name
			$method = 'add'.ucfirst($type);
			foreach($set as $recepient) {
				if(is_array($recepient))
					$message->$method(key($recepient),current($recepient));
				else
					$message->$method($recepient);
			}
		}
		if($from === null) {
			$from = $this->pixie->config->get("email.{$config}.sender");
		}
		
		if(is_array($from))
			$message->setFrom(key($from),current($from));
		else
			$message->setFrom($from);

		return $this->mailer($config)->send($message);
	}
	
	protected function build_mailer($config) {
		$type = $this->pixie->config->get("email.{$config}.type",'native');
		switch ($type) {
			case 'smtp':
				
				// Create SMTP Transport
				$transport = \Swift_SmtpTransport::newInstance(
					$this->pixie->config->get("email.{$config}.hostname"),
					$this->pixie->config->get("email.{$config}.port",25)
				);
				
				// Set encryption if specified
				if ( ($encryption = $this->pixie->config->get("email.{$config}.encryption",false)) !== false)
					$transport->setEncryption($encryption);
				
				// Set username if specified
				if ( ($username = $this->pixie->config->get("email.{$config}.username",false)) !== false)
					$transport->setUsername($username);
					
				// Set password if specified
				if ( ($password = $this->pixie->config->get("email.{$config}.password",false)) !== false)
					$transport->setPassword($password);
					
				// Set timeout, defaults to 5 seconds
				$transport->setTimeout($this->pixie->config->get("email.{$config}.timeout", 5));
				
			break;
			
			case 'sendmail':
				
				// Create a sendmail connection, defalts to "/usr/sbin/sendmail -bs"
				$transport = \Swift_SendmailTransport::newInstance($this->pixie->config->get("email.{$config}.sendmail_command", "/usr/sbin/sendmail -bs"));
				
			break;
			
			case 'native':
				
				// Use the native connection and specify additional params, defaults to "-f%s"
				$transport = \Swift_MailTransport::newInstance($this->pixie->config->get("email.{$config}.mail_parameters","-f%s"));
				
			break;
			
			default:
				throw new Exception("Connection can be one of the following: smtp, sendmail or native. You specified '{$type}' as type");
		}

		return \Swift_Mailer::newInstance($transport);
	}

}
?>
