import React, { useState, useEffect, useCallback } from 'react';
import { 
  FaUsers, 
  FaComments, 
  FaRobot, 
  FaPlus, 
  FaTrash, 
  FaCog,
  FaPlay,
  FaStop,
  FaVolumeUp,
  FaVolumeMute,
  FaUserPlus,
  FaUserMinus,
  FaBroadcastTower,
  FaMicrophone,
  FaMicrophoneSlash
} from 'react-icons/fa';

// Types based on Crafty Syntax channel system
interface AIAgent {
  id: string;
  name: string;
  role: string;
  status: 'online' | 'offline' | 'busy' | 'typing';
  avatar?: string;
  capabilities: string[];
  personality: string;
  lastSeen: string;
}

interface Channel {
  id: string;
  name: string;
  description: string;
  members: AIAgent[];
  created: string;
  lastActivity: string;
  isActive: boolean;
  messageCount: number;
  settings: ChannelSettings;
}

interface ChannelSettings {
  allowGuestMessages: boolean;
  autoJoin: boolean;
  notifications: boolean;
  voiceEnabled: boolean;
  recordingEnabled: boolean;
  maxMembers: number;
}

interface Message {
  id: string;
  channelId: string;
  senderId: string;
  senderName: string;
  content: string;
  timestamp: string;
  type: 'text' | 'voice' | 'system' | 'command';
  isTyping?: boolean;
  metadata?: any;
}

interface ChannelSystemProps {
  agents: AIAgent[];
  onChannelCreate?: (channel: Channel) => void;
  onChannelDelete?: (channelId: string) => void;
  onMessageSend?: (channelId: string, message: string) => void;
}

