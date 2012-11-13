<?php

App::uses('ViewTokenizer', 'Security.Lib');

class ViewTokenizerTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();

		$this->Tokenizer = new ViewTokenizer();
	}

	public function tearDown() {
		parent::tearDown();

		unset($this->Tokenizer);
	}

	public function testProperEscapedValues() {
		$this->Tokenizer->setString('<?= h($variable);?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php echo h($variable);?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php print h($variable);?>');
		$this->assertTrue($this->Tokenizer->check());
	}

	public function testViewHelpersIsAsumedSafe() {
		$this->Tokenizer->setString('<?= $this->Html->link();?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php echo $this->Html->link();?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php print $this->Html->link();?>');
		$this->assertTrue($this->Tokenizer->check());
	}

	public function testDirectEchoOfvariableFails() {
		$this->Tokenizer->setString('<?= $variable;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $variable;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php print $variable;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());
	}

	public function testNestedFunctionCallsWorks() {
		$this->Tokenizer->setString('<?= h(__($variable));?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php echo h(__($variable));?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php print h(__($variable));?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= __(h($variable));?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php echo __(h($variable));?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?php print __(h($variable));?>');
		$this->assertTrue($this->Tokenizer->check());
	}

	public function testNestedFunctionCallsFails() {
		$this->Tokenizer->setString('<?= x(__($variable));?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo x(__($variable));?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?= __(x($variable));?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo __(x($variable));?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$variable', 1)), $this->Tokenizer->getErrors());
	}

	public function testConcatStaticString() {
		$this->Tokenizer->setString('<?= h($hest) . "hello";?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= "hello" . h($hest);?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= $hest . "hello";?>');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= "hello" . $hest;?>');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= "hello. cupcake"; ?>');
		$this->assertTrue($this->Tokenizer->check());
	}

	public function testConcatVariable() {
		$this->Tokenizer->setString('<?= "hello" . h($safeVariable) . h($unsafeVariable);?>');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= $this->Html->link() . h($variable2)');
		$this->assertTrue($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= "hello" . h($safeVariable) . $unsafeVariable;?>');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= $this->Html->link() . $variable2');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= $variable1 . $variable2');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= $variable1 . h($variable2)');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= h($variable1) . $variable2');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= h($variable1) . h($variable2)');
		$this->assertTrue($this->Tokenizer->check());
	}

	public function testInlineVariables() {
		$this->Tokenizer->setString('<?= "hello $unsafeVariable";?>');
		$this->assertFalse($this->Tokenizer->check());

		$this->Tokenizer->setString('<?= "hello {$unsafeVariable}";?>');
		$this->assertFalse($this->Tokenizer->check());
	}

	public function testMultipleEchoArguments() {
		$this->Tokenizer->setString('<?php echo $a, $b, $c;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$b', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $a, $b, h($c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$b', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $a, h($b), h($c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo h($a), h($b), h($c);?>');
		$this->assertTrue($this->Tokenizer->check());
		$this->assertEquals(array(), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo h($a), $b, h($c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$b', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo h($a), $b, $c;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$b', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $a, h($b), $c;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo($a, h($b), $c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo(($a, h($b), $c));?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo((($a, h($b), $c)));?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $a, h($b), $c;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());
	}

	public function testRawFunctionWhitelist() {
		$this->Tokenizer->setString('<?php echo $a, $b, raw($c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$b', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $a, raw($b), raw($c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo raw($a), raw($b), raw($c);?>');
		$this->assertTrue($this->Tokenizer->check());
		$this->assertEquals(array(), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo raw($a), $b, raw($c);?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$b', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo raw($a), $b, $c;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$b', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php echo $a, raw($b), $c;?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(309, '$a', 1), array(309, '$c', 1)), $this->Tokenizer->getErrors());
	}

	public function testNoExitsOrDiesAllowed() {
		$this->Tokenizer->setString('<?php die("hi");?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(300, 'die', 1)), $this->Tokenizer->getErrors());

		$this->Tokenizer->setString('<?php exit("hi");?>');
		$this->assertFalse($this->Tokenizer->check());
		$this->assertEquals(array(array(300, 'exit', 1)), $this->Tokenizer->getErrors());
	}

	public function testChunks() {
		$str = '<li><a href="<?php echo $this->Html->url(array("controller" => "asset_folders", "action" => "add", $current["AssetFolder"]["id"])); ?>" class="button button-gray"><span class="add"></span>Create folder</a></li>';
		$this->Tokenizer->setString($str);
		$this->assertTrue($this->Tokenizer->check());
	}
}
