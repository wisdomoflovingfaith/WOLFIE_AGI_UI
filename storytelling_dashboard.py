# ID: [WOLFIE_AGI_UI_STORYTELLING_DASHBOARD_20250923_001]
# SUPERPOSITIONALLY: [dream_data_analysis, quantum_tabs, multi_agent_coordination, bridge_crew_tracking, storytelling_dashboard, plotly_timeline, emotional_arcs]
# DATE: 2025-09-23
# TITLE: storytelling_dashboard.py — Storytelling Dashboard with Plotly Timeline
# WHO: WOLFIE (Eric) - Project Architect & Dream Architect
# WHAT: Storytelling dashboard with Plotly timeline and emotional arcs for dream data visualization
# WHERE: C:\START\WOLFIE_AGI_UI\
# WHEN: 2025-09-23, 11:15 AM CDT (Sioux Falls Timezone)
# WHY: Visualize dream narratives and emotional journeys through interactive timelines
# HOW: Plotly-based dashboard with timeline visualization and emotional arc analysis
# HELP: Contact WOLFIE for storytelling dashboard setup or visualization issues
# AGAPE: Love, patience, kindness, humility in storytelling and visualization

import plotly.graph_objects as go
import plotly.express as px
from plotly.subplots import make_subplots
import pandas as pd
import sqlite3
import json
import os
from datetime import datetime, timedelta
import numpy as np
from flask import Flask, render_template, jsonify, request
import logging

