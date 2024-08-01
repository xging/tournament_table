import React, { useState } from "react";
import "./matchup-items.css";

const transformData = (data, groupStage) => {
  const matches = Object.keys(data[groupStage]).map(groupKey => {
    const group = data[groupStage][groupKey];
    const teams = Object.keys(group).map(team => ({
      name: team,
      score: group[team]
    }));

    return {
      team1: teams[0],
      team2: teams[1]
    };
  });

  return matches;
};

const MatchupItems = ({ quarterfinalData }) => {
  const [highlight, setHighlight] = useState(false);

  if (!quarterfinalData || Object.keys(quarterfinalData).length === 0) {
    return <div>Loading...</div>;
  }

  const quarterFinalMatches = transformData(quarterfinalData, 'Quarterfinal');
  const semiFinalMatches = transformData(quarterfinalData, 'Semifinal');
  const bronzeMatches = transformData(quarterfinalData, 'BronzeMedal');
  const grandFinalMatches = transformData(quarterfinalData, 'Grandfinal');

  const toggleHighlight = () => {
    setHighlight(!highlight);
  };

  return (
    <div className="col-9">
      <button className ="tgl-btn py-2 px-2 mb-2" onClick={toggleHighlight}>Highlight Winners</button>
      <div className="bracket">
        <section className="round round__quarterfinals">
          <div className="winners">
            <div className="matchups">
              <h5>Quarterfinal</h5>
              {quarterFinalMatches.map((match, index) => (
                <div className="matchup" key={index}>
                  <div className="participants">
                    <div className={`participant ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team1.name}</span>
                      <div className={`bracket__result ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team1.score}</span>
                      </div>
                    </div>
                    <div className={`participant ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team2.name}</span>
                      <div className={`bracket__result ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team2.score}</span>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            <div className="connectors">
              <div className="connector__one">
                <div className="merger"></div>
                <div className="line"></div>
              </div>
              <div className="connector__two">
                <div className="merger"></div>
                <div className="line"></div>
              </div>
            </div>
          </div>
        </section>

        <section className="round round__semifinals">
          <div className="winners">
            <div className="matchups">
              <h5>Semifinal</h5>
              {semiFinalMatches.map((match, index) => (
                <div className="matchup" key={index}>
                  <div className="participants">
                    <div className={`participant ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team1.name}</span>
                      <div className={`bracket__result ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team1.score}</span>
                      </div>
                    </div>
                    <div className={`participant ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team2.name}</span>
                      <div className={`bracket__result ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team2.score}</span>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            <div className="bracket__connector--three">
              <div className="merger"></div>
            </div>
          </div>
        </section>

        <section className="round round__finals ml-3">
          <div className="winners">
            <div className="matchups">
              <h5>Final</h5>
              {grandFinalMatches.map((match, index) => (
                <div className="matchup" key={index}>
                  <div className="participants">
                    <div className={`participant ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team1.name}</span>
                      <div className={`bracket__result ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team1.score}</span>
                      </div>
                    </div>
                    <div className={`participant ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team2.name}</span>
                      <div className={`bracket__result ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team2.score}</span>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
              <h5>Third-place match</h5>
              {bronzeMatches.map((match, index) => (
                <div className="matchup" key={index}>
                  <div className="participants">
                    <div className={`participant ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team1.name}</span>
                      <div className={`bracket__result ${highlight && match.team1.score > match.team2.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team1.score}</span>
                      </div>
                    </div>
                    <div className={`participant ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                      <span>{match.team2.name}</span>
                      <div className={`bracket__result ${highlight && match.team2.score > match.team1.score ? 'participant__winner' : 'participant__loser'}`}>
                        <span>{match.team2.score}</span>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </div>
    </div>
  );
};

export default MatchupItems;
