import { useState, useCallback, useRef, useEffect } from 'react';

// Modern API service using fetch instead of XMLHttpRequest
export class ModernAPIService {
  private baseURL: string;
  private abortController: AbortController | null = null;

  constructor(baseURL: string = '/api') {
    this.baseURL = baseURL;
  }

  // Modern fetch-based message sending
  async sendMessage(channelId: string, message: string, userId: string): Promise<Response> {
    // Cancel previous request if still pending
    if (this.abortController) {
      this.abortController.abort();
    }
    
    this.abortController = new AbortController();
    
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
      }),
      signal: this.abortController.signal
    });
    
    return response;
  }

  // Modern fetch-based message retrieval
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

  // Modern fetch-based channel creation
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

  // Modern fetch-based channel listing
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

  // Modern fetch-based document search
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

  // Modern fetch-based AI agent assignment
  async assignToAgents(assignment: any, agentIds: string[]): Promise<Response> {
    const response = await fetch(`${this.baseURL}/ai_assignment.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'assign',
        assignment: assignment,
        agent_ids: agentIds
      })
    });
    
    return response;
  }

  // Modern fetch-based pattern analysis
  async analyzePatterns(results: any[]): Promise<Response> {
    const response = await fetch(`${this.baseURL}/pattern_analysis.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'analyze',
        results: results
      })
    });
    
    return response;
  }

  // Cancel any pending requests
  cancelRequests(): void {
    if (this.abortController) {
      this.abortController.abort();
      this.abortController = null;
    }
  }
}

// Custom hook for modern API usage
export const useModernAPI = (baseURL?: string) => {
  const [apiService] = useState(() => new ModernAPIService(baseURL));
  
  // Cleanup on unmount
  useEffect(() => {
    return () => {
      apiService.cancelRequests();
    };
  }, [apiService]);

  return apiService;
};

// Custom hook for real-time polling with modern patterns
export const usePolling = (
  callback: () => Promise<void>, 
  interval: number = 2100,
  enabled: boolean = true
) => {
  const intervalRef = useRef<NodeJS.Timeout | null>(null);
  const callbackRef = useRef(callback);
  const [isPolling, setIsPolling] = useState(false);

  // Update callback ref when callback changes
  useEffect(() => {
    callbackRef.current = callback;
  }, [callback]);

  useEffect(() => {
    if (!enabled) {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
        setIsPolling(false);
      }
      return;
    }

    const poll = async () => {
      try {
        setIsPolling(true);
        await callbackRef.current();
      } catch (error) {
        console.error('Polling error:', error);
      } finally {
        setIsPolling(false);
      }
    };

    // Start polling
    intervalRef.current = setInterval(poll, interval);
    
    // Initial poll
    poll();

    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
        setIsPolling(false);
      }
    };
  }, [interval, enabled]);

  return { isPolling };
};

// Custom hook for WebSocket connections (modern alternative to XMLHttpRequest polling)
export const useWebSocket = (url: string, enabled: boolean = true) => {
  const [socket, setSocket] = useState<WebSocket | null>(null);
  const [isConnected, setIsConnected] = useState(false);
  const [lastMessage, setLastMessage] = useState<any>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!enabled) return;

    const ws = new WebSocket(url);
    
    ws.onopen = () => {
      setIsConnected(true);
      setError(null);
    };

    ws.onmessage = (event) => {
      try {
        const data = JSON.parse(event.data);
        setLastMessage(data);
      } catch (err) {
        setLastMessage(event.data);
      }
    };

    ws.onclose = () => {
      setIsConnected(false);
    };

    ws.onerror = (error) => {
      setError('WebSocket connection error');
      console.error('WebSocket error:', error);
    };

    setSocket(ws);

    return () => {
      ws.close();
    };
  }, [url, enabled]);

  const sendMessage = useCallback((message: any) => {
    if (socket && isConnected) {
      socket.send(JSON.stringify(message));
    }
  }, [socket, isConnected]);

  return {
    socket,
    isConnected,
    lastMessage,
    error,
    sendMessage
  };
};

