import React, { useState, useEffect, useCallback } from 'react';
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
  FaChartLine
} from 'react-icons/fa';

// Types
interface Agent {
  id: string;
  name: string;
  role: string;
  status: 'online' | 'offline' | 'busy';
  selected: boolean;
}

interface StudyGroup {
  id: string;
  name: string;
  members: Agent[];
  channel: string;
  created: string;
}

interface AssignmentResult {
  agent: string;
  assignment: string;
  result: string;
  score: number;
  timestamp: string;
  mistakes: string[];
  strengths: string[];
}

interface ChatMessage {
  id: string;
  sender: string;
  message: string;
  timestamp: string;
  type: 'user' | 'ai';
}

// Modern API service using fetch instead of XMLHttpRequest
class ModernAPIService {
  private baseURL: string;

  constructor(baseURL: string = '/api') {
    this.baseURL = baseURL;
  }

  async sendMessage(channelId: string, message: string, userId: string): Promise<Response> {
    const response = await fetch(`${this.baseURL}/wolfie_xmlhttp.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        whattodo: 'send',
        channel_id: channelId,
        message: message,
        user_id: userId
      })
    });
    return response;
  }

  async getMessages(channelId: string, htmlTime: number, layerTime: number): Promise<Response> {
    const response = await fetch(`${this.baseURL}/wolfie_xmlhttp.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        whattodo: 'messages',
        channel_id: channelId,
        HTML: htmlTime,
        LAYER: layerTime
      })
    });
    return response;
  }

  async createChannel(userId: string): Promise<Response> {
    const response = await fetch(`${this.baseURL}/wolfie_xmlhttp.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        whattodo: 'create_channel',
        user_id: userId
      })
    });
    return response;
  }

  async getChannels(): Promise<Response> {
    const response = await fetch(`${this.baseURL}/wolfie_xmlhttp.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        whattodo: 'get_channels'
      })
    });
    return response;
  }

  async searchDocs(query: string): Promise<Response> {
    const response = await fetch(`${this.baseURL}/endpoint_handler.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'searchHeaders',
        query: query
      })
    });
    return response;
  }
}

// Custom hooks for modern React patterns
const useAPI = () => {
  const [apiService] = useState(() => new ModernAPIService());
  return apiService;
};

const usePolling = (callback: () => Promise<void>, interval: number = 2100) => {
  useEffect(() => {
    const poll = async () => {
      try {
        await callback();
      } catch (error) {
        console.error('Polling error:', error);
      }
    };

    const intervalId = setInterval(poll, interval);
    return () => clearInterval(intervalId);
  }, [callback, interval]);
};

const useLocalStorage = <T>(key: string, initialValue: T) => {
  const [storedValue, setStoredValue] = useState<T>(() => {
    try {
      const item = window.localStorage.getItem(key);
      return item ? JSON.parse(item) : initialValue;
    } catch (error) {
      console.error(`Error reading localStorage key "${key}":`, error);
      return initialValue;
    }
  });

  const setValue = useCallback((value: T | ((val: T) => T)) => {
    try {
      const valueToStore = value instanceof Function ? value(storedValue) : value;
      setStoredValue(valueToStore);
      window.localStorage.setItem(key, JSON.stringify(valueToStore));
    } catch (error) {
      console.error(`Error setting localStorage key "${key}":`, error);
    }
  }, [key, storedValue]);

  return [storedValue, setValue] as const;
};

// Main Classroom Interface Component
const ClassroomInterface: React.FC = () => {
  const api = useAPI();
  
  // State management with modern React hooks
  const [agents, setAgents] = useState<Agent[]>([
    { id: 'cursor', name: 'CURSOR', role: 'Code Generation', status: 'online', selected: false },
    { id: 'grok', name: 'GROK', role: 'Ideation & Analysis', status: 'online', selected: false },
    { id: 'gemini', name: 'GEMINI', role: 'Research & Documentation', status: 'online', selected: false },
    { id: 'ara', name: 'ARA', role: 'Love & Wisdom', status: 'online', selected: false },
    { id: 'claude', name: 'CLAUDE', role: 'Creative Writing', status: 'offline', selected: false },
    { id: 'deepseek', name: 'DEEPSEEK', role: 'Data Analysis', status: 'online', selected: false },
    { id: 'copilot', name: 'COPILOT', role: 'Development Assistant', status: 'online', selected: false }
  ]);

  const [studyGroups, setStudyGroups] = useLocalStorage<StudyGroup[]>('studyGroups', []);
  const [currentAssignment, setCurrentAssignment] = useState<any>(null);
  const [results, setResults] = useState<AssignmentResult[]>([]);
  const [activeChat, setActiveChat] = useState<string | null>(null);
  const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
  const [assignmentTitle, setAssignmentTitle] = useState('');
  const [assignmentDescription, setAssignmentDescription] = useState('');
  const [assignmentType, setAssignmentType] = useState('creative');

  // Modern polling for real-time updates
  usePolling(async () => {
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
  }, 2100);

  // Event handlers with useCallback for performance
  const toggleAgent = useCallback((agentId: string) => {
    setAgents(prev => prev.map(agent => 
      agent.id === agentId 
        ? { ...agent, selected: !agent.selected }
        : agent
    ));
  }, []);

  const selectAllAgents = useCallback(() => {
    setAgents(prev => prev.map(agent => ({ ...agent, selected: true })));
  }, []);

  const deselectAllAgents = useCallback(() => {
    setAgents(prev => prev.map(agent => ({ ...agent, selected: false })));
  }, []);

  const assignToClass = useCallback(async () => {
    const selectedAgents = agents.filter(agent => agent.selected);
    
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

    setCurrentAssignment(assignment);
    
    // Simulate AI agents working on assignment
    simulateAgentWork(selectedAgents, assignment);
  }, [agents, assignmentTitle, assignmentDescription, assignmentType]);

  const simulateAgentWork = useCallback((selectedAgents: Agent[], assignment: any) => {
    const newResults: AssignmentResult[] = [];
    
    selectedAgents.forEach((agent, index) => {
      setTimeout(() => {
        const result = generateAgentResult(agent, assignment);
        newResults.push(result);
        setResults(prev => [...prev, result]);
      }, (index + 1) * 2000);
    });
  }, []);

  const generateAgentResult = (agent: Agent, assignment: any): AssignmentResult => {
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

  const createStudyGroup = useCallback(() => {
    const selectedAgents = agents.filter(agent => agent.selected);
    if (selectedAgents.length < 2) {
      alert('Please select at least 2 agents for a study group');
      return;
    }

    const groupName = `Study Group ${studyGroups.length + 1}`;
    const studyGroup: StudyGroup = {
      id: `group_${Date.now()}`,
      name: groupName,
      members: selectedAgents,
      channel: `channel_${Date.now()}`,
      created: new Date().toISOString()
    };

    setStudyGroups(prev => [...prev, studyGroup]);
  }, [agents, studyGroups.length, setStudyGroups]);

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
        group.members.forEach((member, index) => {
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

  const generateChatResponse = (agent: Agent, message: string): string => {
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

  const analyzePatterns = useCallback(() => {
    if (results.length === 0) {
      alert('No results to analyze yet. Please assign an assignment first.');
      return;
    }

    // Pattern analysis logic here
    console.log('Analyzing patterns from results:', results);
  }, [results]);

  const runSecondRound = useCallback(() => {
    if (results.length === 0) {
      alert('No results from first round. Please assign an assignment first.');
      return;
    }

    // Run second round with improvements
    console.log('Running second round with pattern-based improvements');
  }, [results]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600">
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-8 p-8 bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-2xl">
          <h1 className="text-4xl font-bold mb-4">
            <FaChalkboardTeacher className="inline mr-3" />
            WOLFIE AGI Classroom
          </h1>
          <p className="text-xl opacity-90">Professor Interface for AI Agent Assignments and Study Groups</p>
        </div>

        {/* Main Grid */}
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
              onClick={createStudyGroup}
              className="w-full mt-4 bg-gradient-to-r from-green-600 to-green-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition-all duration-200 flex items-center justify-center"
            >
              <FaUsers className="mr-2" />
              Create Study Group
            </button>
          </div>
        </div>

        {/* Study Groups Panel */}
        <div className="bg-white rounded-2xl p-6 shadow-xl mb-8">
          <h3 className="text-2xl font-bold mb-6 flex items-center">
            <FaUsersCog className="mr-3 text-purple-600" />
            Study Groups (Channels)
          </h3>
          
          <div className="space-y-4">
            {studyGroups.map((group) => (
              <div key={group.id} className="border border-gray-200 rounded-lg p-4">
                <div className="flex justify-between items-center mb-3">
                  <h4 className="font-semibold text-lg">{group.name}</h4>
                  <div className="space-x-2">
                    <button
                      onClick={() => openGroupChat(group.id)}
                      className="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors"
                    >
                      <FaComments className="inline mr-1" />
                      Chat
                    </button>
                    <button
                      onClick={() => analyzePatterns()}
                      className="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700 transition-colors"
                    >
                      <FaChartLine className="inline mr-1" />
                      Analyze
                    </button>
                  </div>
                </div>
                <div className="flex flex-wrap gap-2">
                  {group.members.map((member) => (
                    <span
                      key={member.id}
                      className="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium"
                    >
                      {member.name}
                    </span>
                  ))}
                </div>
              </div>
            ))}
          </div>
          
          <div className="mt-6 space-x-4">
            <button
              onClick={analyzePatterns}
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
          </div>
        )}

        {/* Chat Panel */}
        {activeChat && (
          <div className="bg-white rounded-2xl p-6 shadow-xl mt-8">
            <h3 className="text-2xl font-bold mb-6 flex items-center">
              <FaComments className="mr-3 text-blue-600" />
              Group Chat
            </h3>
            
            <div className="bg-gray-900 text-green-400 p-4 rounded-lg h-64 overflow-y-auto mb-4 font-mono text-sm">
              {chatMessages.map((msg) => (
                <div key={msg.id} className={`mb-2 p-2 rounded ${
                  msg.type === 'user' ? 'bg-green-900 bg-opacity-30' : 'bg-blue-900 bg-opacity-30'
                }`}>
                  <strong>{msg.sender}:</strong> {msg.message}
                </div>
              ))}
            </div>
            
            <div className="flex space-x-2">
              <input
                type="text"
                placeholder="Type your message..."
                className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                onKeyPress={(e) => {
                  if (e.key === 'Enter') {
                    sendChatMessage(e.currentTarget.value);
                    e.currentTarget.value = '';
                  }
                }}
              />
              <button
                onClick={(e) => {
                  const input = e.currentTarget.previousElementSibling as HTMLInputElement;
                  if (input.value.trim()) {
                    sendChatMessage(input.value);
                    input.value = '';
                  }
                }}
                className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
              >
                Send
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default ClassroomInterface;
