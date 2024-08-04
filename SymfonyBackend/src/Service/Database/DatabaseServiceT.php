<?PHP
namespace App\Service\Database;

use App\Entity\Divisions;
use App\Entity\Teams;
use App\Entity\Matches;
use App\Entity\DivisionWinners;
use App\Entity\PlayoffMatches;
use App\Entity\QuarterfinalWinners;
use App\Entity\PlayoffResults;
use App\Entity\SemifinalWinners;
use App\Entity\SemifinalLoosers;
use App\Entity\IndexCountTemp;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Repository\Divisions\DivisionsRepository;

class DatabaseServiceT implements DatabaseServiceInterface
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function addDivision(string $name, int $divisionId): ?Divisions
    {
        $division = new Divisions();
        $division->setName($name);
        $division->setDivisionId($divisionId);
        $this->em->persist($division);
        $this->em->flush();

        return $division;
    }


    public function getDivisions(string $name): ?Divisions
    {
        $division = $this->em->getRepository(Divisions::class)->findOneBy(['name' => $name]);
        return $division ? $division: null;
    }

    public function addTeam(string $name, Divisions $division, string $shortname, bool $pickedFlag, int $teamId, int $result): ?Teams
    {
        if ($division === null) {
            throw new \InvalidArgumentException('Division cannot be null.');
        }

        $team = new Teams();
        $team->setName($name);
        $team->setShortname($shortname);
        $team->setDivision($division);
        $team->setPickedFlag($pickedFlag);
        $team->setTeamId($teamId);
        $team->setResult($result);
        $this->em->persist($team);
        $this->em->flush();

        return $team;
    }

    public function addMatch(int $division_id, int $team1Id, int $team2Id, string $result): ?Matches
    {
        $match = new Matches();
        $match->setDivisionId($division_id);
        $match->setTeam1Id($team1Id);
        $match->setTeam2Id($team2Id);
        $match->setResult($result);
        $this->em->persist($match);
        $this->em->flush();
        return $match;
    }

    public function addDivisionWinners(int $divisionId, int $teamId, string $teamName, string $result, $pickedFlag): ?DivisionWinners
    {
        $divisionWinners = new DivisionWinners();
        $divisionWinners->setDivisionId($divisionId);
        $divisionWinners->setTeamId($teamId);
        $divisionWinners->setTeamName($teamName);
        $divisionWinners->setResult($result);
        $divisionWinners->setPickedFlag($pickedFlag);
        $this->em->persist($divisionWinners);


        $this->logger->info("Persisting division winner: " . json_encode([
            'divisionId' => $divisionId,
            'teamID' => $teamId,
            'name' => $teamName,
            'score' => $result,
            'status' => $pickedFlag
        ]));

        $this->em->flush();
        return $divisionWinners;
    }

    public function addPlayoffMatchesRes($groupStageName, $teamName1, $teamName2, $teamScore1, $teamScore2, $groupName): ?PlayoffMatches
    {
        $playoffMatches = new PlayoffMatches();
        $playoffMatches->setGroupStage($groupStageName);
        $playoffMatches->setTeamName1($teamName1);
        $playoffMatches->setTeamName2($teamName2);
        $playoffMatches->setTeamScore1($teamScore1);
        $playoffMatches->setTeamScore2($teamScore2);
        $playoffMatches->setGroupName($groupName);
        $this->em->persist($playoffMatches);
        $this->em->flush();
        return $playoffMatches;
    }

    public function addQuarterfinalWinners($teamName, $pickedFlag): ?QuarterfinalWinners
    {
        $quarterfinalWinners = new QuarterfinalWinners();
        $quarterfinalWinners->setTeamName($teamName);
        $quarterfinalWinners->setPickedFlag($pickedFlag);
        $this->em->persist($quarterfinalWinners);
        $this->em->flush();
        return $quarterfinalWinners;
    }

    public function addPlayoffResults($teamName, $place): ?PlayoffResults
    {
        $playoffResults = new PlayoffResults();
        $playoffResults->setTeamName($teamName);
        $playoffResults->setPlace($place);
        $this->em->persist($playoffResults);
        $this->em->flush();
        return $playoffResults;
    }


    public function addSemifinalWinners($teamName, $pickedFlag): ?SemifinalWinners
    {
        $semifinalWinners = new SemifinalWinners();
        $semifinalWinners->setTeamName($teamName);
        $semifinalWinners->setPickedFlag($pickedFlag);
        $this->em->persist($semifinalWinners);
        $this->em->flush();
        return $semifinalWinners;
    }

    public function addSemifinalLoosers($teamName, $pickedFlag): ?SemifinalLoosers
    {
        $semifinalLoosers = new SemifinalLoosers();
        $semifinalLoosers->setTeamName($teamName);
        $semifinalLoosers->setPickedFlag($pickedFlag);
        $this->em->persist($semifinalLoosers);
        $this->em->flush();
        return $semifinalLoosers;
    }

    public function  getTeamIdByShortName(string $shortName): ?int
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $shortName]);
        return $team ? $team->getTeamId() : null;
    }

    public function getTeamByShortName(string $shortName): ?string
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $shortName]);
        return $team ? $team->getName(): null;
    }

    public function getTeamByPickedFlag(bool $flag): array
    {
        $teams = $this->em->getRepository(Teams::class)->findBy([
            'pickedFlag' => $flag
        ]);
    
        $this->logger->warning(sprintf("Flag here2: %s.", $flag ? 'true' : 'false'));
    
        return $teams;
    }

    public function getTeamIdByName(string $name): ?int
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $name]);
        return $team ? $team->getTeamId() : null;
    }


    public function getDWByDivisionId(int $divisionId): array
    {
        $divisionWinner = $this->em->getRepository(DivisionWinners::class)->findBy([
            'division_id' => $divisionId
        ]);
    
        return $divisionWinner;
    }

    public function clearTable(string $tableName): void
    {
        $conn = $this->em->getConnection();
        
        if($tableName === 'divisions') {
            $conn->executeStatement('DELETE FROM ' . $tableName);
        } else {
            $conn->executeStatement('TRUNCATE TABLE ' . $tableName);
        }
    }

    public function getPickedFlagByShortName(string $shortName): ?bool
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $shortName]);
        return $team ? $team->isPickedFlag() : null;
    }

    public function setPickedFlagByTeamShortName(string $shortName, bool $flag): ?Teams
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $shortName]);
        $this->logger->warning("Team with short name {$shortName}.");
        if ($team) {
            $team->setPickedFlag($flag);
            $this->em->flush();
            $this->logger->warning("Team with short name {$shortName} found.");
        } else {
            $this->logger->warning("Team with short name {$shortName} not found.");
        }
    
        return $team;
    }

    public function getTeamIdByPickedFlag(bool $flag): ?int
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['pickedFlag' => $flag]);
        $this->logger->warning("Flag here: {$flag}.");
        return $team ? $team->getTeamId() : null;
    }
    
    public function getShortNameByPickedFlag(bool $flag, int $divisionId): ?array
    {
        $teams = $this->em->getRepository(Teams::class)->findBy([
            'pickedFlag' => $flag,
            'divisionId' => $divisionId,
        ]);
        $this->logger->warning("Flag here: {$flag}, Division ID: {$divisionId}.");
    
        if (empty($teams)) {
            return [];
        }
    
        $shortNames = array_map(function($team) {
            return $team->getShortName();
        }, $teams);
    
        return $shortNames;
    }
    
    public function addIndexId(int $id,string $stage): ?IndexCountTemp
    {
        $indexCountTemp = new IndexCountTemp();
        $indexCountTemp->setIndexId($id);
        $indexCountTemp->setStage($stage);
        $this->em->persist($indexCountTemp);
        $this->em->flush();

        return $indexCountTemp;
    }

    public function getIndexId(string $stage): ?Int
    {
        $indexCountTemp = $this->em->getRepository(IndexCountTemp::class)->findOneBy(['stage' => $stage]);

        return $indexCountTemp ? $indexCountTemp->getIndexId() : null;
    }

    public function setIndexId(int $value, string $stage): ?IndexCountTemp
    {
        $indexCountTemp = $this->em->getRepository(IndexCountTemp::class)->findOneBy(['stage' => $stage]);

        if ($indexCountTemp) {
            $indexCountTemp->setIndexId($value);
            $this->em->flush(); 
            $this->logger->warning("Index update to {$value} found.");
        } else {
            $this->logger->warning("Index not updated to {$value} not found.");
        }
    
        return $indexCountTemp;
    }

    public function setMatchResult(string $shortName, string $value): ?Teams
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $shortName]);
        $this->logger->warning("Team with short name {$shortName}.");
        if ($team) {
            $team->setResult($value);
            $this->em->flush(); 
            $this->logger->warning("Team with short name {$shortName} found.");
        } else {
            $this->logger->warning("Team with short name {$shortName} not found.");
        }
    
        return $team;
    }

    public function setMatchResultByName(string $name, string $value): ?Teams
    {
        $team = $this->em->getRepository(Teams::class)->findOneBy(['name' => $name]);
        $this->logger->warning("Team with short name {$name}.");
        if ($team) {
            $team->setResult($value);
            $this->em->flush(); 
            $this->logger->warning("Team with short name {$name} found.");
        } else {
            $this->logger->warning("Team with short name {$name} not found.");
        }
    
        return $team;
    }

    public function getMatchResult(string $shortName): ?Int
    {
        $teams= $this->em->getRepository(Teams::class)->findOneBy(['shortname' => $shortName]);

        return $teams ? $teams->getResult() : null;
    }

    public function getMatchResultByName(string $Name): ?Int
    {
        $teams= $this->em->getRepository(Teams::class)->findOneBy(['name' => $Name]);

        return $teams ? $teams->getResult() : null;
    }


    public function setDWPickedFlagByName(string $name, bool $flag): ?DivisionWinners
{
    try {

        if (!$this->em) {
            $this->logger->error('EntityManager не инициализирован.');
            return null;
        }

        $divisionWinners = $this->em->getRepository(DivisionWinners::class)->findOneBy(['team_name' => $name]);

        $this->logger->info("Попытка найти команду с именем {$name}.");

        if ($divisionWinners) {
            $divisionWinners->setPickedFlag($flag);
            $this->em->flush(); 

            $this->logger->info("Команда с именем {$name} найдена и флаг обновлен.");
        } else {
            $this->logger->warning("Команда с именем {$name} не найдена.");
        }

        return $divisionWinners;

    } catch (\Exception $e) {
        $this->logger->error('Произошла ошибка: ' . $e->getMessage());
        return null;
    }
}


