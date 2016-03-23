<?php

namespace ShineUnited\Silex\Common\Tests;

use ShineUnited\Silex\Common\Application;


class MonologTest extends \PHPUnit_Framework_TestCase {

	public function testMissingLogFile() {
		$app = new Application();

		$this->setExpectedException('InvalidArgumentException');
		$app['monolog.logfile'];
	}

	public function testEnvLogFile() {
		$logfile = md5('logfile');

		$_ENV['LOG_FILE'] = $logfile;

		$app = new Application();

		$this->assertEquals($logfile, $app['monolog.logfile']);
	}

	public function testEnvLogLevel() {
		$level = md5('level');

		$_ENV['LOG_LEVEL'] = $level;

		$app = new Application();

		$this->assertEquals($level, $app['monolog.level']);
	}

	public function testEnvLogName() {
		$name = md5('name');

		$_ENV['LOG_NAME'] = $name;

		$app = new Application();

		$this->assertEquals($name, $app['monolog.name']);
	}

	public function testDirectLogFile() {
		$logfile = md5('logfile');

		$config = [
			'monolog.logfile' => $logfile
		];

		$app = new Application($config);

		$this->assertEquals($logfile, $app['monolog.logfile']);
	}

	public function testDirectLogLevel() {
		$level = md5('level');

		$config = [
			'monolog.level' => $level
		];

		$app = new Application($config);

		$this->assertEquals($level, $app['monolog.level']);
	}

	public function testDirectLogName() {
		$name = md5('name');

		$config = [
			'monolog.name' => $name
		];

		$app = new Application($config);

		$this->assertEquals($name, $app['monolog.name']);
	}
}
