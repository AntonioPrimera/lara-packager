<?php

namespace AntonioPrimera\LaraPackager\Components;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @property $questionText;
 * @property $defaultValue;
 * @property $isYesNoQuestion;
 * @property $answer;
 * @property $answerIsDefault;
 * @property $condition;
 * @property $example
 * @property $context
 * @property $questionSet
 * @property $conditionMet
 * @property $wasAsked
 * @property $answeredYes
 */
class ConsoleQuestion
{
	
	protected Command $command;
	protected InputInterface $input;
	protected OutputInterface $output;
	
	//properties set at question setup
	protected $questionText    = null;
	protected $defaultValue    = null;
	protected $yesNoQuestion   = false;
	protected $condition	   = null;
	protected $example		   = null;
	
	//properties set at question asking time
	protected $conditionMet	   = null;
	protected $asked		   = false;
	protected $answer		   = null;
	protected $answerIsDefault = null;
	
	//properties linking to outside contexts
	/**
	 * Any context data used in evaluating callable
	 * conditions / questions / default values
	 *
	 * @var null
	 */
	protected $context		   = null;
	
	/**
	 * If the question is part of a question set
	 * this will be stored here
	 *
	 * @var null | QuestionSet
	 */
	protected $questionSet	   = null;
	
	public function __construct(Command $command, InputInterface $input, OutputInterface $output)
	{
		$this->command = $command;
		$this->input = $input;
		$this->output = $output;
		
		//$helper = $command->getHelper('question');
		//$question = new Question($questionData['question'], $defaultValue);
	}
	
	public static function create(Command $command, InputInterface $input, OutputInterface $output) : static
	{
		return new static($command, $input, $output);
	}
	
	public function __get(string $name)
	{
		if (in_array($name, ['questionText', 'defaultValue', 'answer', 'condition', 'example', 'context', 'questionSet']))
			return call_user_func([$this, 'get' . ucfirst($name)]);
		
		if (in_array($name, ['isYesNoQuestion','answerIsDefault', 'wasAsked', 'conditionMet', 'answeredYes']))
			return $this->$name();
		
		return null;
	}
	
	//--- Question engine ---------------------------------------------------------------------------------------------
	
	public function ask($question = null, $default = null)
	{
		//check if there is a condition and if the condition is met
		$this->conditionMet = $this->condition === null || $this->evaluate($this->condition);
		if (!$this->conditionMet) {
			$this->asked = false;
			$this->answer = null;
			$this->answerIsDefault = false;
			return null;
		}
		
		$helper = $this->command->getHelper('question');
		$defaultValue = $this->evaluate($this->defaultValue);
		$question = new Question(
			$this->evaluate($question) ?: $this->getQuestionText(false),
			$this->evaluate($default) ?: $defaultValue
		);
		$this->answer = $helper->ask($this->input, $this->output, $question);
		
		$this->asked = true;
		$this->answerIsDefault = $this->answer === $defaultValue;
	}
	
	//--- Getters & Setters -------------------------------------------------------------------------------------------
	
	/**
	 * @return null
	 */
	public function getQuestionText($raw = false)
	{
		if ($raw)
			return $this->questionText;
		
		$text = trim($this->evaluate($this->questionText));
		$default = $this->evaluate($this->defaultValue);
		
		//add the yes/no option if necessary
		$yesNo = $this->yesNoQuestion
			? ' [' . (strtolower($default) === 'y' ? 'Y' : 'y') . '/' . (strtolower($default) === 'n' ? 'N' : 'n') . ']'
			: '';
		$text .= $this->isYesNoQuestion() ? $yesNo : '';
		
		//for non-yes-no questions, add the default, if any
		if (!$this->yesNoQuestion)
			$text .= $default ? " [$default]" : '';
		
		//add the example if no default value is present
		if (!$default)
			$text .= $this->example
				? ' (e.g. ' . $this->evaluate($this->example) . ')'
				: '';
		
		return $text . ' ';
	}
	
	/**
	 * @param string|callable $questionText
	 *
	 * @return ConsoleQuestion
	 */
	public function text(string | callable $questionText) : static
	{
		$this->questionText = $questionText;
		return $this;
	}
	
