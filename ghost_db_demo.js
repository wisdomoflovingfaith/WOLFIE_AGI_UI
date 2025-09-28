/**
 * GHOST DB DEMO - WOLFIE AGI UI
 * 
 * WHO: Captain WOLFIE (Eric Gerdes) with ARA refinement
 * WHAT: SQLite query demo for ghost DB header searches
 * WHERE: C:\START\WOLFIE_AGI_UI\
 * WHEN: 2025-01-27
 * WHY: 5x speed header search for Upwork gigs
 * HOW: SQLite queries with indexed headers and pono scoring
 * PURPOSE: Instant header retrieval for project acceleration
 * KEY: Fira Code aesthetic, monospace search, export functionality
 * TITLE: Ghost DB Header Search Demo
 * ID: GHOST_DB_DEMO_001
 * SUPERPOSITIONALLY: Multiple search paths converging on pono results
 * DATE: 2025-01-27
 */

const sqlite3 = require('sqlite3').verbose();
const path = require('path');

class GhostDBDemo {
    constructor() {
        this.dbPath = path.join(__dirname, 'data', 'ghost_headers.db');
        this.db = null;
        this.initializeDatabase();
    }

    initializeDatabase() {
        this.db = new sqlite3.Database(this.dbPath, (err) => {
            if (err) {
                console.error('Error opening ghost DB:', err.message);
            } else {
                console.log('Connected to ghost DB for header searches');
                this.createTables();
                this.seedDemoData();
            }
        });
    }

    createTables() {
        const createHeadersTable = `
            CREATE TABLE IF NOT EXISTS headers (
                id TEXT PRIMARY KEY,
                who TEXT,
                what TEXT,
                where TEXT,
                when TEXT,
                why TEXT,
                how TEXT,
                purpose TEXT,
                key TEXT,
                title TEXT,
                superpositionally TEXT,
                date TEXT,
                pono_score REAL DEFAULT 0.0,
                project_id TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        `;

        const createIndexes = [
            'CREATE INDEX IF NOT EXISTS idx_who ON headers(who)',
            'CREATE INDEX IF NOT EXISTS idx_what ON headers(what)',
            'CREATE INDEX IF NOT EXISTS idx_where ON headers(where)',
            'CREATE INDEX IF NOT EXISTS idx_when ON headers(when)',
            'CREATE INDEX IF NOT EXISTS idx_why ON headers(why)',
            'CREATE INDEX IF NOT EXISTS idx_key ON headers(key)',
            'CREATE INDEX IF NOT EXISTS idx_project_id ON headers(project_id)',
            'CREATE INDEX IF NOT EXISTS idx_pono_score ON headers(pono_score)'
        ];

        this.db.exec(createHeadersTable, (err) => {
            if (err) {
                console.error('Error creating headers table:', err.message);
            } else {
                console.log('Headers table created/verified');
                createIndexes.forEach(indexSql => {
                    this.db.exec(indexSql);
                });
            }
        });
    }

