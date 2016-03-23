<?php

namespace ShineUnited\Silex\Common\Tests;

use ShineUnited\Silex\Common\Application;


class DoctrineConfigTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider envOptionsProvider
	 */
	public function testEnvOption($optName, $envName, $value = 'test') {
		$_ENV[$envName] = $value;

		$app = new Application();

		$this->assertEquals($value, $app['db.options'][$optName]);
	}

	public function envOptionsProvider() {
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
	 * @dataProvider directOptionsProvider
	 */
	public function testDirectOption($optName, $value = 'test') {
		$config = [];
		$config['db.options'] = [];
		$config['db.options'][$optName] = $value;

		$app = new Application($config);

		$this->assertEquals($value, $app['db.options'][$optName]);
	}

	public function directOptionsProvider() {
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
