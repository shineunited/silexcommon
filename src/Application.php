<?php

namespace ShineUnited\SilexApps;

use Silex\Application as BaseApplication;
use Silex\Application\MonologTrait;
use Silex\Application\SwiftmailerTrait;
use Silex\Application\TranslationTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Debug\ErrorHandler;


class Application extends BaseApplication {
	use MonologTrait;
	use SwiftmailerTrait;
	use TranslationTrait;
	use TwigTrait;
	use UrlGeneratorTrait;

	public function __construct(array $values = []) {
		parent::__construct();

		$this->initialize();

		foreach($values as $key => $value) {
			$this[$key] = $value;
		}
	}

	protected function initialize() {
		$this->initializeMonologService();
		$this->initializeSwiftmailerService();
		$this->initializeTwigService();
		$this->initializeUrlGeneratorService();
		$this->initializeValidationService();
		$this->initializeTranslationService();
		$this->initializeSessionService();
	}

	private function initializeMonologService() {
		$this->register(new MonologServiceProvider());

		$this['monolog.name'] = $this->share(function() {
			if(isset($_ENV['LOG_NAME'])) {
				return $_ENV['LOG_NAME'];
			}

			return 'app';
		});

		$this['monolog.level'] = $this->share(function() {
			if(isset($_ENV['LOG_LEVEL'])) {
				return $_ENV['LOG_LEVEL'];
			}

			return 'debug';
		});

		$this['monolog.logfile'] = $this->share(function() {
			if(isset($_ENV['LOG_FILE'])) {
				return $_ENV['LOG_FILE'];
			}

			$logDir = $this['app.root'] . '/log';
			if(!is_dir($logDir)) {
				mkdir($logDir, 0777, true);
			}

			$logFile = $logDir . '/' . $this['now']->format('Ymd') . '.log';

			return $logFile;
		});
	}

	private function initializeSessionService() {
		$this->register(new SessionServiceProvider());
	}

	private function initializeSwiftmailerService() {
		$this->register(new SwiftmailerServiceProvider());

		// set defaults
		$this['swiftmailer.options'] = $this->share(function() {
			$options = [];
			$options['transport'] = 'null';

			$optnames = ['host', 'port', 'username', 'password', 'encryption', 'auth_mode'];
			foreach($optnames as $optname) {
				$envname = 'SMTP_' . strtoupper($optname);

				if(!isset($_ENV[$envname])) {
					continue;
				}

				$options['transport'] = 'smtp';
				$options[$optname] = $_ENV[$envname];
			}

			return $options;
		});

		$this['swiftmailer.transport.mail'] = $this->share(function() {
			$transport = \Swift_MailTransport::newInstance();

			return $transport;
		});

		$this['swiftmailer.transport.sendmail'] = $this->share(function() {
			$transport = \Swift_SendmailTransport::newInstance();

			return $transport;
		});

		$this['swiftmailer.transport.null'] = $this->share(function() {
			$transport = \Swift_NullTransport::newInstance();

			return $transport;
		});

		$this['swiftmailer.transport'] = $this->extend('swiftmailer.transport', function($transport) {
			if(isset($this['swiftmailer.options']) && isset($this['swiftmailer.options']['transport'])) {
				$service = 'swiftmailer.transport.' . strtolower(trim($this['swiftmailer.options']['transport']));
				if(isset($this[$service])) {
					return $this[$service];
				}
			}

			return $transport;
		});
	}

	private function initializeTranslationService() {
		$this->register(new TranslationServiceProvider());
	}

	private function initializeTwigService() {
		$this->register(new TwigServiceProvider());
	}

	private function initializeUrlGeneratorService() {
		$this->register(new UrlGeneratorServiceProvider());
	}

	private function initializeValidationService() {
		$this->register(new ValidatorServiceProvider());
	}

	public function boot() {
		if(!$this->booted) {
			$handler = ErrorHandler::register();
			$handler->setDefaultLogger($this['logger']);
		}

		parent::boot();
	}
}
