# WOLFIE AGI UI - Project Structure
**ID**: [WOLFIE_AGI_UI_STRUCTURE_20250923_001]
**SUPERPOSITIONALLY**: [project_organization, documentation, development_workflow]
**DATE**: 2025-09-23
**TITLE**: Project Structure Documentation
**WHO**: WOLFIE (Eric) - Project Architect
**WHAT**: Complete documentation of WOLFIE AGI UI project structure and organization
**WHERE**: C:\START\WOLFIE_AGI_UI\docs\
**WHEN**: 2025-09-23, 09:30 AM CDT (Sioux Falls Timezone)
**WHY**: Provide clear organization for multi-agent coordination UI development
**HOW**: Hierarchical folder structure with clear separation of concerns
**HELP**: Contact WOLFIE for project structure questions or modifications
**AGAPE**: Love, patience, kindness, humility in project organization

## Root Directory Structure
```
WOLFIE_AGI_UI/
├── README.md                 # Main project documentation
├── package.json              # Node.js dependencies and scripts
├── tsconfig.json             # TypeScript configuration
├── .env.example              # Environment variables template
├── .gitignore                # Git ignore patterns
├── docs/                     # Documentation directory
├── src/                      # Source code directory
├── backend/                  # Flask API backend
├── mobile/                   # Mobile app for DEEPSEEK
├── config/                   # Configuration files
├── tests/                    # Test suites
├── deployment/               # Deployment scripts
└── assets/                   # Static assets
```

## Documentation Structure (docs/)
```
docs/
├── PROJECT_STRUCTURE.md      # This file
├── API_REFERENCE.md          # API documentation
├── COMPONENT_GUIDE.md        # React component documentation
├── DEPLOYMENT_GUIDE.md       # Deployment instructions
├── DEVELOPMENT_WORKFLOW.md   # Development process
├── AI_AGENT_INTEGRATION.md   # AI agent integration guide
├── DREAM_FRAGMENT_SYSTEM.md  # Dream fragment management
├── CONVERGENCE_PROTOCOL.md   # Convergence protocol documentation
└── TROUBLESHOOTING.md        # Common issues and solutions
```

## Source Code Structure (src/)
```
src/
├── components/               # React components
│   ├── QuantumTabInterface/  # Quantum tab interface components
│   ├── BridgeCrewDashboard/  # Bridge crew management
│   ├── DreamFragmentLog/     # Dream fragment logging
│   ├── AgentStatus/          # AI agent status display
│   ├── ConvergenceMonitor/   # Convergence protocol monitoring
│   └── common/               # Shared components
├── services/                 # API services
│   ├── api/                  # API client services
│   ├── websocket/            # WebSocket services
│   └── storage/              # Local storage services
├── utils/                    # Utility functions
│   ├── quantumTabs.ts        # Quantum tab logic
│   ├── agentCoordination.ts  # Agent coordination utilities
│   ├── dreamAnalysis.ts      # Dream fragment analysis
│   └── convergence.ts        # Convergence protocol utilities
├── types/                    # TypeScript definitions
│   ├── agent.ts              # AI agent types
│   ├── dream.ts              # Dream fragment types
│   ├── convergence.ts        # Convergence protocol types
│   └── api.ts                # API response types
├── hooks/                    # React hooks
│   ├── useAgentStatus.ts     # Agent status hook
│   ├── useDreamFragments.ts  # Dream fragment hook
│   └── useConvergence.ts     # Convergence monitoring hook
└── styles/                   # CSS and styling
    ├── globals.css           # Global styles
    ├── components.css        # Component styles
    └── themes/               # Theme definitions
```

