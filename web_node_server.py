# ID: [WOLFIE_AGI_UI_WEB_NODE_SERVER_20250923_001]
# SUPERPOSITIONALLY: [dream_data_analysis, quantum_tabs, multi_agent_coordination, bridge_crew_tracking, web_node_system, real_time_communication]
# DATE: 2025-09-23
# TITLE: web_node_server.py — Web Node System Server
# WHO: WOLFIE (Eric) - Project Architect & Dream Architect
# WHAT: WebSocket server for real-time multi-agent coordination and communication
# WHERE: C:\START\WOLFIE_AGI_UI\
# WHEN: 2025-09-23, 11:00 AM CDT (Sioux Falls Timezone)
# WHY: Enable seamless real-time coordination between multiple AI agents and human operators
# HOW: Flask-SocketIO server with REST API endpoints and real-time status updates
# HELP: Contact WOLFIE for web node system setup or integration issues
# AGAPE: Love, patience, kindness, humility in multi-agent collaboration

from flask import Flask, request, jsonify, render_template
from flask_socketio import SocketIO, emit, join_room, leave_room
import json
import sqlite3
import os
from datetime import datetime
import logging
from cryptography.fernet import Fernet
import configparser

# Initialize Flask app and SocketIO
app = Flask(__name__)
app.config['SECRET_KEY'] = 'wolfie_agi_secret_key_2025'
socketio = SocketIO(app, cors_allowed_origins="*")

# Load configuration
config = configparser.ConfigParser()
config.read('config.ini')
BASE_DIR = config.get('Paths', 'BaseDir', fallback=r'C:\START\WOLFIE_AGI_UI')
DB_PATH = os.path.join(BASE_DIR, 'web_nodes.db')

