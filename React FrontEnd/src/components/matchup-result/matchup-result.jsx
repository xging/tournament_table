import React from 'react';
import './matchup-result.css';

const extractNames = (data) => {
  return Object.keys(data);
};

const MatchupResult = ({ finalResultsData }) => {
  console.log('Received data:', finalResultsData); 

  if (!finalResultsData) {
    return <p>No results data available.</p>;
  }

  const teamNames = extractNames(finalResultsData);

  return (
    <div className="col-3 result">
      <h5>Results</h5>
      <div className="result-list">
        <ul>
          {teamNames.map((name, index) => (
            <li key={index}>{name}</li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default MatchupResult;
