<?php

namespace Unit;

use AntonioPrimera\LaraPackager\Components\ConsoleQuestion;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleQuestionTest extends TestCase
{
	/** @test */
	public function a_question_can_be_created()
	{
		$command = new Command();
		$input = new StringInput('abc');
		$output = new ConsoleOutput();
		$question = new ConsoleQuestion($command, $input, $output);
		
		$this->assertInstanceOf(ConsoleQuestion::class, $question);
	}
	
	//ToDo: Properly test questions and commands ... ------------------------------------------------------------------
}