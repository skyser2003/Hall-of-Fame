<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once CLASS_BATTLE;

/**
 * $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
 */
class HOF_Class_Battle extends battle
{

	function __construct($team0, $team1)
	{

		$team0 = HOF_Class_Battle_Team::newInstance($team0);
		$team1 = HOF_Class_Battle_Team::newInstance($team1);

		parent::__construct($team0, $team1);

		$this->objs['view'] = new HOF_Class_Battle_View(&$this);

		$this->teams[0]['team'] = &$this->team0;
		$this->teams[1]['team'] = &$this->team1;

		$this->teams[0]['mc'] = &$this->team0_mc;
		$this->teams[1]['mc'] = &$this->team1_mc;

		$this->teams[0]['name'] = &$this->team0_name;
		$this->teams[1]['name'] = &$this->team1_name;

		$this->teams[0]['no'] = TEAM_0;
		$this->teams[1]['no'] = TEAM_1;

		$this->teams[0]['team']->update();
		$this->teams[1]['team']->update();
	}

	function outputImage()
	{
		$output = HOF_Class_Battle_Style::newInstance(BTL_IMG_TYPE)
			->setBg($this->BackGround)
			->setTeams($this->team1, $this->team0)
			->setMagicCircle($this->team1_mc, $this->team0_mc)
			->exec();

		echo $output;
	}

	function SkillEffect($skill, $skill_no, &$user, &$target)
	{
		if (!isset($this->objs['SkillEffect']))
		{
			$this->objs['SkillEffect'] = new HOF_Class_Skill_Effect(&$this);
		}

		return $this->objs['SkillEffect']->SkillEffect($skill, $skill_no, &$user, &$target);
	}

	/**
	 * 魔方陣を追加する
	 *
	 * @param bool|$del 魔方陣を削除する
	 */
	function changeMagicCircle($team, $amount, $del = 0)
	{
		$amount *= ($del ? -1 : 1);

		if ($team == TEAM_0)
		{
			$team_mc = &$this->team0_mc;
		}
		else
		{
			$team_mc = &$this->team1_mc;
		}

		if ($del)
		{
			if ($team_mc < $amount) return false;
		}

		$team_mc += $amount;

		$team_mc = abs(max(0, min(5, $team_mc)));

		return true;
	}

	/**
	 * 指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
	 */
	function CountDead($who)
	{
		return HOF_Class_Battle_Team::CountDead($who);
	}

	/**
	 * 全体の死者数を数える...(ネクロマンサしか使ってない?)
	 */
	function CountDeadAll()
	{
		$count = 0;

		$count += HOF_Class_Battle_Team::CountDead($this->team0);
		$count += HOF_Class_Battle_Team::CountDead($this->team1);

		return $count;
	}

	/**
	 * 戦闘にキャラクターを途中参加させる。
	 *
	 * @param HOF_Class_Char|$user
	 * @param HOF_Class_Char|$add
	 */
	function JoinCharacter($user, $add)
	{
		foreach($this->teams as &$team)
		{
			foreach ($team['team'] as $char)
			{
				if ($user === $char)
				{
					$team['team']->addChar($add, $team['no']);
					$this->ChangeDelay();

					return true;
				}
			}
		}
	}

	/**
	 * 戦闘記録を保存する
	 */
	function RecordLog($type = false)
	{
		if ($type == "RANK")
		{
			$file = LOG_BATTLE_RANK;
			$log = game_core::glob(LOG_BATTLE_RANK);
			$logAmount = MAX_BATTLE_LOG_RANK;
		}
		else
			if ($type == "UNION")
			{
				$file = LOG_BATTLE_UNION;
				$log = game_core::glob(LOG_BATTLE_UNION);
				$logAmount = MAX_BATTLE_LOG_UNION;
			}
			else
			{
				$file = LOG_BATTLE_NORMAL;
				$log = game_core::glob(LOG_BATTLE_NORMAL);
				$logAmount = MAX_BATTLE_LOG;
			}

			// 古いログを消す
			$i = 0;
		while ($logAmount <= count($log))
		{
			unlink($log["$i"]);
			unset($log["$i"]);
			$i++;
		}

		// 新しいログを作る
		$time = time() . substr(microtime(), 2, 6);
		$file .= $time . ".dat";

		$head = $time . "\n"; //開始時間(1行目)
		$head .= $this->team0_name . "<>" . $this->team1_name . "\n"; //参加チーム(2行目)
		$head .= count($this->team0) . "<>" . count($this->team1) . "\n"; //参加人数(3行目)
		$head .= $this->team0_ave_lv . "<>" . $this->team1_ave_lv . "\n"; //平均レベル(4行目)
		$head .= $this->result . "\n"; //勝利チーム(5行目)
		$head .= $this->actions . "\n"; //総ターン数(6行目)
		$head .= "\n"; // 改行(7行目)

		HOF_Class_File::WriteFile($file, $head . ob_get_contents());
	}

}
