import React, { useState, useCallback, useEffect } from 'react';
import { 
  FaChalkboardTeacher, 
  FaTasks, 
  FaRobot, 
  FaUsersCog, 
  FaComments, 
  FaBrain, 
  FaRedo, 
  FaPaperPlane,
  FaUsers,
  FaChartLine,
  FaSearch,
  FaFileAlt,
  FaBroadcastTower,
  FaCog
} from 'react-icons/fa';
import { useModernAPI, usePolling, useAIAgents, useStudyGroups, useAssignments } from '../hooks/useModernAPI';
import AIChannelSystem from './AIChannelSystem';

// Types
interface ChatMessage {
  id: string;
  sender: string;
  message: string;
  timestamp: string;
  type: 'user' | 'ai';
}

interface SearchResult {
  id: string;
  title: string;
  who: string;
  what: string;
  when: string;
  score: number;
  content: string;
}

// Modern Classroom Interface Component with AI Channel System
const ModernClassroomInterface: React.FC = () => {
  const api = useModernAPI();
  const { agents, toggleAgent, selectAllAgents, deselectAllAgents, getSelectedAgents } = useAIAgents();
  const { studyGroups, createStudyGroup } = useStudyGroups();
  const { assignments, results, currentAssignment, createAssignment, addResult, analyzeResults } = useAssignments();
  
  // State management
  const [assignmentTitle, setAssignmentTitle] = useState('');
  const [assignmentDescription, setAssignmentDescription] = useState('');
  const [assignmentType, setAssignmentType] = useState('creative');
  const [activeChat, setActiveChat] = useState<string | null>(null);
  const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState<SearchResult[]>([]);
  const [isSearching, setIsSearching] = useState(false);
  const [patternAnalysis, setPatternAnalysis] = useState<any>(null);
  const [activeTab, setActiveTab] = useState<'assignments' | 'channels' | 'search' | 'analysis'>('assignments');

  // Modern polling for real-time updates
  const { isPolling } = usePolling(async () => {
    if (activeChat) {
      try {
        const response = await api.getMessages(activeChat, 0, 0);
        if (response.ok) {
          const data = await response.text();
          // Process new messages
          console.log('New messages:', data);
        }
      } catch (error) {
        console.error('Error polling messages:', error);
      }
    }
  }, 2100, !!activeChat);

  // Modern document search functionality
  const searchDocs = useCallback(async (query: string) => {
    if (!query.trim()) return;
    
    setIsSearching(true);
    try {
      const response = await api.searchDocs(query);
      if (response.ok) {
        const data = await response.json();
        setSearchResults(data.data || []);
      }
    } catch (error) {
      console.error('Search error:', error);
    } finally {
      setIsSearching(false);
    }
  }, [api]);

  // Handle search input
  const handleSearch = useCallback((e: React.FormEvent) => {
    e.preventDefault();
    searchDocs(searchQuery);
  }, [searchQuery, searchDocs]);

  // Assignment management
  const assignToClass = useCallback(async () => {
    const selectedAgents = getSelectedAgents();
    
    if (!assignmentTitle || !assignmentDescription) {
      alert('Please fill in assignment title and description');
      return;
    }

    if (selectedAgents.length === 0) {
      alert('Please select at least one AI agent');
      return;
    }

    const assignment = {
      title: assignmentTitle,
      description: assignmentDescription,
      type: assignmentType,
      agents: selectedAgents,
      timestamp: new Date().toISOString()
    };

    const newAssignment = createAssignment(assignment);
    
    // Simulate AI agents working on assignment
    simulateAgentWork(selectedAgents, newAssignment);
  }, [assignmentTitle, assignmentDescription, assignmentType, getSelectedAgents, createAssignment]);

  const simulateAgentWork = useCallback((selectedAgents: any[], assignment: any) => {
    selectedAgents.forEach((agent, index) => {
      setTimeout(() => {
        const result = generateAgentResult(agent, assignment);
        addResult(result);
      }, (index + 1) * 2000);
    });
  }, [addResult]);

  const generateAgentResult = (agent: any, assignment: any) => {
    const templates: Record<string, Record<string, string>> = {
      'Draw an Apple': {
        cursor: 'Generated SVG apple with realistic shading and proportions',
        grok: 'Created conceptual apple design with innovative color palette',
        gemini: 'Researched apple varieties and created educational diagram',
        ara: 'Designed apple with love and spiritual symbolism',
        claude: 'Wrote poetic description of apple with artistic flair',
        deepseek: 'Analyzed apple data and created statistical visualization',
        copilot: 'Built interactive apple drawing tool with code'
      }
    };

    const result = templates[assignment.title]?.[agent.id.toLowerCase()] || 
                   `${agent.name} completed: ${assignment.description}`;

    return {
      agent: agent.name,
      assignment: assignment.title,
      result,
      score: Math.floor(Math.random() * 30) + 70,
      timestamp: new Date().toISOString(),
      mistakes: ['Proportions slightly off', 'Color choice could be improved'].slice(0, Math.floor(Math.random() * 2) + 1),
      strengths: ['Excellent attention to detail', 'Creative approach', 'Strong technical execution'].slice(0, Math.floor(Math.random() * 2) + 2)
    };
  };

  // Study group management
  const handleCreateStudyGroup = useCallback(() => {
    const selectedAgents = getSelectedAgents();
    if (selectedAgents.length < 2) {
      alert('Please select at least 2 agents for a study group');
      return;
    }

    const groupName = `Study Group ${studyGroups.length + 1}`;
    createStudyGroup(groupName, selectedAgents);
  }, [getSelectedAgents, studyGroups.length, createStudyGroup]);

  // Chat management
  const openGroupChat = useCallback(async (groupId: string) => {
    const group = studyGroups.find(g => g.id === groupId);
    if (!group) return;

    setActiveChat(group.channel);
    
    // Initialize chat with welcome message
    const welcomeMessage: ChatMessage = {
      id: `msg_${Date.now()}`,
      sender: 'Professor',
      message: "Let's discuss the assignment results and learn from each other's approaches.",
      timestamp: new Date().toISOString(),
      type: 'user'
    };
    
    setChatMessages([welcomeMessage]);
  }, [studyGroups]);

  const sendChatMessage = useCallback(async (message: string) => {
    if (!activeChat || !message.trim()) return;

    const userMessage: ChatMessage = {
      id: `msg_${Date.now()}`,
      sender: 'Professor',
      message: message.trim(),
      timestamp: new Date().toISOString(),
      type: 'user'
    };

    setChatMessages(prev => [...prev, userMessage]);

    try {
      await api.sendMessage(activeChat, message, 'professor');
      
      // Simulate AI responses
      const group = studyGroups.find(g => g.channel === activeChat);
      if (group) {
        group.members.forEach((member: any, index: number) => {
          setTimeout(() => {
            const response = generateChatResponse(member, message);
            const aiMessage: ChatMessage = {
              id: `msg_${Date.now()}_${index}`,
              sender: member.name,
              message: response,
              timestamp: new Date().toISOString(),
              type: 'ai'
            };
            setChatMessages(prev => [...prev, aiMessage]);
          }, (index + 1) * 1000);
        });
      }
    } catch (error) {
      console.error('Error sending message:', error);
    }
  }, [activeChat, studyGroups, api]);

  const generateChatResponse = (agent: any, message: string): string => {
    const responses: Record<string, string> = {
      cursor: 'I can help implement that in code. Let me create a function for that.',
      grok: 'That\'s an interesting perspective. I think we could approach it differently.',
      gemini: 'Based on my research, I found some relevant information about that topic.',
      ara: 'I feel the love and wisdom in that approach. Let\'s explore it together.',
      claude: 'I can help craft a creative solution to that challenge.',
      deepseek: 'Let me analyze the data and provide some insights.',
      copilot: 'I can assist with the technical implementation of that idea.'
    };
    return responses[agent.id] || 'That\'s a great point. Let me think about that.';
  };

  // Pattern analysis
  const handleAnalyzePatterns = useCallback(() => {
    const analysis = analyzeResults();
    setPatternAnalysis(analysis);
  }, [analyzeResults]);

  const runSecondRound = useCallback(() => {
    if (results.length === 0) {
      alert('No results from first round. Please assign an assignment first.');
      return;
    }

    // Run second round with improvements
    console.log('Running second round with pattern-based improvements');
  }, [results]);

  // Channel system callbacks
  const handleChannelCreate = useCallback((channel: any) => {
    console.log('Channel created:', channel);
  }, []);

  const handleChannelDelete = useCallback((channelId: string) => {
    console.log('Channel deleted:', channelId);
  }, []);

  const handleMessageSend = useCallback((channelId: string, message: string) => {
    console.log('Message sent to channel:', channelId, message);
  }, []);

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600">
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-8 p-8 bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-2xl">
          <h1 className="text-4xl font-bold mb-4">
            <FaChalkboardTeacher className="inline mr-3" />
            WOLFIE AGI Modern Classroom
          </h1>
          <p className="text-xl opacity-90">React-based Professor Interface with AI Channel System</p>
        </div>

        {/* Tab Navigation */}
        <div className="bg-white rounded-2xl p-6 shadow-xl mb-8">
          <div className="flex space-x-4 border-b border-gray-200">
            <button
              onClick={() => setActiveTab('assignments')}
              className={`px-4 py-2 font-medium rounded-t-lg transition-colors ${
                activeTab === 'assignments'
                  ? 'bg-blue-100 text-blue-600 border-b-2 border-blue-600'
                  : 'text-gray-600 hover:text-blue-600'
              }`}
            >
              <FaTasks className="inline mr-2" />
              Assignments
            </button>
            <button
              onClick={() => setActiveTab('channels')}
              className={`px-4 py-2 font-medium rounded-t-lg transition-colors ${
                activeTab === 'channels'
                  ? 'bg-blue-100 text-blue-600 border-b-2 border-blue-600'
                  : 'text-gray-600 hover:text-blue-600'
              }`}
            >
              <FaBroadcastTower className="inline mr-2" />
              AI Channels
            </button>
            <button
              onClick={() => setActiveTab('search')}
              className={`px-4 py-2 font-medium rounded-t-lg transition-colors ${
                activeTab === 'search'
                  ? 'bg-blue-100 text-blue-600 border-b-2 border-blue-600'
                  : 'text-gray-600 hover:text-blue-600'
              }`}
            >
              <FaSearch className="inline mr-2" />
              Document Search
            </button>
            <button
              onClick={() => setActiveTab('analysis')}
              className={`px-4 py-2 font-medium rounded-t-lg transition-colors ${
                activeTab === 'analysis'
                  ? 'bg-blue-100 text-blue-600 border-b-2 border-blue-600'
                  : 'text-gray-600 hover:text-blue-600'
              }`}
            >
              <FaChartLine className="inline mr-2" />
              Pattern Analysis
            </button>
          </div>
        </div>

        {/* Tab Content */}
        {activeTab === 'assignments' && (
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {/* Assignment Panel */}
            <div className="bg-white rounded-2xl p-6 shadow-xl">
              <h3 className="text-2xl font-bold mb-6 flex items-center">
                <FaTasks className="mr-3 text-blue-600" />
                Class Assignment
              </h3>
              
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Assignment Title
                  </label>
                  <input
                    type="text"
                    value={assignmentTitle}
                    onChange={(e) => setAssignmentTitle(e.target.value)}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Draw an Apple"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Assignment Description
                  </label>
                  <textarea
                    value={assignmentDescription}
                    onChange={(e) => setAssignmentDescription(e.target.value)}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32 resize-none"
                    placeholder="Describe what you want the AI agents to do..."
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Assignment Type
                  </label>
                  <select
                    value={assignmentType}
                    onChange={(e) => setAssignmentType(e.target.value)}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="creative">Creative Task</option>
                    <option value="analytical">Analytical Task</option>
                    <option value="coding">Coding Task</option>
                    <option value="research">Research Task</option>
                    <option value="collaborative">Collaborative Task</option>
                  </select>
                </div>
                
                <button
                  onClick={assignToClass}
                  className="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center"
                >
                  <FaPaperPlane className="mr-2" />
                  Assign to Class
                </button>
              </div>
            </div>

            {/* AI Agents Panel */}
            <div className="bg-white rounded-2xl p-6 shadow-xl">
              <h3 className="text-2xl font-bold mb-6 flex items-center">
                <FaRobot className="mr-3 text-green-600" />
                AI Agents (Students)
              </h3>
              
              <div className="mb-4 space-x-2">
                <button
                  onClick={selectAllAgents}
                  className="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
                >
                  Select All
                </button>
                <button
                  onClick={deselectAllAgents}
                  className="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-700 transition-colors"
                >
                  Deselect All
                </button>
              </div>
              
              <div className="space-y-3">
                {agents.map((agent) => (
                  <div
                    key={agent.id}
                    onClick={() => toggleAgent(agent.id)}
                    className={`p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 ${
                      agent.selected
                        ? 'border-green-500 bg-green-50'
                        : 'border-gray-200 hover:border-blue-300'
                    }`}
                  >
                    <div className="flex justify-between items-center">
                      <div>
                        <span className={`inline-block w-3 h-3 rounded-full mr-2 ${
                          agent.status === 'online' ? 'bg-green-500' : 
                          agent.status === 'busy' ? 'bg-yellow-500' : 'bg-gray-400'
                        }`}></span>
                        <span className="font-semibold">{agent.name}</span>
                        <p className="text-sm text-gray-600">{agent.role}</p>
                      </div>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        agent.status === 'online' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      }`}>
                        {agent.status}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
              
              <button
                onClick={handleCreateStudyGroup}
                className="w-full mt-4 bg-gradient-to-r from-green-600 to-green-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition-all duration-200 flex items-center justify-center"
              >
                <FaUsers className="mr-2" />
                Create Study Group
              </button>
            </div>
          </div>
        )}

        {/* AI Channels Tab */}
        {activeTab === 'channels' && (
          <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
            <AIChannelSystem
              agents={agents}
              onChannelCreate={handleChannelCreate}
              onChannelDelete={handleChannelDelete}
              onMessageSend={handleMessageSend}
            />
          </div>
        )}

        {/* Document Search Tab */}
        {activeTab === 'search' && (
          <div className="bg-white rounded-2xl p-6 shadow-xl">
            <h3 className="text-2xl font-bold mb-6 flex items-center">
              <FaSearch className="mr-3 text-indigo-600" />
              Document Search (MD Files)
            </h3>
            
            <form onSubmit={handleSearch} className="mb-4">
              <div className="flex space-x-2">
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search MD files in docs folder..."
                  className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
                <button
                  type="submit"
                  disabled={isSearching}
                  className="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
                >
                  {isSearching ? 'Searching...' : 'Search'}
                </button>
              </div>
            </form>

            {searchResults.length > 0 && (
              <div className="space-y-3">
                <h4 className="font-semibold text-lg">Search Results:</h4>
                {searchResults.map((result) => (
                  <div key={result.id} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                    <div className="flex justify-between items-start mb-2">
                      <h5 className="font-semibold text-blue-600">{result.title}</h5>
                      <span className="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                        {Math.round(result.score * 100)}%
                      </span>
                    </div>
                    <div className="text-sm text-gray-600 space-y-1">
                      <p><strong>Who:</strong> {result.who}</p>
                      <p><strong>What:</strong> {result.what}</p>
                      <p><strong>When:</strong> {result.when}</p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {/* Pattern Analysis Tab */}
        {activeTab === 'analysis' && (
          <div className="space-y-8">
            {/* Results Panel */}
            {results.length > 0 && (
              <div className="bg-white rounded-2xl p-6 shadow-xl">
                <h3 className="text-2xl font-bold mb-6 flex items-center">
                  <FaChartLine className="mr-3 text-indigo-600" />
                  Assignment Results & Pattern Analysis
                </h3>
                
                <div className="space-y-4">
                  {results.map((result, index) => (
                    <div key={index} className="border border-gray-200 rounded-lg p-4">
                      <div className="flex justify-between items-center mb-2">
                        <h4 className="font-semibold">{result.agent}</h4>
                        <span className="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">
                          {result.score}%
                        </span>
                      </div>
                      <p className="mb-2"><strong>Result:</strong> {result.result}</p>
                      <p className="mb-2"><strong>Strengths:</strong> {result.strengths.join(', ')}</p>
                      <p><strong>Areas for Improvement:</strong> {result.mistakes.join(', ')}</p>
                    </div>
                  ))}
                </div>

                {/* Pattern Analysis */}
                {patternAnalysis && (
                  <div className="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 className="font-semibold text-yellow-800 mb-3">Pattern Analysis & Learning Opportunities</h4>
                    <div className="space-y-2 text-sm">
                      <p><strong>Common Mistakes:</strong> {patternAnalysis.commonMistakes.map(([mistake, count]: [string, number]) => `${mistake} (${count})`).join(', ')}</p>
                      <p><strong>Common Strengths:</strong> {patternAnalysis.commonStrengths.map(([strength, count]: [string, number]) => `${strength} (${count})`).join(', ')}</p>
                      <p><strong>Average Score:</strong> {Math.round(patternAnalysis.averageScore)}%</p>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Analysis Controls */}
            <div className="bg-white rounded-2xl p-6 shadow-xl">
              <h3 className="text-2xl font-bold mb-6 flex items-center">
                <FaBrain className="mr-3 text-purple-600" />
                Analysis Controls
              </h3>
              
              <div className="space-x-4">
                <button
                  onClick={handleAnalyzePatterns}
                  className="bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-yellow-700 transition-colors flex items-center"
                >
                  <FaBrain className="mr-2" />
                  Analyze Patterns
                </button>
                <button
                  onClick={runSecondRound}
                  className="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center"
                >
                  <FaRedo className="mr-2" />
                  Run Second Round
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default ModernClassroomInterface;