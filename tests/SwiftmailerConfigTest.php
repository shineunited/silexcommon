<?php

namespace ShineUnited\Silex\Common\Tests;

use ShineUnited\Silex\Common\Application;


class SwiftmailerConfigTest extends \PHPUnit_Framework_TestCase {

	public function testNullTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'null'
		];

		$this->assertInstanceOf('Swift_Transport_NullTransport', $app['swiftmailer.transport']);
	}

	public function testMailTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'mail'
		];

		$this->assertInstanceOf('Swift_Transport_MailTransport', $app['swiftmailer.transport']);
	}

	public function testSendmailTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'sendmail'
		];

		$this->assertInstanceOf('Swift_Transport_SendmailTransport', $app['swiftmailer.transport']);
	}

	public function testSmtpTransport() {
		$app = new Application();
		$app['swiftmailer.options'] = [
			'transport' => 'smtp'
		];

		$this->assertInstanceOf('Swift_Transport_EsmtpTransport', $app['swiftmailer.transport']);
	}

	/**
	 * @dataProvider envOptionsProvider
	 */
	public function testEnvOption($optName, $envName, $value = 'test') {
		$_ENV[$envName] = $value;

		$app = new Application();

		$this->assertEquals($value, $app['swiftmailer.options'][$optName]);
	}

	public function envOptionsProvider() {
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
	 * @dataProvider directOptionsProvider
	 */
	public function testDirectOption($optName, $value = 'test') {
		$config = [];
		$config['swiftmailer.options'] = [];
		$config['swiftmailer.options'][$optName] = $value;

		$app = new Application($config);

		$this->assertEquals($value, $app['swiftmailer.options'][$optName]);
	}

	public function directOptionsProvider() {
		return [
			['host'],
			['port'],
			['username'],
			['password'],
			['encryption'],
			['auth_mode']
		];
	}
}
