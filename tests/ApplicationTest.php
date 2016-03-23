<?php

namespace ShineUnited\Silex\Common\Tests;

use ShineUnited\Silex\Common\Application;


class ApplicationTest extends \PHPUnit_Framework_TestCase {

	public function testMonologMissingLogFile() {
		$app = new Application();

		$this->setExpectedException('InvalidArgumentException');
		$app['monolog.logfile'];
	}

	public function testMonologEnvLogFile() {
		$logfile = md5('logfile');

		$_ENV['LOG_FILE'] = $logfile;

		$app = new Application();

		$this->assertEquals($logfile, $app['monolog.logfile']);
	}

	public function testMonologEnvLogLevel() {
		$level = md5('level');

		$_ENV['LOG_LEVEL'] = $level;

		$app = new Application();

		$this->assertEquals($level, $app['monolog.level']);
	}

	public function testMonologEnvLogName() {
		$name = md5('name');

		$_ENV['LOG_NAME'] = $name;

		$app = new Application();

		$this->assertEquals($name, $app['monolog.name']);
	}

	public function testMonologDirectLogFile() {
		$logfile = md5('logfile');

		$config = [
			'monolog.logfile' => $logfile
		];

		$app = new Application($config);

		$this->assertEquals($logfile, $app['monolog.logfile']);
	}

	public function testMonologDirectLogLevel() {
		$level = md5('level');

		$config = [
			'monolog.level' => $level
		];

		$app = new Application($config);

		$this->assertEquals($level, $app['monolog.level']);
	}

	public function testMonologDirectLogName() {
		$name = md5('name');

		$config = [
			'monolog.name' => $name
		];

		$app = new Application($config);

		$this->assertEquals($name, $app['monolog.name']);
	}

	public function testSwiftmailerNullTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'null'
		];

		$this->assertInstanceOf('Swift_Transport_NullTransport', $app['swiftmailer.transport']);
	}

	public function testSwiftmailerMailTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'mail'
		];

		$this->assertInstanceOf('Swift_Transport_MailTransport', $app['swiftmailer.transport']);
	}

	public function testSwiftmailerSendmailTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'sendmail'
		];

		$this->assertInstanceOf('Swift_Transport_SendmailTransport', $app['swiftmailer.transport']);
	}

	public function testSwiftmailerSmtpTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'smtp'
		];

		$this->assertInstanceOf('Swift_Transport_EsmtpTransport', $app['swiftmailer.transport']);
	}

	/**
	 * @dataProvider swiftmailerEnvOptionsProvider
	 */
	public function testSwiftmailerEnvOption($optName, $envName, $value = 'test') {
		$_ENV[$envName] = $value;

		$app = new Application();

		$this->assertEquals($value, $app['swiftmailer.options'][$optName]);
	}

	public function swiftmailerEnvOptionsProvider() {
		return [
			['host', 'SMTP_HOST'],
			['port', 'SMTP_PORT'],
			['username', 'SMTP_USERNAME'],
			['password', 'SMTP_PASSWORD'],
			['encryption', 'SMTP_ENCRYPTION'],
			['auth_mode', 'SMTP_AUTH_MODE']
		];
	}

	/**
	 * @dataProvider swiftmailerDirectOptionsProvider
	 */
	public function testSwiftmailerDirectOption($optName, $value = 'test') {
		$config = [];
		$config['swiftmailer.options'] = [];
		$config['swiftmailer.options'][$optName] = $value;

		$app = new Application($config);

		$this->assertEquals($value, $app['swiftmailer.options'][$optName]);
	}

	public function swiftmailerDirectOptionsProvider() {
		return [
			['host'],
			['port'],
			['username'],
			['password'],
			['encryption'],
			['auth_mode']
		];
	}

	/**
	 * @dataProvider DoctrineEnvOptionsProvider
	 */
	public function testDoctrineEnvOption($optName, $envName, $value = 'test') {
		$_ENV[$envName] = $value;

		$app = new Application();

		$this->assertEquals($value, $app['db.options'][$optName]);
	}

	public function doctrineEnvOptionsProvider() {
		return [
			['driver', 'RDS_DRIVER'],
			['dbname', 'RDS_DB_NAME'],
			['host', 'RDS_HOSTNAME'],
			['user', 'RDS_USERNAME'],
			['password', 'RDS_PASSWORD'],
			['charset', 'RDS_CHARSET'],
			['path', 'RDS_PATH'],
			['port', 'RDS_PORT']
		];
	}

	/**
	 * @dataProvider doctrineDirectOptionsProvider
	 */
	public function testDoctrineDirectOption($optName, $value = 'test') {
		$config = [];
		$config['db.options'] = [];
		$config['db.options'][$optName] = $value;

		$app = new Application($config);

		$this->assertEquals($value, $app['db.options'][$optName]);
	}

	public function doctrineDirectOptionsProvider() {
		return [
			['driver'],
			['dbname'],
			['host'],
			['user'],
			['password'],
			['charset'],
			['path'],
			['port']
		];
	}
}