	/**
	 * @return null
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}
	
	/**
	 * @param string|int|callable|bool $defaultValue
	 *
	 * @return ConsoleQuestion
	 */
	public function defaultValue(string | int | callable | bool | null $defaultValue) : static
	{
		$this->defaultValue = $defaultValue;
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isYesNoQuestion() : bool
	{
		return $this->yesNoQuestion;
	}
	
	/**
	 * @param bool $yesNoQuestion
	 *
	 * @return ConsoleQuestion
	 */
	public function yesNoQuestion(bool $yesNoQuestion = true) : static
	{
		$this->yesNoQuestion = $yesNoQuestion;
		return $this;
	}
	
	/**
	 * @return null
	 */
	public function getAnswer()
	{
		return $this->yesNoQuestion
			? stripos($this->answer, 'y') !== false
			: $this->answer;
	}
	
	public function getRawAnswer()
	{
		return $this->answer;
	}
	
	/**
	 * @return bool|null
	 */
	public function answerIsDefault()
	{
		return $this->answerIsDefault;
	}
	
	/**
	 * @return null
	 */
	public function getCondition()
	{
		return $this->condition;
	}
	
	/**
	 * @param null $condition
	 */
	public function condition($condition) : static
	{
		$this->condition = $condition;
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function wasAsked() : bool
	{
		return $this->asked;
	}
	
	/**
	 * @return bool|null
	 */
	public function conditionMet() : bool | null
	{
		return $this->conditionMet;
	}
	
	/**
	 * @return null
	 */
	public function getExample()
	{
		return $this->example;
	}
	
	/**
	 * @param null $example
	 */
	public function example($example) : static
	{
		$this->example = $example;
		return $this;
	}
	
	/**
	 * @return null
	 */
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * @param null $context
	 */
	public function context($context) : static
	{
		$this->context = $context;
		return $this;
	}
	
	/**
	 * @return QuestionSet|null
	 */
	public function getQuestionSet() : ?QuestionSet
	{
		return $this->questionSet;
	}
	
	/**
	 * @param QuestionSet|null $questionSet
	 *
	 * @return ConsoleQuestion
	 */
	public function setQuestionSet(?QuestionSet $questionSet) : static
	{
		$this->questionSet = $questionSet;
		return $this;
	}
	
	public function answeredYes()
	{
		return $this->yesNoQuestion ? $this->getAnswer() : false;
	}
	
	//--- Public helpers ----------------------------------------------------------------------------------------------
	
	public function addToQuestionSet(QuestionSet $questionSet, string $name)
	{
		$questionSet->add($name, $this);
		return $this;
	}
	
	public function debug()
	{
		$this->output->writeln([
			' ',
			'--- DEBUG ---------------------------------------------',
			' ',
			'defaultValue: ' . $this->evaluate($this->defaultValue),
			'yesNoQuestion: ' . ($this->yesNoQuestion ? 'true' : 'false'),
			'example: ' . $this->evaluate($this->example),
			'conditionMet: ' . $this->outputDebugData($this->conditionMet),
			'asked: ' . $this->outputDebugData($this->asked),
			'answer: ' . $this->answer,
			'answeredYes: ' . $this->outputDebugData($this->answeredYes()),
			'answerIsDefault: ' . $this->outputDebugData($this->answerIsDefault),
			' ',
			'--- DEBUG END -----------------------------------------',
			' ',
			//protected $condition	   = null;
			//
			////properties set at question asking time
			//protected $conditionMet	   = null;
			//protected $asked		   = false;
			//protected $answer		   = null;
			//protected $answerIsDefault = null;
		]);
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function evaluate($data)
	{
		return is_callable($data) ? $data($this->context, $this->questionSet) : $data;
	}
	
	protected function outputDebugData($data)
	{
		if (is_bool($data))
			return $data ? 'true' : 'false';
		
		if ($data === null)
			return 'NULL';
		
		if (is_string($data) || is_numeric($data))
			return $data;
		
		return json_encode($data);
	}
}