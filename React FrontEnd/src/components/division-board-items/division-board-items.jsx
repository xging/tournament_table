import React, { useState } from 'react';
import './division-board-items.css';

const DivisionBoardItems = ({ data, highlightWinners, showWinners}) => {
    const renderTableRows = (teams, matches) => {
        return teams.map((team, rowIndex) => {
            const results = matches[rowIndex][team];
            const totalWins = Object.values(results).filter(result => parseInt(result.split(':')[0]) > parseInt(result.split(':')[1])).length;
            const totalPoints = Object.values(results).reduce((acc, val) => acc + parseInt(val.split(':')[0]), 0);
            return (
                <tr key={team}>
                    <td className={`team-name ${highlightWinners && checkWinner(team)}`} style={{ borderRight: '2px solid #fff' }}>{team}</td>
                    {teams.map((opponent, colIndex) => {
                        const matchResult = rowIndex === colIndex ? '' : results[opponent];
                        return (
                            <td key={colIndex} className={`white-out ${rowIndex === colIndex ? 'grey-out ' : highlightWinners && checkWinner(team)}`}>
                                {matchResult}
                            </td>
                        );
                    })}
                    <td className={`match-result ${highlightWinners && checkWinner(team)}`}>{totalWins}</td>
                </tr>
            );
        });
    };

    const checkWinner = (teamName) => {
        const isWinner = data.divisions.some(division =>
            division.winners.includes(teamName)
        );
        return isWinner ? 'winner' : '';
    };

    return (
        <div className="col-md-12">
            <div className="tab-content" id="myTabContent">
                {data ? (
                    data.divisions.map((division, divisionIndex) => (
                        <div key={divisionIndex} className={`tab-pane fade ${divisionIndex === 0 ? 'show active' : ''}`} id={division.name.replace(/\s/g, "")} role="tabpanel" aria-labelledby={division.name.replace(/\s/g, "")}>
                            <table className="table table-bordered table-hover">
                                <thead className="thead-light">
                                    <tr>
                                        <th style={{ borderRight: '2px solid #3a5583' }}>Teams</th>
                                        {division.teams.map((team, teamIndex) => (
                                            <th key={teamIndex} className='team-name'>{team}</th>
                                        ))}
                                        <th style={{ borderLeft: '2px solid #3a5583' }}>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {renderTableRows(division.teams, division.matches)}
                                </tbody>
                            </table>
                        </div>
                    ))
                ) : (
                    <div className='tab-pane fade show active' id="" role="tabpanel" aria-labelledby="">
                        <table className="table table-bordered table-hover">
                            <thead className="thead-light">
                                <tr>
                                    <th style={{ borderRight: '2px solid #3a5583' }}>Teams</th>
                                    {[...Array(8)].map((_, index) => (
                                        <th key={index} className='team-name'>{'Team '+index}</th>
                                    ))}
                                    <th style={{ borderLeft: '2px solid #3a5583' }}>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                            {[...Array(8)].map((_, index) => (
                                <tr>
                                    <td className='team-name' style={{ borderRight: '2px solid #fff' }}>{'Team '+index}</td>
                                    {[...Array(8)].map((_, index) => (
                                        <td key={index} className='white-out'>0</td>
                                    ))}
                                    <td className='match-result'>0</td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

export default DivisionBoardItems;