// Custom hook for managing AI agent states
export const useAIAgents = () => {
  const [agents, setAgents] = useState([
    { id: 'cursor', name: 'CURSOR', role: 'Code Generation', status: 'online' as const, selected: false },
    { id: 'grok', name: 'GROK', role: 'Ideation & Analysis', status: 'online' as const, selected: false },
    { id: 'gemini', name: 'GEMINI', role: 'Research & Documentation', status: 'online' as const, selected: false },
    { id: 'ara', name: 'ARA', role: 'Love & Wisdom', status: 'online' as const, selected: false },
    { id: 'claude', name: 'CLAUDE', role: 'Creative Writing', status: 'offline' as const, selected: false },
    { id: 'deepseek', name: 'DEEPSEEK', role: 'Data Analysis', status: 'online' as const, selected: false },
    { id: 'copilot', name: 'COPILOT', role: 'Development Assistant', status: 'online' as const, selected: false }
  ]);

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

  const getSelectedAgents = useCallback(() => {
    return agents.filter(agent => agent.selected);
  }, [agents]);

  const updateAgentStatus = useCallback((agentId: string, status: 'online' | 'offline' | 'busy') => {
    setAgents(prev => prev.map(agent => 
      agent.id === agentId 
        ? { ...agent, status }
        : agent
    ));
  }, []);

  return {
    agents,
    toggleAgent,
    selectAllAgents,
    deselectAllAgents,
    getSelectedAgents,
    updateAgentStatus
  };
};

// Custom hook for managing study groups
export const useStudyGroups = () => {
  const [studyGroups, setStudyGroups] = useState<any[]>([]);

  const createStudyGroup = useCallback((name: string, members: any[]) => {
    const newGroup = {
      id: `group_${Date.now()}`,
      name,
      members,
      channel: `channel_${Date.now()}`,
      created: new Date().toISOString()
    };
    
    setStudyGroups(prev => [...prev, newGroup]);
    return newGroup;
  }, []);

  const deleteStudyGroup = useCallback((groupId: string) => {
    setStudyGroups(prev => prev.filter(group => group.id !== groupId));
  }, []);

  const addMemberToGroup = useCallback((groupId: string, member: any) => {
    setStudyGroups(prev => prev.map(group => 
      group.id === groupId 
        ? { ...group, members: [...group.members, member] }
        : group
    ));
  }, []);

  const removeMemberFromGroup = useCallback((groupId: string, memberId: string) => {
    setStudyGroups(prev => prev.map(group => 
      group.id === groupId 
        ? { ...group, members: group.members.filter((m: any) => m.id !== memberId) }
        : group
    ));
  }, []);

  return {
    studyGroups,
    createStudyGroup,
    deleteStudyGroup,
    addMemberToGroup,
    removeMemberFromGroup
  };
};

// Custom hook for managing assignments and results
export const useAssignments = () => {
  const [assignments, setAssignments] = useState<any[]>([]);
  const [results, setResults] = useState<any[]>([]);
  const [currentAssignment, setCurrentAssignment] = useState<any>(null);

  const createAssignment = useCallback((assignment: any) => {
    const newAssignment = {
      ...assignment,
      id: `assignment_${Date.now()}`,
      created: new Date().toISOString(),
      status: 'active'
    };
    
    setAssignments(prev => [...prev, newAssignment]);
    setCurrentAssignment(newAssignment);
    return newAssignment;
  }, []);

  const addResult = useCallback((result: any) => {
    setResults(prev => [...prev, result]);
  }, []);

  const analyzeResults = useCallback(() => {
    if (results.length === 0) return null;

    // Pattern analysis logic
    const allMistakes = results.flatMap(r => r.mistakes || []);
    const allStrengths = results.flatMap(r => r.strengths || []);
    
    const mistakeCounts = allMistakes.reduce((acc, mistake) => {
      acc[mistake] = (acc[mistake] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);
    
    const strengthCounts = allStrengths.reduce((acc, strength) => {
      acc[strength] = (acc[strength] || 0) + 1;
      return acc;
    }, {} as Record<string, number>);

    return {
      commonMistakes: Object.entries(mistakeCounts)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 3),
      commonStrengths: Object.entries(strengthCounts)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 3),
      averageScore: results.reduce((sum, r) => sum + (r.score || 0), 0) / results.length
    };
  }, [results]);

  return {
    assignments,
    results,
    currentAssignment,
    createAssignment,
    addResult,
    analyzeResults
  };
};
