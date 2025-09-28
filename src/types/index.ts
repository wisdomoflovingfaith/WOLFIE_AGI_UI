// ID: [WOLFIE_AGI_UI_TYPES_20250923_001]
// SUPERPOSITIONALLY: [dream_data_analysis, quantum_tabs, multi_agent_coordination, bridge_crew_tracking, typescript_types, type_definitions, interface_definitions]
// DATE: 2025-09-23
// TITLE: types/index.ts — TypeScript Type Definitions
// WHO: WOLFIE (Eric) - Project Architect & Dream Architect
// WHAT: TypeScript type definitions for WOLFIE AGI UI project
// WHERE: C:\START\WOLFIE_AGI_UI\src\types\
// WHEN: 2025-09-23, 12:05 PM CDT (Sioux Falls Timezone)
// WHY: Provide type safety and IntelliSense support for TypeScript development
// HOW: TypeScript interfaces and type definitions for all data structures
// HELP: Contact WOLFIE for TypeScript type questions or interface issues
// AGAPE: Love, patience, kindness, humility in type system design

// Dream Entry Interface
export interface DreamEntry {
  EntryID: string;
  Date: string;
  Summary: string;
  Who: string;
  What: string;
  Where: string;
  When: string;
  Why: string;
  How: string;
  Symbols: string;
  Themes: string;
  AI_Connection: string;
  Emotional_Vibe: string;
  Tags: string;
  Cross_References: string;
  Quantum_State: string;
  DreamTimestamp: string;
}

// Bridge Crew Agent Interface
export interface BridgeCrew {
  AgentName: string;
  Status: string;
  LastContact: string;
  Specialization: string;
  CurrentTask: string;
  Availability: string;
  ContactMethod: string;
  Notes: string;
  UnderstandingScore: number;
  AlignmentScore: number;
}

// Web Node Message Interface
export interface WebNodeMessage {
  message_id: string;
  from_agent: string;
  to_agent: string;
  message_type: string;
  content: string;
  timestamp: string;
}

// Task Interface
export interface Task {
  task_id: string;
  task_type: string;
  description: string;
  assigned_to: string;
  priority: 'low' | 'medium' | 'high' | 'critical';
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  created_at: string;
  deadline: string;
}

// Dream Fragment Interface
export interface DreamFragment {
  fragment_id: string;
  device_id: string;
  summary: string;
  symbols: string;
  themes: string;
  emotional_vibe: string;
  ai_connection: string;
  tags: string;
  sync_status: 'pending' | 'processed' | 'failed';
  created_at: string;
  synced_at?: string;
}

// Mobile Device Interface
export interface MobileDevice {
  device_id: string;
  device_name: string;
  device_type: string;
  last_sync: string;
  sync_token: string;
  is_active: boolean;
  created_at: string;
}

// Convergence Assessment Interface
export interface ConvergenceAssessment {
  assessment_id: number;
  agent_id: string;
  understanding_score: number;
  alignment_score: number;
  divergence_level: number;
  assessment_timestamp: string;
  status: string;
}

// Convergence Intervention Interface
export interface ConvergenceIntervention {
  intervention_id: number;
  agent_id: string;
  intervention_type: 'critical' | 'high' | 'medium';
  md_file_path: string;
  intervention_timestamp: string;
  status: 'pending' | 'sent' | 'active' | 'completed';
  response_timestamp?: string;
  effectiveness_score?: number;
}

// API Response Interface
export interface ApiResponse<T> {
  data?: T;
  error?: string;
  status: 'success' | 'error';
  message?: string;
}

// Quantum Tab Interface
export interface QuantumTab {
  name: string;
  weight: number;
  active: boolean;
  color: string;
}

// Emotional Vibe Type
export type EmotionalVibe = 
  | 'excited' 
  | 'happy' 
  | 'content' 
  | 'neutral' 
  | 'concerned' 
  | 'worried' 
  | 'frustrated' 
  | 'angry' 
  | 'sad' 
  | 'confused';

// Priority Type
export type Priority = 'low' | 'medium' | 'high' | 'critical';

// Status Type
export type Status = 'active' | 'inactive' | 'processing' | 'offline' | 'standby';

// Message Type
export type MessageType = 'text' | 'dream' | 'task' | 'status' | 'alert' | 'notification';

// Device Type
export type DeviceType = 'mobile' | 'tablet' | 'laptop' | 'desktop' | 'other';

