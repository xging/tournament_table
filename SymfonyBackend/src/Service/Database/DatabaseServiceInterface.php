<?PHP
namespace App\Service\Database;

use App\Entity\Divisions;
use App\Entity\Teams;
use App\Entity\Matches;
use App\Entity\DivisionWinners;
use App\Entity\PlayoffMatches;
use App\Entity\PlayoffResults;
use App\Entity\QuarterfinalWinners;
use App\Entity\SemifinalWinners;
use App\Entity\SemifinalLoosers;
use App\Entity\IndexCountTemp;

interface DatabaseServiceInterface
{
    public function addDivision(string $name, int $divisionId): ?Divisions;
    public function addTeam(string $name, Divisions $division, string $shortname, bool $pickedFlag, int $teamId, int $result): ?Teams;
    public function addMatch(int $division_id, int $team1Id, int $team2Id, string $result): ?Matches;
    public function addDivisionWinners(int $divisionId, int $teamId, string $teamName, string $result, $pickedFlag): ?DivisionWinners;
    public function getTeamIdByShortName(string $shortName): ?int;
    public function getTeamByShortName(string $shortName): ?string;

    public function addPlayoffMatchesRes(string $stage, string $team1, string $team2, int $score1, int $score2, string $group): ?PlayoffMatches;
    public function addQuarterfinalWinners($teamName, $pickedFlag): ?QuarterfinalWinners;
    public function addPlayoffResults(string $looser, int $place): ?PlayoffResults;
    public function addSemifinalWinners($teamName, $pickedFlag): ?SemifinalWinners;
    public function addSemifinalLoosers($teamName, $pickedFlag): ?SemifinalLoosers;

    public function clearTable(string $tableName): void;


    public function getPickedFlagByShortName(string $shortName): ?bool;
    public function setPickedFlagByTeamShortName(string $shortName, bool $flag): ?Teams;
    public function getTeamIdByPickedFlag(bool $flag): ?int;
    public function getShortNameByPickedFlag(bool $flag, int $divisionId): ?array;

    public function addIndexId(int $id, string $stage): ?IndexCountTemp;
    public function getIndexId(string $stage): ?int;
    public function setIndexId(int $value, string $stage): ?IndexCountTemp;
    public function setMatchResult(string $shortName, string $value): ?Teams;
    public function getMatchResult(string $shortName): ?int;

    public function setDWPickedFlagByName(string $name, bool $flag): ?DivisionWinners;
    public function setQWPickedFlagByName(string $name, bool $flag): ?QuarterfinalWinners;
    public function setSLPickedFlagByName(string $name, bool $flag): ?SemifinalLoosers;
    public function setSWPickedFlagByName(string $name, bool $flag): ?SemifinalWinners;

    public function getMatchResultByName(string $Name): ?int;
    public function setMatchResultByName(string $name, string $value): ?Teams;
    public function getDivisions(string $name): ?Divisions;
}