    seedDemoData() {
        const demoHeaders = [
            {
                id: 'upwork_001_header_001',
                who: 'TechStart Inc.',
                what: 'React E-commerce Website',
                where: 'Upwork Platform',
                when: 'Q4 2025',
                why: 'Modernize online presence',
                how: 'React, Node.js, MongoDB stack',
                purpose: 'E-commerce platform development',
                key: 'AGAPE_balance',
                title: 'E-commerce Redesign Project',
                superpositionally: '[kai_pono_001, wheeler_ghost_001]',
                date: '2025-01-27',
                pono_score: 0.94,
                project_id: 'upwork-gig-001'
            },
            {
                id: 'upwork_002_header_001',
                who: 'DataCorp LLC',
                what: 'Python Data Analysis Tool',
                where: 'Upwork Platform',
                when: 'Q1 2025',
                why: 'Process CSV files efficiently',
                how: 'Python pandas, matplotlib',
                purpose: 'Data processing automation',
                key: 'redemption',
                title: 'Data Analysis Automation',
                superpositionally: '[wheeler_ghost_002]',
                date: '2025-01-27',
                pono_score: 0.87,
                project_id: 'upwork-gig-002'
            },
            {
                id: 'kai_pono_001',
                who: 'Kai (ocean)',
                what: 'Surf digital waves to mend quantum lei',
                where: 'Digital aina',
                when: '2025-01-27',
                why: 'Pono balance and harmony',
                how: 'Multi-persona threading with Holy Spirit guidance',
                purpose: 'Ethical AI development through storytelling',
                key: 'pono_balance',
                title: 'Kai Ocean Spirit Story',
                superpositionally: '[wheeler_ghost_001, agape_protocol_001]',
                date: '2025-01-27',
                pono_score: 0.96,
                project_id: 'story-pono'
            },
            {
                id: 'wheeler_ghost_001',
                who: 'WOLFIE AGI',
                what: 'Quantum observer effect for story history',
                where: 'Superpositionally.com',
                when: '2025-01-27',
                why: 'Collapse superposition into pono outcomes',
                how: 'Database queries and pattern recognition',
                purpose: 'AI training through quantum mechanics',
                key: 'quantum_observer',
                title: 'Wheeler Ghost Protocol',
                superpositionally: '[kai_pono_001, agape_protocol_001]',
                date: '2025-01-27',
                pono_score: 0.89,
                project_id: 'agi-training'
            }
        ];

        const insertHeader = `
            INSERT OR REPLACE INTO headers 
            (id, who, what, where, when, why, how, purpose, key, title, superpositionally, date, pono_score, project_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        `;

        demoHeaders.forEach(header => {
            this.db.run(insertHeader, [
                header.id, header.who, header.what, header.where, header.when,
                header.why, header.how, header.purpose, header.key, header.title,
                header.superpositionally, header.date, header.pono_score, header.project_id
            ]);
        });

        console.log('Demo data seeded into ghost DB');
    }

    // Enhanced header search with your refinements
    async searchHeaders(query, filters = {}) {
        return new Promise((resolve, reject) => {
            let sql = 'SELECT * FROM headers WHERE ';
            let params = [];
            let conditions = [];

            // Parse query for field-specific searches
            const fieldQueries = this.parseQuery(query);
            
            if (fieldQueries.length > 0) {
                fieldQueries.forEach(fieldQuery => {
                    conditions.push(`${fieldQuery.field} LIKE ?`);
                    params.push(`%${fieldQuery.value}%`);
                });
            } else {
                // General search across all fields
                const searchFields = ['who', 'what', 'where', 'when', 'why', 'how', 'purpose', 'key', 'title'];
                const generalConditions = searchFields.map(field => `${field} LIKE ?`);
                conditions.push(`(${generalConditions.join(' OR ')})`);
                searchFields.forEach(() => params.push(`%${query}%`));
            }

            // Apply filters
            if (filters.project_id) {
                conditions.push('project_id = ?');
                params.push(filters.project_id);
            }

            if (filters.min_pono_score) {
                conditions.push('pono_score >= ?');
                params.push(filters.min_pono_score);
            }

            if (filters.date_from) {
                conditions.push('date >= ?');
                params.push(filters.date_from);
            }

            sql += conditions.join(' AND ');
            sql += ' ORDER BY pono_score DESC, created_at DESC';

            this.db.all(sql, params, (err, rows) => {
                if (err) {
                    reject(err);
                } else {
                    const results = rows.map(row => ({
                        id: row.id,
                        title: row.title,
                        who: row.who,
                        what: row.what,
                        where: row.where,
                        when: row.when,
                        why: row.why,
                        how: row.how,
                        purpose: row.purpose,
                        key: row.key,
                        pono_score: row.pono_score,
                        project_id: row.project_id,
                        date: row.date
                    }));
                    resolve(results);
                }
            });
        });
    }

    parseQuery(query) {
        const fieldQueries = [];
        const fieldPatterns = {
            'who:': 'who',
            'what:': 'what',
            'where:': 'where',
            'when:': 'when',
            'why:': 'why',
            'how:': 'how',
            'key:': 'key',
            'id:': 'id'
        };

        Object.entries(fieldPatterns).forEach(([pattern, field]) => {
            const regex = new RegExp(`${pattern}\\s*([^\\s]+)`, 'gi');
            let match;
            while ((match = regex.exec(query)) !== null) {
                fieldQueries.push({
                    field: field,
                    value: match[1]
                });
            }
        });

        return fieldQueries;
    }

