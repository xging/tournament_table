import React from 'react';
import DivisionBoardItems from '../division-board-items/division-board-items';
import DivisionBoardNav from '../division-board-nav/division-board-nav';
import './division-board.css';

const DivisionBoard = ({ data, highlightWinners, showWinners}) => {
    return (
        <div className="row">
            <DivisionBoardNav data={data} showWinners={showWinners} highlightWinners={highlightWinners}/>
            <DivisionBoardItems data={data} highlightWinners={highlightWinners}/>
        </div>
    );
}

export default DivisionBoard;
