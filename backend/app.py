# ID: [WOLFIE_AGI_UI_BACKEND_20250923_003]
# SUPERPOSITIONALLY: [dream_data_analysis, quantum_tabs, multi_agent_coordination, bridge_crew_tracking, project_organization, music_influence]
# DATE: 2025-09-23
# TITLE: app.py — Flask API for WOLFIE AGI UI
# WHO: WOLFIE (Eric) - Project Architect & Dream Architect
# WHAT: Flask API to connect React UI with SQLite for dream log, bridge crew, and music tracking
# WHERE: C:\START\WOLFIE_AGI_UI\backend\
# WHEN: 2025-09-23, 09:25 AM CDT (Sioux Falls Timezone)
# WHY: Enable seamless data flow for dream fragments, agent coordination, and music influence
# HOW: Flask REST API with SQLite, PUHC tab-pull logic, text-based input, music logging
# HELP: Contact WOLFIE for API setup or data integration issues
# AGAPE: Love, patience, kindness, humility in multi-agent collaboration

from flask import Flask, request, jsonify
import sqlite3
import json
import os
from cryptography.fernet import Fernet
import logging
import configparser
from datetime import datetime

app = Flask(__name__)

# Load configuration
config = configparser.ConfigParser()
config.read(os.path.join('C:\\START\\WOLFIE_AGI_UI\\config', 'config.ini'))
BASE_DIR = config.get('Paths', 'BaseDir', fallback=r'C:\START\WOLFIE_AGI_UI')
DB_PATH = os.path.join(BASE_DIR, 'dreams.db')
logging.basicConfig(filename=os.path.join(BASE_DIR, 'backend', 'api.log'), level=logging.INFO)

# Encryption setup
key_file = os.path.join(BASE_DIR, 'config', 'encryption_key.bin')
with open(key_file, 'rb') as f:
    key = f.read()
cipher = Fernet(key)

# Database connection
def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn

# API endpoints
@app.route('/api/dreams', methods=['GET'])
def get_dreams():
    try:
        conn = get_db()
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM Dreams')
        dreams = [dict(row) for row in cursor.fetchall()]
        for dream in dreams:
            dream['Summary'] = cipher.decrypt(dream['Summary'].encode()).decode()
        conn.close()
        return jsonify(dreams)
    except Exception as e:
        logging.error(f"Error fetching dreams: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/dreams', methods=['POST'])
def add_dream():
    try:
        data = request.json
        data['Summary'] = cipher.encrypt(data['Summary'].encode()).decode()
        conn = get_db()
        cursor = conn.cursor()
        cursor.execute('''
            INSERT INTO Dreams
            (EntryID, Date, Summary, Who, What, Where, When, Why, How, Symbols, Themes,
            AI_Connection, Emotional_Vibe, Tags, Cross_References, Quantum_State, DreamTimestamp)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            data['EntryID'], data['Date'], data['Summary'],
            data.get('Who', ''), data.get('What', ''),
            data.get('Where', ''), data.get('When', ''),
            data.get('Why', ''), data.get('How', ''),
            data.get('Symbols', ''), data.get('Themes', ''),
            data.get('AI_Connection', ''), data.get('Emotional_Vibe', ''),
            data.get('Tags', ''), data.get('Cross_References', ''),
            data.get('Quantum_State', '{}'),
            data.get('DreamTimestamp', '')
        ))
        conn.commit()
        conn.close()
        data['Summary'] = cipher.decrypt(data['Summary'].encode()).decode()
        return jsonify(data), 201
    except Exception as e:
        logging.error(f"Error adding dream: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/bridge_crew', methods=['GET'])
def get_bridge_crew():
    try:
        conn = get_db()
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM BridgeCrew')
        crew = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return jsonify(crew)
    except Exception as e:
        logging.error(f"Error fetching bridge crew: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/pull_tab', methods=['POST'])
def pull_tab():
    try:
        tab_name = request.json['tab_name']
        conn = get_db()
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM Dreams')
        dreams = [dict(row) for row in cursor.fetchall()]
        for dream in dreams:
            dream['Quantum_State'] = json.loads(dream['Quantum_State'])
            dream['Tab_Weight'] = dream['Quantum_State'].get(tab_name, 0.0)
            dream['Summary'] = cipher.decrypt(dream['Summary'].encode()).decode()
        dreams.sort(key=lambda x: x['Tab_Weight'], reverse=True)
        conn.close()
        return jsonify(dreams)
    except Exception as e:
        logging.error(f"Error pulling tab: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/api/music_log', methods=['GET'])
def get_music_log():
    try:
        conn = get_db()
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM MusicLog')
        music = [dict(row) for row in cursor.fetchall()]
        conn.close()
        return jsonify(music)
    except Exception as e:
        logging.error(f"Error fetching music log: {str(e)}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5000)
