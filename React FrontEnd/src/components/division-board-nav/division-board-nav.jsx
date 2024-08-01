import React from 'react';
import './division-board-nav.css';

const DivisionBoardNav = ({ data, showWinners, highlightWinners }) => {
    return (
        <div className='col-12'>
            <ul className="nav nav-tabs" id="myTab" role="tablist">
                {data ? (
                    data.divisions.map((division, divisionIndex) => (
                        <li className="nav-item" key={divisionIndex}>
                            <a className={`nav-link ${divisionIndex === 0 ? 'active' : ''}`} id={division.name.replace(/\s/g, "") + '-tab'} data-toggle="tab" href={'#' + division.name.replace(/\s/g, "")} role="tab" aria-controls={division.name.replace(/\s/g, "")} aria-selected="true">{division.name}</a>
                        </li>
                    ))
                ) : (
                    <span></span>
                )}
                <li className="nav-item ml-auto">
                    <button
                        className="btn nav-button"
                        onClick={() => showWinners()}
                        disabled = {data ? false : true}
                    >
                        {highlightWinners ? 'Hide Winners' : 'Highlight Winners'}
                    </button>
                </li>
            </ul>
        </div>
    );
}

export default DivisionBoardNav;
