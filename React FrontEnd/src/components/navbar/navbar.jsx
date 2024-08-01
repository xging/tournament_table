import React from 'react';
import NavbarItems from '../navbar-items/navbar-items';
import './navbar.css';

const Navbar = ({fetchData, fetchQuarterfinals, fetchFinalResults}) => {
    return (
        <div className="container position-fixed navbar-container">
            <NavbarItems fetchData={fetchData} fetchQuarterfinals={fetchQuarterfinals} fetchFinalResults={fetchFinalResults}/>
      </div>
    );
}

export default Navbar;