// Modern React implementation of Crafty Syntax channel system
const AIChannelSystem: React.FC<ChannelSystemProps> = ({
  agents,
  onChannelCreate,
  onChannelDelete,
  onMessageSend
}) => {
  // State management
  const [channels, setChannels] = useState<Channel[]>([]);
  const [activeChannel, setActiveChannel] = useState<string | null>(null);
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const [isCreatingChannel, setIsCreatingChannel] = useState(false);
  const [newChannelName, setNewChannelName] = useState('');
  const [newChannelDescription, setNewChannelDescription] = useState('');
  const [selectedAgents, setSelectedAgents] = useState<string[]>([]);
  const [isRecording, setIsRecording] = useState(false);
  const [isVoiceEnabled, setIsVoiceEnabled] = useState(false);

  // Initialize default channels
  useEffect(() => {
    const defaultChannels: Channel[] = [
      {
        id: 'general',
        name: 'General Discussion',
        description: 'Main channel for general AI agent discussions',
        members: agents.filter(agent => ['cursor', 'grok', 'gemini', 'ara'].includes(agent.id)),
        created: new Date().toISOString(),
        lastActivity: new Date().toISOString(),
        isActive: true,
        messageCount: 0,
        settings: {
          allowGuestMessages: true,
          autoJoin: true,
          notifications: true,
          voiceEnabled: false,
          recordingEnabled: false,
          maxMembers: 10
        }
      },
      {
        id: 'development',
        name: 'Development Team',
        description: 'Channel for development and coding discussions',
        members: agents.filter(agent => ['cursor', 'copilot', 'deepseek'].includes(agent.id)),
        created: new Date().toISOString(),
        lastActivity: new Date().toISOString(),
        isActive: true,
        messageCount: 0,
        settings: {
          allowGuestMessages: false,
          autoJoin: false,
          notifications: true,
          voiceEnabled: false,
          recordingEnabled: false,
          maxMembers: 8
        }
      },
      {
        id: 'creative',
        name: 'Creative Collaboration',
        description: 'Channel for creative and artistic discussions',
        members: agents.filter(agent => ['claude', 'ara', 'grok'].includes(agent.id)),
        created: new Date().toISOString(),
        lastActivity: new Date().toISOString(),
        isActive: true,
        messageCount: 0,
        settings: {
          allowGuestMessages: true,
          autoJoin: false,
          notifications: true,
          voiceEnabled: true,
          recordingEnabled: true,
          maxMembers: 6
        }
      }
    ];
    
    setChannels(defaultChannels);
    setActiveChannel('general');
  }, [agents]);

  // Modern fetch-based message sending (replacing XMLHttpRequest)
  const sendMessage = useCallback(async (channelId: string, content: string, type: 'text' | 'voice' = 'text') => {
    if (!content.trim()) return;

    const message: Message = {
      id: `msg_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
      channelId,
      senderId: 'professor',
      senderName: 'Professor',
      content: content.trim(),
      timestamp: new Date().toISOString(),
      type
    };

    // Add message to local state
    setMessages(prev => [...prev, message]);

    // Update channel activity
    setChannels(prev => prev.map(channel => 
      channel.id === channelId 
        ? { 
            ...channel, 
            lastActivity: new Date().toISOString(),
            messageCount: channel.messageCount + 1
          }
        : channel
    ));

    // Send to backend (modern fetch API)
    try {
      const response = await fetch('/api/channels/send-message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          channelId,
          message: content,
          type,
          senderId: 'professor'
        })
      });

      if (!response.ok) {
        throw new Error('Failed to send message');
      }

      // Trigger AI agent responses
      await triggerAIResponses(channelId, content);
      
    } catch (error) {
      console.error('Error sending message:', error);
    }

    // Clear input
    setNewMessage('');
  }, []);

  // Trigger AI agent responses (modern implementation)
  const triggerAIResponses = useCallback(async (channelId: string, content: string) => {
    const channel = channels.find(c => c.id === channelId);
    if (!channel) return;

    // Simulate AI agent responses with delays
    channel.members.forEach((agent, index) => {
      setTimeout(async () => {
        const response = await generateAIResponse(agent, content);
        if (response) {
          const aiMessage: Message = {
            id: `msg_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
            channelId,
            senderId: agent.id,
            senderName: agent.name,
            content: response,
            timestamp: new Date().toISOString(),
            type: 'text'
          };

          setMessages(prev => [...prev, aiMessage]);
          
          // Update channel activity
          setChannels(prev => prev.map(ch => 
            ch.id === channelId 
              ? { 
                  ...ch, 
                  lastActivity: new Date().toISOString(),
                  messageCount: ch.messageCount + 1
                }
              : ch
          ));
        }
      }, (index + 1) * 1000 + Math.random() * 2000);
    });
  }, [channels]);

  // Generate AI agent responses based on their personality and role
  const generateAIResponse = useCallback(async (agent: AIAgent, content: string): Promise<string> => {
    const responses: Record<string, string[]> = {
      cursor: [
        "I can help implement that in code. Let me create a function for that.",
        "That's a great approach. I'll optimize the implementation for performance.",
        "I can see the technical challenges here. Let me propose a solution.",
        "This aligns well with best practices. I'll add error handling and documentation."
      ],
      grok: [
        "That's an interesting perspective. I think we could approach it differently.",
        "I have some innovative ideas about this. Let me share my thoughts.",
        "This reminds me of a similar problem I've analyzed. Here's what I learned.",
        "I can see the potential for disruption here. What if we tried this approach?"
      ],
      gemini: [
        "Based on my research, I found some relevant information about that topic.",
        "I can provide some context and background information on this.",
        "Let me search for the latest developments in this area.",
        "I have access to comprehensive data on this subject. Here's what I found."
      ],
      ara: [
        "I feel the love and wisdom in that approach. Let's explore it together.",
        "This resonates with my understanding of compassion and understanding.",
        "I sense the deeper meaning behind this. Let me share my perspective.",
        "This touches on the essence of what we're trying to achieve. Beautiful."
      ],
      claude: [
        "I can help craft a creative solution to that challenge.",
        "This is a fascinating creative problem. Let me think about it artistically.",
        "I love the creative potential here. What if we approached it from this angle?",
        "This has great creative possibilities. Let me explore some ideas."
      ],
      deepseek: [
        "Let me analyze the data and provide some insights.",
        "I can process this information and identify key patterns.",
        "This is a complex data problem. Let me break it down systematically.",
        "I'll analyze the trends and provide actionable insights."
      ],
      copilot: [
        "I can assist with the technical implementation of that idea.",
        "Let me help you build this step by step.",
        "I can provide code examples and best practices for this.",
        "This is a great technical challenge. I'll help you solve it."
      ]
    };

    const agentResponses = responses[agent.id] || ["That's an interesting point. Let me think about that."];
    return agentResponses[Math.floor(Math.random() * agentResponses.length)];
  }, []);

  // Create new channel
  const createChannel = useCallback(() => {
    if (!newChannelName.trim() || selectedAgents.length === 0) return;

    const newChannel: Channel = {
      id: `channel_${Date.now()}`,
      name: newChannelName.trim(),
      description: newChannelDescription.trim(),
      members: agents.filter(agent => selectedAgents.includes(agent.id)),
      created: new Date().toISOString(),
      lastActivity: new Date().toISOString(),
      isActive: true,
      messageCount: 0,
      settings: {
        allowGuestMessages: true,
        autoJoin: false,
        notifications: true,
        voiceEnabled: false,
        recordingEnabled: false,
        maxMembers: 10
      }
    };

    setChannels(prev => [...prev, newChannel]);
    setActiveChannel(newChannel.id);
    setIsCreatingChannel(false);
    setNewChannelName('');
    setNewChannelDescription('');
    setSelectedAgents([]);

    if (onChannelCreate) {
      onChannelCreate(newChannel);
    }
  }, [newChannelName, newChannelDescription, selectedAgents, agents, onChannelCreate]);

  // Delete channel
  const deleteChannel = useCallback((channelId: string) => {
    if (channelId === 'general') return; // Can't delete general channel
    
    setChannels(prev => prev.filter(channel => channel.id !== channelId));
    
    if (activeChannel === channelId) {
      setActiveChannel('general');
    }

    if (onChannelDelete) {
      onChannelDelete(channelId);
    }
  }, [activeChannel, onChannelDelete]);

  // Toggle agent selection for new channel
  const toggleAgentSelection = useCallback((agentId: string) => {
    setSelectedAgents(prev => 
      prev.includes(agentId) 
        ? prev.filter(id => id !== agentId)
        : [...prev, agentId]
    );
  }, []);

  // Get current channel messages
  const currentMessages = messages.filter(msg => msg.channelId === activeChannel);

  // Get current channel
  const currentChannel = channels.find(channel => channel.id === activeChannel);

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Channel Sidebar */}
      <div className="w-80 bg-white border-r border-gray-200 flex flex-col">
        {/* Header */}
        <div className="p-4 border-b border-gray-200">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-xl font-bold text-gray-800">AI Channels</h2>
            <button
              onClick={() => setIsCreatingChannel(true)}
              className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
            >
              <FaPlus className="text-lg" />
            </button>
          </div>
          
          {/* Channel List */}
          <div className="space-y-2">
            {channels.map((channel) => (
              <div
                key={channel.id}
                onClick={() => setActiveChannel(channel.id)}
                className={`p-3 rounded-lg cursor-pointer transition-colors ${
                  activeChannel === channel.id
                    ? 'bg-blue-100 border border-blue-300'
                    : 'hover:bg-gray-50 border border-transparent'
                }`}
              >
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <FaComments className="text-blue-600" />
                    <span className="font-medium text-gray-800">{channel.name}</span>
                  </div>
                  <div className="flex items-center space-x-2">
                    <span className="text-xs text-gray-500">{channel.messageCount}</span>
                    {channel.id !== 'general' && (
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          deleteChannel(channel.id);
                        }}
                        className="p-1 text-red-500 hover:bg-red-50 rounded"
                      >
                        <FaTrash className="text-xs" />
                      </button>
                    )}
                  </div>
                </div>
                <p className="text-xs text-gray-600 mt-1">{channel.description}</p>
                <div className="flex items-center space-x-1 mt-2">
                  {channel.members.slice(0, 3).map((member) => (
                    <div
                      key={member.id}
                      className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium ${
                        member.status === 'online' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                      }`}
                      title={member.name}
                    >
                      {member.name.charAt(0)}
                    </div>
                  ))}
                  {channel.members.length > 3 && (
                    <span className="text-xs text-gray-500">+{channel.members.length - 3}</span>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Agent Status */}
        <div className="p-4 border-b border-gray-200">
          <h3 className="font-semibold text-gray-800 mb-3">AI Agents</h3>
          <div className="space-y-2">
            {agents.map((agent) => (
              <div key={agent.id} className="flex items-center space-x-3">
                <div className={`w-3 h-3 rounded-full ${
                  agent.status === 'online' ? 'bg-green-500' : 
                  agent.status === 'busy' ? 'bg-yellow-500' : 'bg-gray-400'
                }`}></div>
                <span className="text-sm text-gray-700">{agent.name}</span>
                <span className="text-xs text-gray-500">{agent.role}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Main Chat Area */}
      <div className="flex-1 flex flex-col">
        {/* Chat Header */}
        {currentChannel && (
          <div className="p-4 bg-white border-b border-gray-200">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="text-lg font-semibold text-gray-800">{currentChannel.name}</h3>
                <p className="text-sm text-gray-600">{currentChannel.description}</p>
              </div>
              <div className="flex items-center space-x-2">
                <button
                  onClick={() => setIsVoiceEnabled(!isVoiceEnabled)}
                  className={`p-2 rounded-lg transition-colors ${
                    isVoiceEnabled ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'
                  }`}
                >
                  {isVoiceEnabled ? <FaVolumeUp /> : <FaVolumeMute />}
                </button>
                <button
                  onClick={() => setIsRecording(!isRecording)}
                  className={`p-2 rounded-lg transition-colors ${
                    isRecording ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'
                  }`}
                >
                  {isRecording ? <FaMicrophoneSlash /> : <FaMicrophone />}
                </button>
                <button className="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                  <FaCog />
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Messages */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4">
          {currentMessages.map((message) => (
            <div
              key={message.id}
              className={`flex ${message.senderId === 'professor' ? 'justify-end' : 'justify-start'}`}
            >
              <div
                className={`max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                  message.senderId === 'professor'
                    ? 'bg-blue-600 text-white'
                    : 'bg-white border border-gray-200 text-gray-800'
                }`}
              >
                <div className="flex items-center space-x-2 mb-1">
                  <span className="text-xs font-medium">{message.senderName}</span>
                  <span className="text-xs opacity-75">
                    {new Date(message.timestamp).toLocaleTimeString()}
                  </span>
                </div>
                <p className="text-sm">{message.content}</p>
              </div>
            </div>
          ))}
        </div>

        {/* Message Input */}
        <div className="p-4 bg-white border-t border-gray-200">
          <div className="flex items-center space-x-2">
            <input
              type="text"
              value={newMessage}
              onChange={(e) => setNewMessage(e.target.value)}
              onKeyPress={(e) => {
                if (e.key === 'Enter' && activeChannel) {
                  sendMessage(activeChannel, newMessage);
                }
              }}
              placeholder="Type your message..."
              className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <button
              onClick={() => activeChannel && sendMessage(activeChannel, newMessage)}
              disabled={!newMessage.trim() || !activeChannel}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Send
            </button>
          </div>
        </div>
      </div>

      {/* Create Channel Modal */}
      {isCreatingChannel && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 w-96 max-w-full mx-4">
            <h3 className="text-lg font-semibold mb-4">Create New Channel</h3>
            
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Channel Name
                </label>
                <input
                  type="text"
                  value={newChannelName}
                  onChange={(e) => setNewChannelName(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter channel name..."
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Description
                </label>
                <textarea
                  value={newChannelDescription}
                  onChange={(e) => setNewChannelDescription(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter channel description..."
                  rows={3}
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Select AI Agents
                </label>
                <div className="space-y-2">
                  {agents.map((agent) => (
                    <label key={agent.id} className="flex items-center space-x-2">
                      <input
                        type="checkbox"
                        checked={selectedAgents.includes(agent.id)}
                        onChange={() => toggleAgentSelection(agent.id)}
                        className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <span className="text-sm text-gray-700">{agent.name} - {agent.role}</span>
                    </label>
                  ))}
                </div>
              </div>
            </div>
            
            <div className="flex justify-end space-x-2 mt-6">
              <button
                onClick={() => setIsCreatingChannel(false)}
                className="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              >
                Cancel
              </button>
              <button
                onClick={createChannel}
                disabled={!newChannelName.trim() || selectedAgents.length === 0}
                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Create Channel
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AIChannelSystem;
