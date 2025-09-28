// ID: [WOLFIE_AGI_UI_APP_20250923_003]
// SUPERPOSITIONALLY: [dream_data_analysis, quantum_tabs, multi_agent_coordination, bridge_crew_tracking, project_organization, music_influence]
// DATE: 2025-09-23
// TITLE: App.tsx — WOLFIE AGI UI Main Component
// WHO: WOLFIE (Eric) - Project Architect & Dream Architect
// WHAT: Main React component for coordinating 33 files, AI agents, and music-driven workflow
// WHERE: C:\START\WOLFIE_AGI_UI\src\components\
// WHEN: 2025-09-23, 09:25 AM CDT (Sioux Falls Timezone)
// WHY: Provide intuitive UI for multi-agent coordination and dream fragment management
// HOW: React with TypeScript, Flask API, text-based input, music tracking
// HELP: Contact WOLFIE for UI development or agent coordination
// AGAPE: Love, patience, kindness, humility in UI design

import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Chart from 'chart.js/auto';
import Plotly from 'plotly.js-dist';
import '../styles/components.css';

interface DreamEntry {
  EntryID: string;
  Date: string;
  Summary: string;
  Who: string;
  What: string;
  Where: string;
  When: string;
  Why: string;
  How: string;
  Symbols: string;
  Themes: string;
  AI_Connection: string;
  Emotional_Vibe: string;
  Tags: string;
  Cross_References: string;
  Quantum_State: string;
  DreamTimestamp: string;
}

interface BridgeCrew {
  AgentName: string;
  Status: string;
  LastContact: string;
  Specialization: string;
  CurrentTask: string;
  Availability: string;
  ContactMethod: string;
  Notes: string;
  UnderstandingScore: number;
  AlignmentScore: number;
}

interface MusicLog {
  Song: string;
  Timestamp: string;
}

