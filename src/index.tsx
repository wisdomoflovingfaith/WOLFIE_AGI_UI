import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import './styles.css';

// Modern React 18 root API
const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);

root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);