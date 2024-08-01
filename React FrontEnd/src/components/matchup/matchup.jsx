import React from 'react';
import MatchupItems from '../matchup-items/matchup-items';
import MatchupResult from '../matchup-result/matchup-result';
import './matchup.css';

const Matchup = ({quarterfinalData, finalResultsData}) => {
    return (
        <div className="row my-5 pb-5">
            <MatchupItems quarterfinalData={quarterfinalData} />
            <MatchupResult finalResultsData={finalResultsData}/>
        </div>
    );
}

export default Matchup;