public function setQWPickedFlagByName(string $name, bool $flag): ?QuarterfinalWinners
{
    try {
        if (!$this->em) {
            $this->logger->error('EntityManager не инициализирован.');
            return null;
        }

        $quarterfinalWinners = $this->em->getRepository(QuarterfinalWinners::class)->findOneBy(['teamName' => $name]);

        $this->logger->info("Quarterfinal Попытка найти команду с именем {$name}.");

        if ($quarterfinalWinners) {
            $quarterfinalWinners->setPickedFlag($flag);
            $this->em->flush();

            $this->logger->info("Quarterfinal Команда с именем {$name} найдена и флаг обновлен.");
        } else {
            $this->logger->warning("Quarterfinal Команда с именем {$name} не найдена.");
        }

        return $quarterfinalWinners;

    } catch (\Exception $e) {
        $this->logger->error('Quarterfinal Произошла ошибка: ' . $e->getMessage());
        return null;
    }
    }


    public function setSLPickedFlagByName(string $name, bool $flag): ?SemifinalLoosers
{
    try {
        if (!$this->em) {
            $this->logger->error('EntityManager не инициализирован.');
            return null;
        }

        $semifinalLoosers = $this->em->getRepository(SemifinalLoosers::class)->findOneBy(['team_name' => $name]);

        $this->logger->info("SemifinalLoosersПопытка найти команду с именем {$name}.");

        if ($semifinalLoosers) {
            $semifinalLoosers->setPickedFlag($flag);
            $this->em->flush();

            $this->logger->info("SemifinalLoosers Команда с именем {$name} найдена и флаг обновлен.");
        } else {
            $this->logger->warning("SemifinalLoosersКоманда с именем {$name} не найдена.");
        }

        return $semifinalLoosers;

    } catch (\Exception $e) {
        $this->logger->error('SemifinalLoosers Произошла ошибка: ' . $e->getMessage());
        return null;
    }
    }


    public function setSWPickedFlagByName(string $name, bool $flag): ?SemifinalWinners
    {
        try {
            if (!$this->em) {
                $this->logger->error('EntityManager не инициализирован.');
                return null;
            }
            $semifinalWinners = $this->em->getRepository(SemifinalWinners::class)->findOneBy(['team_name' => $name]);
    
            $this->logger->info("semifinalWinners Попытка найти команду с именем {$name}.");
    
            if ($semifinalWinners) {
                $semifinalWinners->setPickedFlag($flag);
                $this->em->flush();
    
                $this->logger->info("semifinalWinners Команда с именем {$name} найдена и флаг обновлен.");
            } else {
                $this->logger->warning("semifinalWinners Команда с именем {$name} не найдена.");
            }
    
            return $semifinalWinners;
    
        } catch (\Exception $e) {
            $this->logger->error('semifinalWinners Произошла ошибка: ' . $e->getMessage());
            return null;
        }
    }

    public function setDWResultById(int $teamId, int $res): ?DivisionWinners
    {
        $dw = $this->em->getRepository(DivisionWinners::class)->findOneBy(['team_id' => $teamId]);
        $dw->setResult($res);
        $this->em->flush(); 
        return $dw;
    }

    
    public function getDWResultById(int $teamId): ?Int
    {
        $dw = $this->em->getRepository(DivisionWinners::class)->findOneBy(['team_id' => $teamId]);
        return $dw ? $dw->getTeamId() : null;
    }


}
