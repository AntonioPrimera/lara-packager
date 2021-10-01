<?php

namespace AntonioPrimera\LaraPackager\Components;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionSet
{
	protected $questions = [];
	protected $command;
	protected $input;
	protected $output;
	protected $debugMode = false;
	
	/**
	 * Enable getting questions by their name
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function __get(string $name)
	{
		return $this->questions[$name] ?? null;
	}
	
	//--- Asking questions --------------------------------------------------------------------------------------------
	
	public function askQuestion(string | ConsoleQuestion $question) : static
	{
		$questionInstance = $question instanceof ConsoleQuestion ? $question : $this->get($question);
		
		if ($questionInstance)
			$questionInstance->ask();
		
		if ($this->debugMode)
			$questionInstance->debug();
		
		return $this;
	}
	
	public function askAll() : static
	{
		foreach ($this->questions as $question)
			$this->askQuestion($question);
		
		return $this;
	}
	
	//--- Question Factory --------------------------------------------------------------------------------------------
	
	public function setupQuestionFactory(Command $command, InputInterface $input, OutputInterface $output) : static
	{
		$this->command = $command;
		$this->input = $input;
		$this->output = $output;
		
		return $this;
	}
	
	public function createQuestion($name) : ConsoleQuestion
	{
		$question = ConsoleQuestion::create($this->command, $this->input, $this->output);
		$this->add($name, $question);
		
		return $question;
	}
	
	//--- Question list management ------------------------------------------------------------------------------------
	
	public function add(string $name, ConsoleQuestion $question) : static
	{
		$this->questions[$name] = $question;
		$question->setQuestionSet($this);
		
		return $this;
	}
	
	public function get(string $name) : ?ConsoleQuestion
	{
		return $this->questions[$name] ?? null;
	}
	
	public function exists(string $name) : bool
	{
		return isset($this->questions[$name]);
	}
	
	public function all() : array
	{
		return $this->questions;
	}
	
	public function remove(string $name) : static
	{
		if ($this->exists($name))
			unset($this->questions[$name]);
		
		return $this;
	}
	
	//--- Public Helpers ----------------------------------------------------------------------------------------------
	
	public function setDebugMode($debugMode = true)
	{
		$this->debugMode = $debugMode;
	}
}