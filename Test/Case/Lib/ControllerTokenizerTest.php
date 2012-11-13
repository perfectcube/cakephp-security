<?php

App::uses('ControllerTokenizer', 'Security.Lib');

class ControllerTokenizerTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();

		$this->Tokenizer = new ControllerTokenizer();
	}

	public function tearDown() {
		parent::tearDown();

		unset($this->Tokenizer);
	}

	public function testNoExitsOrDiesAllowed() {
		$this->Tokenizer->setString('<?php die("hi");?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(300, 'die', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php exit("hi");?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(300, 'exit', 1)), $this->Tokenizer->getErrors());
	}

}
