import React, { useState, useEffect } from 'react';
import axios from 'axios';
import './App.css';
import DivisionBoard from '../division-board/division-board';
import Matchup from '../matchup/matchup';
import Navbar from '../navbar/navbar';

function App() {
  const [data, setData] = useState(null);
  const [quarterfinalData, setQuarterFinalData] = useState(null);
  const [finalResultsData, setFinalResultsData] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);
  const [highlightWinners, setHighlightWinners] = useState(false);


const showWinners = () => {
  setHighlightWinners(!highlightWinners);
  
}


const fetchData = async () => {
  try {
    setLoading(true);
    const response = await axios.get('http://127.0.0.1:8000/api/get-match-data');
    setData(response.data);
  } catch (error) {
    console.error('Error fetching the JSON data:', error);
    setError(error.message);
  } finally {
    setLoading(false);
    setQuarterFinalData([]);
    setFinalResultsData();
  }
};

useEffect(() => {
}, []); 


const fetchQuarterfinals = async () => {
  try {
    setLoading(true);
    const response = await axios.get('http://127.0.0.1:8000/api/get-playoff-data');
    setQuarterFinalData(response.data);
  } catch (error) {
    console.error('Error fetching the JSON data:', error);
    setError(error.message);
  } finally {
    setLoading(false);
    setFinalResultsData();
  }
};
useEffect(() => {
  // fetchQuarterfinals();
}, []); 


const fetchFinalResults = async () => {
  try {
    setLoading(true);
    const response = await axios.get('http://127.0.0.1:8000/api/get-playoff-data');
    setFinalResultsData(response.data.Results);
  } catch (error) {
    console.error('Error fetching the JSON data:', error);
    setError(error.message);
  } finally {
    setLoading(false);
  }
};

useEffect(() => {
  // fetchFinalResults();
}, []);

  return (
    <>
    <Navbar fetchData = {fetchData} fetchQuarterfinals = {fetchQuarterfinals} fetchFinalResults={fetchFinalResults}/>
    <div className="container mt-5 tournament-table App">
      <div className="mb-5" style={{maxWidth:'532px', margin:'0 auto'}}>
        <h1 className="text-header text-center archivo-black-regular">TOURNAMENT TABLE</h1>
      </div>
      <DivisionBoard data={data} highlightWinners={highlightWinners} showWinners={showWinners}/>
      <Matchup quarterfinalData={quarterfinalData} finalResultsData={finalResultsData}/>
    </div>
    </>
  );
}

export default App;