# Setup logging
logging.basicConfig(
    filename=os.path.join(BASE_DIR, 'web_node_server.log'),
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

# Global data structures
agents = {}
tasks = {}
dream_fragments = []
message_history = []

# Database initialization
def init_database():
    """Initialize the web nodes database"""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    # Create agents table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS agents (
            agent_id TEXT PRIMARY KEY,
            agent_name TEXT NOT NULL,
            capabilities TEXT,
            status TEXT,
            last_seen TEXT,
            understanding_score INTEGER,
            alignment_score INTEGER,
            current_task TEXT
        )
    ''')
    
    # Create tasks table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS tasks (
            task_id TEXT PRIMARY KEY,
            task_type TEXT,
            description TEXT,
            assigned_to TEXT,
            priority TEXT,
            status TEXT,
            created_at TEXT,
            deadline TEXT,
            FOREIGN KEY (assigned_to) REFERENCES agents (agent_id)
        )
    ''')
    
    # Create messages table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS messages (
            message_id INTEGER PRIMARY KEY AUTOINCREMENT,
            from_agent TEXT,
            to_agent TEXT,
            message_type TEXT,
            content TEXT,
            timestamp TEXT,
            FOREIGN KEY (from_agent) REFERENCES agents (agent_id),
            FOREIGN KEY (to_agent) REFERENCES agents (agent_id)
        )
    ''')
    
    conn.commit()
    conn.close()
    logging.info("Database initialized successfully")

# REST API Endpoints
@app.route('/api/agents', methods=['GET'])
def get_agents():
    """Get all registered agents"""
    try:
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM agents')
        agents_data = []
        for row in cursor.fetchall():
            agents_data.append({
                'agent_id': row[0],
                'agent_name': row[1],
                'capabilities': row[2],
                'status': row[3],
                'last_seen': row[4],
                'understanding_score': row[5],
                'alignment_score': row[6],
                'current_task': row[7]
            })
        conn.close()
        return jsonify(agents_data)
    except Exception as e:
        logging.error(f"Error fetching agents: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/tasks', methods=['GET'])
def get_tasks():
    """Get all tasks"""
    try:
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM tasks')
        tasks_data = []
        for row in cursor.fetchall():
            tasks_data.append({
                'task_id': row[0],
                'task_type': row[1],
                'description': row[2],
                'assigned_to': row[3],
                'priority': row[4],
                'status': row[5],
                'created_at': row[6],
                'deadline': row[7]
            })
        conn.close()
        return jsonify(tasks_data)
    except Exception as e:
        logging.error(f"Error fetching tasks: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/tasks', methods=['POST'])
def create_task():
    """Create a new task"""
    try:
        data = request.json
        task_id = f"TASK_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute('''
            INSERT INTO tasks (task_id, task_type, description, assigned_to, priority, status, created_at, deadline)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            task_id,
            data.get('task_type', 'general'),
            data.get('description', ''),
            data.get('assigned_to', ''),
            data.get('priority', 'medium'),
            'pending',
            datetime.now().isoformat(),
            data.get('deadline', '')
        ))
        conn.commit()
        conn.close()
        
        # Emit task creation event
        socketio.emit('task_created', {
            'task_id': task_id,
            'task_type': data.get('task_type'),
            'description': data.get('description'),
            'assigned_to': data.get('assigned_to'),
            'priority': data.get('priority')
        }, broadcast=True)
        
        return jsonify({'task_id': task_id, 'status': 'created'}), 201
    except Exception as e:
        logging.error(f"Error creating task: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/dream_fragments', methods=['GET'])
def get_dream_fragments():
    """Get all dream fragments"""
    return jsonify(dream_fragments)

@app.route('/api/dream_fragments', methods=['POST'])
def add_dream_fragment():
    """Add a new dream fragment"""
    try:
        data = request.json
        fragment_id = f"DREAM_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        
        fragment = {
            'fragment_id': fragment_id,
            'agent_id': data.get('agent_id'),
            'summary': data.get('summary', ''),
            'symbols': data.get('symbols', ''),
            'themes': data.get('themes', ''),
            'ai_connection': data.get('ai_connection', ''),
            'emotional_vibe': data.get('emotional_vibe', ''),
            'timestamp': datetime.now().isoformat()
        }
        
        dream_fragments.append(fragment)
        
        # Emit dream fragment event
        socketio.emit('dream_fragment_added', fragment, broadcast=True)
        
        return jsonify(fragment), 201
    except Exception as e:
        logging.error(f"Error adding dream fragment: {str(e)}")
        return jsonify({'error': str(e)}), 500

# WebSocket Event Handlers
@socketio.on('connect')
def handle_connect():
    """Handle client connection"""
    logging.info(f"Client connected: {request.sid}")
    emit('connected', {'message': 'Connected to Web Node Server'})

@socketio.on('disconnect')
def handle_disconnect():
    """Handle client disconnection"""
    logging.info(f"Client disconnected: {request.sid}")
    # Update agent status to offline
    for agent_id, agent_data in agents.items():
        if agent_data.get('socket_id') == request.sid:
            agent_data['status'] = 'offline'
            agent_data['last_seen'] = datetime.now().isoformat()
            socketio.emit('agent_status_updated', agent_data, broadcast=True)
            break

@socketio.on('agent_register')
def handle_agent_register(data):
    """Handle agent registration"""
    try:
        agent_id = data['agent_id']
        agent_data = {
            'agent_id': agent_id,
            'agent_name': data['agent_name'],
            'capabilities': data.get('capabilities', []),
            'status': 'active',
            'last_seen': datetime.now().isoformat(),
            'understanding_score': data.get('understanding_score', 0),
            'alignment_score': data.get('alignment_score', 0),
            'current_task': data.get('current_task', ''),
            'socket_id': request.sid
        }
        
        agents[agent_id] = agent_data
        
        # Save to database
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute('''
            INSERT OR REPLACE INTO agents 
            (agent_id, agent_name, capabilities, status, last_seen, understanding_score, alignment_score, current_task)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            agent_id,
            agent_data['agent_name'],
            json.dumps(agent_data['capabilities']),
            agent_data['status'],
            agent_data['last_seen'],
            agent_data['understanding_score'],
            agent_data['alignment_score'],
            agent_data['current_task']
        ))
        conn.commit()
        conn.close()
        
        emit('agent_registered', {'agent_id': agent_id, 'status': 'success'})
        emit('agent_list_updated', list(agents.values()), broadcast=True)
        logging.info(f"Agent registered: {agent_id}")
        
    except Exception as e:
        logging.error(f"Error registering agent: {str(e)}")
        emit('agent_registered', {'agent_id': data.get('agent_id'), 'status': 'error', 'error': str(e)})

@socketio.on('status_update')
def handle_status_update(data):
    """Handle agent status update"""
    try:
        agent_id = data['agent_id']
        if agent_id in agents:
            agents[agent_id].update(data)
            agents[agent_id]['last_seen'] = datetime.now().isoformat()
            
            # Update database
            conn = sqlite3.connect(DB_PATH)
            cursor = conn.cursor()
            cursor.execute('''
                UPDATE agents 
                SET status = ?, last_seen = ?, current_task = ?
                WHERE agent_id = ?
            ''', (
                data.get('status', agents[agent_id]['status']),
                agents[agent_id]['last_seen'],
                data.get('current_task', agents[agent_id]['current_task']),
                agent_id
            ))
            conn.commit()
            conn.close()
            
            emit('status_updated', data, broadcast=True)
            logging.info(f"Status updated for agent: {agent_id}")
        
    except Exception as e:
        logging.error(f"Error updating status: {str(e)}")
        emit('status_update_error', {'error': str(e)})

@socketio.on('send_message')
def handle_send_message(data):
    """Handle message sending between agents"""
    try:
        message = {
            'message_id': f"MSG_{datetime.now().strftime('%Y%m%d_%H%M%S')}",
            'from_agent': data['from_agent'],
            'to_agent': data['to_agent'],
            'message_type': data.get('message_type', 'text'),
            'content': data['content'],
            'timestamp': datetime.now().isoformat()
        }
        
        # Save to database
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute('''
            INSERT INTO messages (from_agent, to_agent, message_type, content, timestamp)
            VALUES (?, ?, ?, ?, ?)
        ''', (
            message['from_agent'],
            message['to_agent'],
            message['message_type'],
            message['content'],
            message['timestamp']
        ))
        conn.commit()
        conn.close()
        
        # Send to specific agent or broadcast
        if data['to_agent'] == 'broadcast':
            emit('message_received', message, broadcast=True)
        else:
            # Find target agent's socket and send message
            target_agent = agents.get(data['to_agent'])
            if target_agent and 'socket_id' in target_agent:
                emit('message_received', message, room=target_agent['socket_id'])
        
        message_history.append(message)
        logging.info(f"Message sent from {data['from_agent']} to {data['to_agent']}")
        
    except Exception as e:
        logging.error(f"Error sending message: {str(e)}")
        emit('message_error', {'error': str(e)})

@socketio.on('join_room')
def handle_join_room(data):
    """Handle joining a room"""
    room = data['room']
    join_room(room)
    emit('joined_room', {'room': room})
    logging.info(f"Client {request.sid} joined room: {room}")

@socketio.on('leave_room')
def handle_leave_room(data):
    """Handle leaving a room"""
    room = data['room']
    leave_room(room)
    emit('left_room', {'room': room})
    logging.info(f"Client {request.sid} left room: {room}")

# Dashboard route
@app.route('/dashboard')
def dashboard():
    """Serve the bridge crew dashboard"""
    return render_template('bridge_dashboard.html')

# Health check endpoint
@app.route('/health')
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'agents_count': len(agents),
        'tasks_count': len(tasks),
        'dream_fragments_count': len(dream_fragments)
    })

if __name__ == '__main__':
    # Initialize database
    init_database()
    
    # Start the server
    logging.info("Starting Web Node Server...")
    socketio.run(app, debug=True, host='0.0.0.0', port=5001)