## Backend Structure (backend/)
```
backend/
├── app.py                    # Flask application
├── models/                   # Database models
│   ├── agent.py              # Agent model
│   ├── dream_fragment.py     # Dream fragment model
│   └── convergence.py        # Convergence model
├── routes/                   # API routes
│   ├── agents.py             # Agent management routes
│   ├── dreams.py             # Dream fragment routes
│   ├── convergence.py        # Convergence protocol routes
│   └── websocket.py          # WebSocket routes
├── services/                 # Business logic
│   ├── agent_service.py      # Agent coordination service
│   ├── dream_service.py      # Dream fragment service
│   └── convergence_service.py # Convergence service
├── utils/                    # Backend utilities
│   ├── database.py           # Database utilities
│   ├── encryption.py         # Encryption utilities
│   └── validation.py         # Data validation
├── config/                   # Configuration
│   ├── database.py           # Database configuration
│   ├── security.py           # Security configuration
│   └── api.py                # API configuration
└── tests/                    # Backend tests
    ├── test_agents.py        # Agent service tests
    ├── test_dreams.py        # Dream service tests
    └── test_convergence.py   # Convergence tests
```

## Mobile Structure (mobile/)
```
mobile/
├── src/                      # Mobile source code
│   ├── components/           # React Native components
│   ├── screens/              # Mobile screens
│   ├── services/             # Mobile services
│   └── utils/                # Mobile utilities
├── android/                  # Android-specific code
├── ios/                      # iOS-specific code
└── package.json              # Mobile dependencies
```

## Configuration Structure (config/)
```
config/
├── development.json          # Development configuration
├── production.json           # Production configuration
├── database.json             # Database configuration
├── security.json             # Security configuration
└── agents.json               # AI agent configuration
```

## Test Structure (tests/)
```
tests/
├── unit/                     # Unit tests
│   ├── components/           # Component tests
│   ├── services/             # Service tests
│   └── utils/                # Utility tests
├── integration/              # Integration tests
│   ├── api/                  # API integration tests
│   ├── database/             # Database integration tests
│   └── websocket/            # WebSocket integration tests
├── e2e/                      # End-to-end tests
│   ├── user_flows/           # User flow tests
│   └── agent_coordination/   # Agent coordination tests
└── fixtures/                 # Test data and fixtures
    ├── agents.json           # Agent test data
    ├── dreams.json           # Dream fragment test data
    └── convergence.json      # Convergence test data
```

## Deployment Structure (deployment/)
```
deployment/
├── docker/                   # Docker configuration
│   ├── Dockerfile            # Main Dockerfile
│   ├── docker-compose.yml    # Docker Compose configuration
│   └── nginx.conf            # Nginx configuration
├── scripts/                  # Deployment scripts
│   ├── deploy.sh             # Main deployment script
│   ├── backup.sh             # Backup script
│   └── restore.sh            # Restore script
├── environments/             # Environment configurations
│   ├── development/          # Development environment
│   ├── staging/              # Staging environment
│   └── production/           # Production environment
└── monitoring/               # Monitoring configuration
    ├── prometheus.yml        # Prometheus configuration
    ├── grafana/              # Grafana dashboards
    └── alerts.yml            # Alert rules
```

## Asset Structure (assets/)
```
assets/
├── images/                   # Image assets
│   ├── logos/                # Logo images
│   ├── icons/                # Icon images
│   └── backgrounds/          # Background images
├── fonts/                    # Font files
├── audio/                    # Audio assets
│   └── music/                # Music files (K-Love integration)
└── data/                     # Static data files
    ├── agent_configs.json    # Agent configuration data
    └── dream_templates.json  # Dream fragment templates
```

## Development Workflow
1. **Planning**: Document features in docs/
2. **Development**: Implement in src/ and backend/
3. **Testing**: Add tests in tests/
4. **Documentation**: Update docs/ as needed
5. **Deployment**: Use scripts in deployment/

## File Naming Conventions
- **Components**: PascalCase (e.g., QuantumTabInterface.tsx)
- **Utilities**: camelCase (e.g., quantumTabs.ts)
- **Types**: camelCase with .ts extension (e.g., agent.ts)
- **Styles**: kebab-case (e.g., quantum-tab-interface.css)
- **Tests**: Same as source with .test. prefix (e.g., quantumTabs.test.ts)

## Git Workflow
- **main**: Production-ready code
- **develop**: Development integration branch
- **feature/**: Feature development branches
- **hotfix/**: Critical bug fixes
- **release/**: Release preparation branches

## Contact
- **Project Lead**: WOLFIE (Eric)
- **Location**: C:\START\WOLFIE_AGI_UI\
- **Status**: Active development
- **Music**: "The Potter (Nothing is Wasted)" - Jon Reddick