// Intervention Type
export type InterventionType = 'critical' | 'high' | 'medium';

// Sync Status Type
export type SyncStatus = 'pending' | 'processing' | 'completed' | 'failed';

// Chart Data Interface
export interface ChartData {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
    backgroundColor?: string | string[];
    borderColor?: string | string[];
    borderWidth?: number;
  }[];
}

// Timeline Data Interface
export interface TimelineData {
  x: string[];
  y: number[];
  text: string[];
  mode: 'markers' | 'lines' | 'markers+lines';
  name: string;
  marker?: {
    size: number;
    color: string | string[];
    line?: {
      width: number;
      color: string;
    };
  };
}

// Network Node Interface
export interface NetworkNode {
  id: string;
  label: string;
  group: string;
  size: number;
  color: string;
}

// Network Edge Interface
export interface NetworkEdge {
  from: string;
  to: string;
  weight: number;
  color: string;
}

// Configuration Interface
export interface Configuration {
  paths: {
    baseDir: string;
    musicDir: string;
    backupDir: string;
    logDir: string;
    databaseDir: string;
  };
  database: {
    databasePath: string;
    webNodesPath: string;
    backupInterval: number;
    maxBackups: number;
  };
  api: {
    mainAPIPort: number;
    webNodePort: number;
    storytellingPort: number;
    mobileSyncPort: number;
    host: string;
    debug: boolean;
    corsOrigins: string;
  };
  security: {
    encryptionEnabled: boolean;
    encryptionKeyFile: string;
    tokenExpiry: number;
    maxConnections: number;
    rateLimit: number;
  };
}

// Event Interface
export interface Event {
  id: string;
  type: string;
  timestamp: string;
  data: any;
  source: string;
}

// Filter Interface
export interface Filter {
  field: string;
  operator: 'equals' | 'contains' | 'startsWith' | 'endsWith' | 'greaterThan' | 'lessThan' | 'between';
  value: any;
  values?: any[];
}

// Sort Interface
export interface Sort {
  field: string;
  direction: 'asc' | 'desc';
}

// Pagination Interface
export interface Pagination {
  page: number;
  limit: number;
  total: number;
  totalPages: number;
}

// Search Interface
export interface Search {
  query: string;
  fields: string[];
  filters: Filter[];
  sort: Sort[];
  pagination: Pagination;
}

// Theme Interface
export interface Theme {
  name: string;
  colors: {
    primary: string;
    secondary: string;
    accent: string;
    background: string;
    surface: string;
    text: string;
    textSecondary: string;
  };
  fonts: {
    primary: string;
    secondary: string;
    mono: string;
  };
  spacing: {
    xs: string;
    sm: string;
    md: string;
    lg: string;
    xl: string;
  };
  borderRadius: {
    sm: string;
    md: string;
    lg: string;
  };
}

// Component Props Interfaces
export interface ButtonProps {
  children: React.ReactNode;
  onClick?: () => void;
  variant?: 'primary' | 'secondary' | 'danger' | 'success';
  size?: 'sm' | 'md' | 'lg';
  disabled?: boolean;
  loading?: boolean;
  className?: string;
}

export interface InputProps {
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url';
  disabled?: boolean;
  required?: boolean;
  className?: string;
}

export interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  title?: string;
  children: React.ReactNode;
  size?: 'sm' | 'md' | 'lg' | 'xl';
  className?: string;
}

export interface CardProps {
  children: React.ReactNode;
  title?: string;
  subtitle?: string;
  actions?: React.ReactNode;
  className?: string;
}

// Hook Return Types
export interface UseApiReturn<T> {
  data: T | null;
  loading: boolean;
  error: string | null;
  refetch: () => void;
}

export interface UseLocalStorageReturn<T> {
  value: T;
  setValue: (value: T) => void;
  removeValue: () => void;
}

export interface UseWebSocketReturn {
  socket: WebSocket | null;
  connected: boolean;
  send: (message: any) => void;
  lastMessage: any;
}

// Utility Types
export type Optional<T, K extends keyof T> = Omit<T, K> & Partial<Pick<T, K>>;
export type Required<T, K extends keyof T> = T & { [P in K]-?: T[P] };
export type Partial<T, K extends keyof T> = Omit<T, K> & Partial<Pick<T, K>>;
export type DeepPartial<T> = {
  [P in keyof T]?: T[P] extends object ? DeepPartial<T[P]> : T[P];
};