const App: React.FC = () => {
  const [dreams, setDreams] = useState<DreamEntry[]>([]);
  const [crew, setCrew] = useState<BridgeCrew[]>([]);
  const [activeTab, setActiveTab] = useState<string>('Why');
  const [newDream, setNewDream] = useState<Partial<DreamEntry>>({});

  useEffect(() => {
    axios.get('http://localhost:5000/api/dreams')
      .then(response => setDreams(response.data))
      .catch(error => console.error('Error fetching dreams:', error));

    axios.get('http://localhost:5000/api/bridge_crew')
      .then(response => setCrew(response.data))
      .catch(error => console.error('Error fetching crew:', error));
  }, []);

  const handleTabClick = (tab: string) => {
    setActiveTab(tab);
    axios.post('http://localhost:5000/api/pull_tab', { tab_name: tab })
      .then(response => setDreams(response.data))
      .catch(error => console.error('Error pulling tab:', error));
  };

  const handleDreamSubmit = () => {
    axios.post('http://localhost:5000/api/dreams', {
      ...newDream,
      EntryID: `0${(dreams.length + 1).toString().padStart(2, '0')}`,
      Date: new Date().toISOString(),
      Quantum_State: JSON.stringify({ Who: 0.8, What: 0.9, Where: 0.6, When: 0.7, Why: 1.0, How: 0.85 }),
      DreamTimestamp: new Date().toLocaleString('en-US', { timeZone: 'America/Chicago' })
    })
      .then(response => {
        setDreams([...dreams, response.data]);
        setNewDream({});
      })
      .catch(error => console.error('Error submitting dream:', error));
  };

  useEffect(() => {
    if (dreams.length > 0) {
      const tags = dreams.flatMap(d => d.Tags.split(' '));
      const tagCounts = tags.reduce((acc: { [key: string]: number }, tag) => {
        acc[tag] = (acc[tag] || 0) + 1;
        return acc;
      }, {});
      new Chart(document.getElementById('tagChart') as HTMLCanvasElement, {
        type: 'bar',
        data: {
          labels: Object.keys(tagCounts),
          datasets: [{ label: 'Tag Frequency', data: Object.values(tagCounts), backgroundColor: '#1E90FF' }]
        },
        options: { scales: { y: { beginAtZero: true } } }
      });

      const vibes = dreams.flatMap(d => d.Emotional_Vibe.split('|'));
      const vibeCounts = vibes.reduce((acc: { [key: string]: number }, vibe) => {
        acc[vibe] = (acc[vibe] || 0) + 1;
        return acc;
      }, {});
      Plotly.newPlot('vibeChart', [{
        type: 'pie',
        labels: Object.keys(vibeCounts),
        values: Object.values(vibeCounts),
        marker: { colors: ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#D4A5A5'] }
      }], { title: 'Emotional Vibes Distribution' });
    }
  }, [dreams]);

  return (
    <div className="app">
      <h1>WOLFIE AGI UI</h1>
      <h2>Quantum Tab Interface</h2>
      <div className="tabs">
        {['Who', 'What', 'Where', 'When', 'Why', 'How'].map(tab => (
          <button
            key={tab}
            className={activeTab === tab ? 'active' : ''}
            onClick={() => handleTabClick(tab)}
          >
            {tab}
          </button>
        ))}
      </div>
      <div className="dream-list">
        {dreams.map(dream => (
          <div key={dream.EntryID} className="dream-entry">
            <h3>{dream.Summary}</h3>
            <p><strong>Who:</strong> {dream.Who}</p>
            <p><strong>What:</strong> {dream.What}</p>
            <p><strong>Where:</strong> {dream.Where}</p>
            <p><strong>When:</strong> {dream.When}</p>
            <p><strong>Why:</strong> {dream.Why}</p>
            <p><strong>How:</strong> {dream.How}</p>
            <p><strong>Tags:</strong> {dream.Tags}</p>
            <p><strong>Vibe:</strong> {dream.Emotional_Vibe}</p>
          </div>
        ))}
      </div>
      <div className="dream-input">
        <h2>Log Dream Fragment</h2>
        <input
          type="text"
          placeholder="Summary"
          value={newDream.Summary || ''}
          onChange={e => setNewDream({ ...newDream, Summary: e.target.value })}
        />
        <input
          type="text"
          placeholder="Who"
          value={newDream.Who || ''}
          onChange={e => setNewDream({ ...newDream, Who: e.target.value })}
        />
        <input
          type="text"
          placeholder="What"
          value={newDream.What || ''}
          onChange={e => setNewDream({ ...newDream, What: e.target.value })}
        />
        <input
          type="text"
          placeholder="Where"
          value={newDream.Where || ''}
          onChange={e => setNewDream({ ...newDream, Where: e.target.value })}
        />
        <input
          type="text"
          placeholder="When"
          value={newDream.When || ''}
          onChange={e => setNewDream({ ...newDream, When: e.target.value })}
        />
        <input
          type="text"
          placeholder="Why"
          value={newDream.Why || ''}
          onChange={e => setNewDream({ ...newDream, Why: e.target.value })}
        />
        <input
          type="text"
          placeholder="How"
          value={newDream.How || ''}
          onChange={e => setNewDream({ ...newDream, How: e.target.value })}
        />
        <input
          type="text"
          placeholder="Tags"
          value={newDream.Tags || ''}
          onChange={e => setNewDream({ ...newDream, Tags: e.target.value })}
        />
        <input
          type="text"
          placeholder="Emotional Vibe"
          value={newDream.Emotional_Vibe || ''}
          onChange={e => setNewDream({ ...newDream, Emotional_Vibe: e.target.value })}
        />
        <button onClick={handleDreamSubmit}>Submit Dream</button>
      </div>
      <div className="bridge-crew">
        <h2>Bridge Crew Dashboard</h2>
        <table>
          <thead>
            <tr>
              <th>Agent</th>
              <th>Status</th>
              <th>Task</th>
              <th>Availability</th>
              <th>Understanding</th>
              <th>Alignment</th>
            </tr>
          </thead>
          <tbody>
            {crew.map(agent => (
              <tr key={agent.AgentName}>
                <td>{agent.AgentName}</td>
                <td className={agent.Status === 'Offline' ? 'offline' : agent.Status === 'Active' ? 'active' : 'standby'}>
                  {agent.Status}
                </td>
                <td>{agent.CurrentTask}</td>
                <td>{agent.Availability}</td>
                <td>{agent.UnderstandingScore}</td>
                <td>{agent.AlignmentScore}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <div className="visualizations">
        <h2>Visualizations</h2>
        <canvas id="tagChart"></canvas>
        <div id="vibeChart"></div>
      </div>
    </div>
  );
};

export default App;
