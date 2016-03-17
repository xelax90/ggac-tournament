<?php
namespace GGACTournament\Tournament\RoundCreator;

use GGACTournament\Entity\Team;

interface AlreadyPlayedInterface{
	public function alreadyPlayed(Team $t1 = null, Team $t2 = null);
}