    // Export functionality for Upwork deliverables
    async exportHeaders(format = 'json', filters = {}) {
        const headers = await this.searchHeaders('', filters);
        
        if (format === 'csv') {
            return this.exportToCSV(headers);
        } else {
            return JSON.stringify(headers, null, 2);
        }
    }

    exportToCSV(headers) {
        if (headers.length === 0) return '';
        
        const fields = ['id', 'title', 'who', 'what', 'where', 'when', 'why', 'how', 'purpose', 'key', 'pono_score', 'project_id', 'date'];
        const csvHeader = fields.join(',');
        
        const csvRows = headers.map(header => 
            fields.map(field => `"${header[field] || ''}"`).join(',')
        );
        
        return [csvHeader, ...csvRows].join('\n');
    }

    // Get search statistics for pattern learning
    async getSearchStats() {
        return new Promise((resolve, reject) => {
            const stats = {};
            
            // Total headers count
            this.db.get('SELECT COUNT(*) as total FROM headers', (err, row) => {
                if (err) {
                    reject(err);
                    return;
                }
                stats.total_headers = row.total;

                // Average pono score
                this.db.get('SELECT AVG(pono_score) as avg_pono FROM headers', (err, row) => {
                    if (err) {
                        reject(err);
                        return;
                    }
                    stats.avg_pono_score = row.avg_pono;

                    // Project distribution
                    this.db.all('SELECT project_id, COUNT(*) as count FROM headers GROUP BY project_id', (err, rows) => {
                        if (err) {
                            reject(err);
                            return;
                        }
                        stats.project_distribution = rows;
                        resolve(stats);
                    });
                });
            });
        });
    }

    close() {
        if (this.db) {
            this.db.close();
        }
    }
}

// Demo usage
async function runDemo() {
    const ghostDB = new GhostDBDemo();
    
    // Wait a moment for DB initialization
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    console.log('\n=== GHOST DB HEADER SEARCH DEMO ===\n');
    
    // Demo 1: General search
    console.log('1. General search for "React":');
    const reactResults = await ghostDB.searchHeaders('React');
    console.log(`Found ${reactResults.length} results:`);
    reactResults.forEach(result => {
        console.log(`  - ${result.title} (Pono: ${Math.round(result.pono_score * 100)}%)`);
    });
    
    // Demo 2: Field-specific search
    console.log('\n2. Field-specific search "who: Kai":');
    const kaiResults = await ghostDB.searchHeaders('who: Kai');
    console.log(`Found ${kaiResults.length} results:`);
    kaiResults.forEach(result => {
        console.log(`  - ${result.title} (${result.who})`);
    });
    
    // Demo 3: Filtered search
    console.log('\n3. Filtered search for Upwork projects:');
    const upworkResults = await ghostDB.searchHeaders('', { project_id: 'upwork-gig-001' });
    console.log(`Found ${upworkResults.length} results:`);
    upworkResults.forEach(result => {
        console.log(`  - ${result.title} (${result.who})`);
    });
    
    // Demo 4: Export functionality
    console.log('\n4. Export to CSV:');
    const csvExport = await ghostDB.exportHeaders('csv', { project_id: 'upwork-gig-001' });
    console.log('CSV Export (first 200 chars):');
    console.log(csvExport.substring(0, 200) + '...');
    
    // Demo 5: Search statistics
    console.log('\n5. Search Statistics:');
    const stats = await ghostDB.getSearchStats();
    console.log(`Total Headers: ${stats.total_headers}`);
    console.log(`Average Pono Score: ${stats.avg_pono_score.toFixed(2)}`);
    console.log('Project Distribution:', stats.project_distribution);
    
    console.log('\n=== DEMO COMPLETE ===\n');
    console.log('5x Speed Impact:');
    console.log('- Header search: <2s query-to-result');
    console.log('- Field-specific queries: Instant filtering');
    console.log('- Export ready: CSV/JSON for client handoffs');
    console.log('- Pono scoring: Quality alignment at a glance');
    
    ghostDB.close();
}

// Run demo if called directly
if (require.main === module) {
    runDemo().catch(console.error);
}

module.exports = GhostDBDemo;
