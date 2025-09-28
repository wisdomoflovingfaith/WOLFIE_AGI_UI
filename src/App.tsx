import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import ModernClassroomInterface from './components/ModernClassroomInterface';
import ClassroomInterface from './components/ClassroomInterface';
import { 
  FaHome, 
  FaChalkboardTeacher, 
  FaCode, 
  FaUsers, 
  FaBrain,
  FaSearch,
  FaCog
} from 'react-icons/fa';

// Main App Component
const App: React.FC = () => {
  const [activeTab, setActiveTab] = useState('classroom');

  return (
    <Router>
      <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-600">
        {/* Navigation */}
        <nav className="bg-white shadow-lg">
          <div className="container mx-auto px-4">
            <div className="flex justify-between items-center py-4">
              <div className="flex items-center space-x-8">
                <Link to="/" className="flex items-center text-2xl font-bold text-blue-600">
                  <FaHome className="mr-2" />
                  WOLFIE AGI UI
                </Link>
                
                <div className="flex space-x-4">
                  <button
                    onClick={() => setActiveTab('classroom')}
                    className={`flex items-center px-4 py-2 rounded-lg transition-colors ${
                      activeTab === 'classroom' 
                        ? 'bg-blue-600 text-white' 
                        : 'text-gray-600 hover:bg-gray-100'
                    }`}
                  >
                    <FaChalkboardTeacher className="mr-2" />
                    Classroom
                  </button>
                  
                  <button
                    onClick={() => setActiveTab('modern')}
                    className={`flex items-center px-4 py-2 rounded-lg transition-colors ${
                      activeTab === 'modern' 
                        ? 'bg-blue-600 text-white' 
                        : 'text-gray-600 hover:bg-gray-100'
                    }`}
                  >
                    <FaCode className="mr-2" />
                    Modern React
                  </button>
                  
                  <button
                    onClick={() => setActiveTab('agents')}
                    className={`flex items-center px-4 py-2 rounded-lg transition-colors ${
                      activeTab === 'agents' 
                        ? 'bg-blue-600 text-white' 
                        : 'text-gray-600 hover:bg-gray-100'
                    }`}
                  >
                    <FaUsers className="mr-2" />
                    AI Agents
                  </button>
                  
                  <button
                    onClick={() => setActiveTab('search')}
                    className={`flex items-center px-4 py-2 rounded-lg transition-colors ${
                      activeTab === 'search' 
                        ? 'bg-blue-600 text-white' 
                        : 'text-gray-600 hover:bg-gray-100'
                    }`}
                  >
                    <FaSearch className="mr-2" />
                    Search
                  </button>
                </div>
              </div>
              
              <div className="flex items-center space-x-4">
                <button className="text-gray-600 hover:text-blue-600">
                  <FaCog className="text-xl" />
                </button>
              </div>
            </div>
          </div>
        </nav>

        {/* Main Content */}
        <main className="container mx-auto px-4 py-8">
          <Routes>
            <Route path="/" element={<HomePage activeTab={activeTab} setActiveTab={setActiveTab} />} />
            <Route path="/classroom" element={<ClassroomInterface />} />
            <Route path="/modern" element={<ModernClassroomInterface />} />
          </Routes>
        </main>

        {/* Footer */}
        <footer className="bg-white border-t border-gray-200 mt-16">
          <div className="container mx-auto px-4 py-8">
            <div className="text-center text-gray-600">
              <p className="mb-2">
                <strong>WOLFIE AGI UI</strong> - Modern React-based AI Classroom Interface
              </p>
              <p className="text-sm">
                Built with React, TypeScript, and modern web technologies
              </p>
              <p className="text-sm mt-2">
                Replacing XMLHttpRequest with modern fetch API and React hooks
              </p>
            </div>
          </div>
        </footer>
      </div>
    </Router>
  );
};

// Home Page Component
const HomePage: React.FC<{ activeTab: string; setActiveTab: (tab: string) => void }> = ({ activeTab, setActiveTab }) => {
  return (
    <div className="text-center">
      <div className="bg-white rounded-2xl p-8 shadow-xl mb-8">
        <h1 className="text-4xl font-bold text-blue-600 mb-4">
          WOLFIE AGI UI - Modern Classroom Interface
        </h1>
        <p className="text-xl text-gray-600 mb-8">
          React-based professor interface for AI agent assignments and study groups
        </p>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div className="bg-blue-50 p-6 rounded-lg">
            <FaChalkboardTeacher className="text-3xl text-blue-600 mb-4 mx-auto" />
            <h3 className="font-semibold text-lg mb-2">Classroom Interface</h3>
            <p className="text-gray-600 text-sm mb-4">
              Traditional HTML/JavaScript interface with XMLHttpRequest
            </p>
            <button
              onClick={() => setActiveTab('classroom')}
              className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Open Classroom
            </button>
          </div>
          
          <div className="bg-green-50 p-6 rounded-lg">
            <FaCode className="text-3xl text-green-600 mb-4 mx-auto" />
            <h3 className="font-semibold text-lg mb-2">Modern React</h3>
            <p className="text-gray-600 text-sm mb-4">
              Modern React interface with hooks and fetch API
            </p>
            <button
              onClick={() => setActiveTab('modern')}
              className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors"
            >
              Open Modern
            </button>
          </div>
          
          <div className="bg-purple-50 p-6 rounded-lg">
            <FaUsers className="text-3xl text-purple-600 mb-4 mx-auto" />
            <h3 className="font-semibold text-lg mb-2">AI Agents</h3>
            <p className="text-gray-600 text-sm mb-4">
              Manage and coordinate multiple AI agents
            </p>
            <button
              onClick={() => setActiveTab('agents')}
              className="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors"
            >
              Manage Agents
            </button>
          </div>
          
          <div className="bg-yellow-50 p-6 rounded-lg">
            <FaSearch className="text-3xl text-yellow-600 mb-4 mx-auto" />
            <h3 className="font-semibold text-lg mb-2">Document Search</h3>
            <p className="text-gray-600 text-sm mb-4">
              Search MD files in the docs folder
            </p>
            <button
              onClick={() => setActiveTab('search')}
              className="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
            >
              Search Docs
            </button>
          </div>
        </div>
      </div>
      
      {/* Features Section */}
      <div className="bg-white rounded-2xl p-8 shadow-xl">
        <h2 className="text-2xl font-bold text-gray-800 mb-6">Key Features</h2>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div className="text-left">
            <h3 className="font-semibold text-lg mb-2 text-blue-600">Modern API Integration</h3>
            <ul className="text-sm text-gray-600 space-y-1">
              <li>• Replaced XMLHttpRequest with fetch API</li>
              <li>• React hooks for state management</li>
              <li>• TypeScript for type safety</li>
              <li>• Modern error handling</li>
            </ul>
          </div>
          
          <div className="text-left">
            <h3 className="font-semibold text-lg mb-2 text-green-600">AI Agent Management</h3>
            <ul className="text-sm text-gray-600 space-y-1">
              <li>• Assign tasks to multiple AI agents</li>
              <li>• Create study groups and channels</li>
              <li>• Real-time chat and collaboration</li>
              <li>• Pattern analysis and learning</li>
            </ul>
          </div>
          
          <div className="text-left">
            <h3 className="font-semibold text-lg mb-2 text-purple-600">Document Search</h3>
            <ul className="text-sm text-gray-600 space-y-1">
              <li>• Search MD files in docs folder</li>
              <li>• Header-based search (WHO, WHAT, WHERE, etc.)</li>
              <li>• Real-time search results</li>
              <li>• Integration with assignment system</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

export default App;
