import React, { useState } from "react";
import axios from "axios";
import "./navbar-items.css";

const NavbarItems = ({ fetchData, fetchQuarterfinals, fetchFinalResults }) => {
  const [loadingMatch, setLoadingMatch] = useState(false);
  const [loadingSingleMatch, setLoadingSingleMatch] = useState(false);
  const [loadingQuarter, setLoadingQuarter] = useState(false);
  const [loadingResults, setLoadingResults] = useState(false);
  const [loadingPlayoff,  setLoadingPlayoff] = useState(false);
  const [matchFlag, setMatchFlag] = useState(true);
  const [resultFlag, setResultFlag] = useState(true);

  const getFullMatches = async (event) => {
    event.preventDefault();
    setLoadingMatch(true);

    try {
      const response = await axios.post(
        "http://127.0.0.1:8000/api/generate-match-data"
      );
      console.log(response.data);
      fetchData();
      // alert("Match data generated!");
    } catch (error) {
      console.error("There was an error generating the match!", error);
      // alert("Failed to generate match. Please try again.");
    } finally {
      setLoadingMatch(false);
      setLoadingSingleMatch(true);
      setLoadingPlayoff(true);
      setMatchFlag(false);
    }
  };


  const getSingleMatch = async (event) => {
    event.preventDefault();
    setLoadingSingleMatch(true);

    try {
      const response = await axios.post(

        "http://127.0.0.1:8000/api/create-single-match"
      );
      console.log(response.data);
      fetchData();
      // alert("Match data generated!");
    } catch (error) {
      console.error("There was an error generating the match!", error);
      // alert("Failed to generate match. Please try again.");
    } finally {
      setLoadingSingleMatch(false);
      setLoadingMatch(true);
      setLoadingQuarter(true);
      setMatchFlag(false);
    }
  };

  const getQuarterfinals = async (event) => {
    event.preventDefault();
    setLoadingQuarter(true);

    try {
      const response = await axios.post(
        "http://127.0.0.1:8000/api/generate-playoff-data"
      );
      console.log(response.data);
      fetchQuarterfinals();
      // alert("Playoff data generated!");
    } catch (error) {
      console.error("There was an error generating the playoff match!", error);
      // alert("Failed to generate playoff match. Please try again.");
    } finally {
      setLoadingQuarter(false);
      setLoadingResults(false);
      setResultFlag(false);
    }
  };

  const getSinglePlayoff = async (event) => {
    event.preventDefault();
    setLoadingPlayoff(true);

    try {
      const response = await axios.post(
        "http://127.0.0.1:8000/api/generate-playoff-single-data"
      );
      console.log(response.data);
      fetchQuarterfinals();
      // alert("Playoff data generated!");
    } catch (error) {
      console.error("There was an error generating the playoff match!", error);
      // alert("Failed to generate playoff match. Please try again.");
    } finally {
      setLoadingPlayoff(false);
      setLoadingResults(false);
      setResultFlag(false);
    }
  };

  const getResults = async (event) => {
    event.preventDefault();
    setLoadingResults(true);

    try {
      await fetchFinalResults();
      // alert("Results generated!");
    } catch (error) {
      console.error("There was an error generating the results match!", error);
      // alert("Failed to generate results match. Please try again.");
    } finally {
      setLoadingResults(false);
    }
  };

  return (
    <nav className="navbar navbar-expand-lg navbar-light bg-light">
      <ul className="button-list py-0 px-0 my-0 mx-0">
        <li>
        {" "}
          <button
            className="btn btn-primary btn-customized"
            onClick={getFullMatches}
            disabled={loadingMatch}
          >
            {!matchFlag ? "Generate Division Matches" : loadingMatch ? "Generating..." : "Generate Division Matches"}
          </button>
        </li>
        <li className="mr-3">
        {" "}
          <button
            className="btn btn-primary btn-customized"
            onClick={getQuarterfinals}
            disabled={matchFlag || loadingQuarter}
          >
            {!matchFlag ? "Generate Playoff Matches" : loadingQuarter ? "Generating..." :"Generate Playoff Matches"}
          </button>
        </li>
        <li>
        {" "}
          <button
            className="btn btn-primary btn-customized"
            onClick={getSingleMatch}
            disabled={loadingSingleMatch}
          >
            {/* {loadingSingleMatch ? "Generating..." : "Generate Single Match"} */}
            {!matchFlag ? "Generate Single Match" : loadingPlayoff ? "Generating..." : "Generate Single Match"}
          </button>
        </li>
        <li className="mr-3">
        {" "}
          <button
            className="btn btn-primary btn-customized"
            onClick={getSinglePlayoff}
            disabled={matchFlag || loadingPlayoff}
          >
        {!matchFlag ? "Generate Single Playoff" : loadingPlayoff ? "Generating..." : "Generate Single Playoff"}
          </button>
        </li>
        <li>
        {" "}
          <button
            className="btn btn-primary btn-customized"
            onClick={getResults}
            disabled={resultFlag}
          >
            {loadingResults ? "Generating..." : "Get Results"}
          </button>
        </li>
      </ul>
    </nav>
  );
};

export default NavbarItems;
