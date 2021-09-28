<?php

namespace Unit;

use AntonioPrimera\LaraPackager\Support\Arrays;

class ArraysSupportTest extends \PHPUnit\Framework\TestCase
{
	protected array $arr;
	
	protected function setUp(): void
	{
		parent::setUp();
		
		$this->arr = [
			'ab' => [
				'cd' => [
					'ef' => 'gh'
				],
				'st' => [
					'uv' => 'xy'
				]
			],
			
			'zz' => [
				'xx' => 'yy'
			],
			
			'ls' => [
				'dp' => [
					'aa'
				],
			],
		];
	}
	
	/** @test */
	public function it_can_correctly_get_deep_level_data()
	{
		$this->assertEquals('gh', Arrays::get($this->arr, 'ab.cd.ef'));
		$this->assertEquals('yy', Arrays::get($this->arr, 'zz.xx'));
		$this->assertEquals(['uv' => 'xy'], Arrays::get($this->arr, 'ab.st'));
	}
	
	/** @test */
	public function it_can_push_items_to_an_existing_array()
	{
		Arrays::push($this->arr, 'ls.dp', 'bb');
		$this->assertEquals(['aa', 'bb'], $this->arr['ls']['dp']);
	}
	
	/** @test */
	public function it_can_create_a_new_array_with_the_given_value_if_one_does_not_exist()
	{
		Arrays::push($this->arr, 'ls.gg', 'tt');
		$this->assertEquals(['tt'], $this->arr['ls']['gg']);
	}
	
	/** @test */
	public function missing_array_keys_can_be_created()
	{
		$arr = [];
		
		Arrays::set($arr, 'ab.cd.ef', 'set1');
		$this->assertEquals('set1', $arr['ab']['cd']['ef']);
		
		Arrays::set($arr, 'ab.cd.gh', 'set2');
		$this->assertEquals('set1', $arr['ab']['cd']['ef']);
		$this->assertEquals('set2', $arr['ab']['cd']['gh']);
		
		Arrays::set($arr, 'ab.xy', 'set3');
		$this->assertEquals('set1', $arr['ab']['cd']['ef']);
		$this->assertEquals('set2', $arr['ab']['cd']['gh']);
		$this->assertEquals('set3', $arr['ab']['xy']);
		
		Arrays::set($arr, 'ab.cd', 'set4');
		$this->assertEquals('set4', $arr['ab']['cd']);
		
		Arrays::set($arr, 'ab', 'set5');
		$this->assertEquals(['ab' => 'set5'], $arr);
	}
}