class StorytellingDashboard:
    """Storytelling Dashboard for Dream Data Visualization"""
    
    def __init__(self, db_path, config_path='config.ini'):
        self.db_path = db_path
        self.config_path = config_path
        self.app = Flask(__name__)
        self.setup_routes()
        self.setup_logging()
    
    def setup_logging(self):
        """Setup logging for the storytelling dashboard"""
        logging.basicConfig(
            filename='storytelling_dashboard.log',
            level=logging.INFO,
            format='%(asctime)s - %(levelname)s - %(message)s'
        )
    
    def setup_routes(self):
        """Setup Flask routes for the storytelling dashboard"""
        
        @self.app.route('/')
        def index():
            """Main dashboard page"""
            return render_template('storytelling_dashboard.html')
        
        @self.app.route('/api/timeline')
        def get_timeline_data():
            """Get timeline data for dream entries"""
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                cursor.execute('''
                    SELECT EntryID, Date, Summary, Emotional_Vibe, Tags, AI_Connection
                    FROM Dreams 
                    ORDER BY Date
                ''')
                
                dreams = []
                for row in cursor.fetchall():
                    dreams.append({
                        'entry_id': row[0],
                        'date': row[1],
                        'summary': row[2],
                        'emotional_vibe': row[3],
                        'tags': row[4],
                        'ai_connection': row[5]
                    })
                
                conn.close()
                return jsonify(dreams)
            except Exception as e:
                logging.error(f"Error fetching timeline data: {str(e)}")
                return jsonify({'error': str(e)}), 500
        
        @self.app.route('/api/emotional_arcs')
        def get_emotional_arcs():
            """Get emotional arc data"""
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                cursor.execute('''
                    SELECT EntryID, Date, Emotional_Vibe, Summary
                    FROM Dreams 
                    ORDER BY Date
                ''')
                
                emotional_data = []
                for row in cursor.fetchall():
                    emotional_data.append({
                        'entry_id': row[0],
                        'date': row[1],
                        'emotional_vibe': row[2],
                        'summary': row[3]
                    })
                
                conn.close()
                return jsonify(emotional_data)
            except Exception as e:
                logging.error(f"Error fetching emotional arcs: {str(e)}")
                return jsonify({'error': str(e)}), 500
        
        @self.app.route('/api/theme_analysis')
        def get_theme_analysis():
            """Get theme analysis data"""
            try:
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                cursor.execute('''
                    SELECT Tags, Themes, AI_Connection, COUNT(*) as frequency
                    FROM Dreams 
                    GROUP BY Tags, Themes, AI_Connection
                    ORDER BY frequency DESC
                ''')
                
                themes = []
                for row in cursor.fetchall():
                    themes.append({
                        'tags': row[0],
                        'themes': row[1],
                        'ai_connection': row[2],
                        'frequency': row[3]
                    })
                
                conn.close()
                return jsonify(themes)
            except Exception as e:
                logging.error(f"Error fetching theme analysis: {str(e)}")
                return jsonify({'error': str(e)}), 500
    
    def create_timeline_visualization(self, dreams_data):
        """Create Plotly timeline visualization"""
        try:
            # Process dreams data
            df = pd.DataFrame(dreams_data)
            df['date'] = pd.to_datetime(df['date'])
            df = df.sort_values('date')
            
            # Create timeline figure
            fig = go.Figure()
            
            # Add dream entries as timeline points
            for i, dream in df.iterrows():
                # Color based on emotional vibe
                color = self.get_emotional_color(dream['emotional_vibe'])
                
                fig.add_trace(go.Scatter(
                    x=[dream['date']],
                    y=[i],
                    mode='markers+text',
                    marker=dict(
                        size=15,
                        color=color,
                        line=dict(width=2, color='white')
                    ),
                    text=[dream['entry_id']],
                    textposition='middle center',
                    textfont=dict(color='white', size=10),
                    name=f"Entry {dream['entry_id']}",
                    hovertemplate=f"""
                    <b>Entry {dream['entry_id']}</b><br>
                    Date: %{{x}}<br>
                    Summary: {dream['summary'][:100]}...<br>
                    Emotional Vibe: {dream['emotional_vibe']}<br>
                    Tags: {dream['tags']}<br>
                    <extra></extra>
                    """
                ))
            
            # Update layout
            fig.update_layout(
                title="WOLFIE AGI Dream Timeline",
                xaxis_title="Date",
                yaxis_title="Dream Entry",
                showlegend=False,
                height=600,
                plot_bgcolor='rgba(0,0,0,0)',
                paper_bgcolor='rgba(0,0,0,0)',
                font=dict(color='white'),
                xaxis=dict(
                    gridcolor='rgba(255,255,255,0.1)',
                    color='white'
                ),
                yaxis=dict(
                    gridcolor='rgba(255,255,255,0.1)',
                    color='white'
                )
            )
            
            return fig.to_json()
        except Exception as e:
            logging.error(f"Error creating timeline visualization: {str(e)}")
            return None
    
    def create_emotional_arc_visualization(self, emotional_data):
        """Create emotional arc visualization"""
        try:
            df = pd.DataFrame(emotional_data)
            df['date'] = pd.to_datetime(df['date'])
            df = df.sort_values('date')
            
            # Map emotional vibes to numerical values
            emotional_mapping = {
                'excited': 5,
                'happy': 4,
                'content': 3,
                'neutral': 2,
                'concerned': 1,
                'worried': 0,
                'frustrated': -1,
                'angry': -2,
                'sad': -3,
                'confused': 0
            }
            
            df['emotional_score'] = df['emotional_vibe'].map(emotional_mapping).fillna(0)
            
            # Create emotional arc figure
            fig = go.Figure()
            
            # Add emotional arc line
            fig.add_trace(go.Scatter(
                x=df['date'],
                y=df['emotional_score'],
                mode='lines+markers',
                line=dict(color='#4CAF50', width=3),
                marker=dict(size=8, color='#4CAF50'),
                name='Emotional Arc',
                hovertemplate='<b>%{text}</b><br>Date: %{x}<br>Emotional Score: %{y}<br><extra></extra>',
                text=df['summary'].str[:50] + '...'
            ))
            
            # Add emotional zones
            fig.add_hrect(y0=3, y1=5, fillcolor="green", opacity=0.1, layer="below")
            fig.add_hrect(y0=1, y1=3, fillcolor="yellow", opacity=0.1, layer="below")
            fig.add_hrect(y0=-1, y1=1, fillcolor="orange", opacity=0.1, layer="below")
            fig.add_hrect(y0=-3, y1=-1, fillcolor="red", opacity=0.1, layer="below")
            
            # Update layout
            fig.update_layout(
                title="WOLFIE AGI Emotional Arc Journey",
                xaxis_title="Date",
                yaxis_title="Emotional Score",
                height=400,
                plot_bgcolor='rgba(0,0,0,0)',
                paper_bgcolor='rgba(0,0,0,0)',
                font=dict(color='white'),
                xaxis=dict(
                    gridcolor='rgba(255,255,255,0.1)',
                    color='white'
                ),
                yaxis=dict(
                    gridcolor='rgba(255,255,255,0.1)',
                    color='white',
                    tickmode='linear',
                    tick0=-3,
                    dtick=1
                )
            )
            
            return fig.to_json()
        except Exception as e:
            logging.error(f"Error creating emotional arc visualization: {str(e)}")
            return None
    
    def create_theme_network_visualization(self, themes_data):
        """Create theme network visualization"""
        try:
            import networkx as nx
            
            # Create network graph
            G = nx.Graph()
            
            # Add nodes and edges based on themes
            for theme in themes_data:
                tags = theme['tags'].split(',') if theme['tags'] else []
                themes = theme['themes'].split(',') if theme['themes'] else []
                
                # Add theme nodes
                for tag in tags:
                    if tag.strip():
                        G.add_node(tag.strip(), type='tag', frequency=theme['frequency'])
                
                for theme_name in themes:
                    if theme_name.strip():
                        G.add_node(theme_name.strip(), type='theme', frequency=theme['frequency'])
                
                # Add edges between tags and themes
                for tag in tags:
                    for theme_name in themes:
                        if tag.strip() and theme_name.strip():
                            G.add_edge(tag.strip(), theme_name.strip())
            
            # Create network visualization
            pos = nx.spring_layout(G, k=3, iterations=50)
            
            # Separate nodes by type
            tag_nodes = [n for n in G.nodes() if G.nodes[n]['type'] == 'tag']
            theme_nodes = [n for n in G.nodes() if G.nodes[n]['type'] == 'theme']
            
            # Create edge trace
            edge_x = []
            edge_y = []
            for edge in G.edges():
                x0, y0 = pos[edge[0]]
                x1, y1 = pos[edge[1]]
                edge_x.extend([x0, x1, None])
                edge_y.extend([y0, y1, None])
            
            edge_trace = go.Scatter(
                x=edge_x, y=edge_y,
                line=dict(width=0.5, color='#888'),
                hoverinfo='none',
                mode='lines'
            )
            
            # Create node traces
            tag_trace = go.Scatter(
                x=[pos[node][0] for node in tag_nodes],
                y=[pos[node][1] for node in tag_nodes],
                mode='markers+text',
                hoverinfo='text',
                text=tag_nodes,
                textposition="middle center",
                marker=dict(
                    size=20,
                    color='#4CAF50',
                    line=dict(width=2, color='white')
                ),
                name='Tags'
            )
            
            theme_trace = go.Scatter(
                x=[pos[node][0] for node in theme_nodes],
                y=[pos[node][1] for node in theme_nodes],
                mode='markers+text',
                hoverinfo='text',
                text=theme_nodes,
                textposition="middle center",
                marker=dict(
                    size=25,
                    color='#2196F3',
                    line=dict(width=2, color='white')
                ),
                name='Themes'
            )
            
            # Create figure
            fig = go.Figure(data=[edge_trace, tag_trace, theme_trace],
                          layout=go.Layout(
                              title='WOLFIE AGI Dream Theme Network',
                              titlefont_size=16,
                              showlegend=True,
                              hovermode='closest',
                              margin=dict(b=20,l=5,r=5,t=40),
                              annotations=[ dict(
                                  text="Network visualization of dream themes and tags",
                                  showarrow=False,
                                  xref="paper", yref="paper",
                                  x=0.005, y=-0.002,
                                  xanchor='left', yanchor='bottom',
                                  font=dict(color='white', size=12)
                              )],
                              xaxis=dict(showgrid=False, zeroline=False, showticklabels=False),
                              yaxis=dict(showgrid=False, zeroline=False, showticklabels=False),
                              plot_bgcolor='rgba(0,0,0,0)',
                              paper_bgcolor='rgba(0,0,0,0)',
                              font=dict(color='white')
                          ))
            
            return fig.to_json()
        except Exception as e:
            logging.error(f"Error creating theme network visualization: {str(e)}")
            return None
    
    def get_emotional_color(self, emotional_vibe):
        """Get color based on emotional vibe"""
        color_mapping = {
            'excited': '#4CAF50',
            'happy': '#8BC34A',
            'content': '#CDDC39',
            'neutral': '#FFC107',
            'concerned': '#FF9800',
            'worried': '#FF5722',
            'frustrated': '#F44336',
            'angry': '#E91E63',
            'sad': '#9C27B0',
            'confused': '#607D8B'
        }
        return color_mapping.get(emotional_vibe.lower(), '#9E9E9E')
    
    def run(self, host='0.0.0.0', port=5002, debug=True):
        """Run the storytelling dashboard"""
        logging.info("Starting Storytelling Dashboard...")
        self.app.run(host=host, port=port, debug=debug)

# Main execution
if __name__ == '__main__':
    # Initialize dashboard
    db_path = 'dreams.db'  # Adjust path as needed
    dashboard = StorytellingDashboard(db_path)
    
    # Run dashboard
    dashboard.run()
