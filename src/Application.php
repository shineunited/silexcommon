<?php

namespace ShineUnited\Silex\Common;

use Silex\Application AS BaseApplication;
use Silex\Application\MonologTrait;
use Silex\Application\SwiftmailerTrait;
use Silex\Application\TranslationTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
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
		$this->initializeTranslationService();
		$this->initializeValidationService();
		$this->initializeDoctrineService();
	}

	private function initializeDoctrineService() {
		$this->register(new DoctrineServiceProvider());

		$this['db.options'] = $this->share(function() {
			$options = [];

			$map = [
				'driver'   => 'RDS_DRIVER',
				'dbname'   => 'RDS_DB_NAME',
				'host'     => 'RDS_HOSTNAME',
				'user'     => 'RDS_USERNAME',
				'password' => 'RDS_PASSWORD',
				'charset'  => 'RDS_CHARSET',
				'path'     => 'RDS_PATH',
				'port'     => 'RDS_PORT'
			];

			foreach($map as $optname => $envname) {
				if(!isset($_ENV[$envname])) {
					continue;
				}

				$options[$optname] = $_ENV[$envname];
			}

			if(count(array_keys($options)) > 0) {
				return $options;
			}

			throw new \InvalidArgumentException('Identifier "db.options" is not defined.');
		});

		$this['db.schema'] = $this->share(function() {
			return new Schema();
		});

		$this['db'] = $this->share($this->extend('db', function($db) {
			$schemaManager = $db->getSchemaManager();
			$currentSchema = $schemaManager->createSchema();

			$queries = $currentSchema->getMigrateToSql($this['db.schema'], $db->getDatabasePlatform());
			foreach($queries as $query) {
				if(substr($query, 0, 4) == 'DROP') {
					// skip drop table queries
					continue;
				}

				$db->exec($query);
			}

			return $db;
		}));
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

			throw new \InvalidArgumentException('Identifier "monolog.logfile" is not defined.');
		});

		$this['monolog'] = $this->share($this->extend('monolog', function($monolog) {
			$dirpath = dirname($this['monolog.logfile']);
			if(!is_dir($dirpath)) {
				mkdir($dirpath, 0777, true);
			}

			return $monolog;
		}));
	}

	private function initializeSwiftmailerService() {
		$this->register(new SwiftmailerServiceProvider());

		// set defaults
		$this['swiftmailer.options'] = $this->share(function() {
			$options = [];
			$options['transport'] = 'null';

			$map = [
				'host'       => 'SMTP_HOST',
				'port'       => 'SMTP_PORT',
				'username'   => 'SMTP_USERNAME',
				'password'   => 'SMTP_PASSWORD',
				'encryption' => 'SMTP_ENCRYPTION',
				'auth_mode'  => 'SMTP_AUTH_MODE'
			];

			foreach($map as $optname => $envname) {
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
